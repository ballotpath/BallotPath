CREATE OR REPLACE FUNCTION bp_import_dist_elec_div_csv_to_staging_tables(filename character varying)
  RETURNS character varying AS
$BODY$
  DECLARE
    input_file character varying := format(E'/tmp/import/%s', filename);
    output_file character varying := format(E'/tmp/import/errors/bad_inserts_%s.csv', NOW());
    districts CURSOR FOR SELECT * FROM bulk_staging_districts;
    tmp integer;
    ed_id integer := NULL;
  BEGIN

  CREATE TEMPORARY TABLE bulk_staging_districts (
	  election_div_name character varying(50),
	  phys_addr1 character varying(25),
	  phys_addr2 character varying(25),
	  phys_addr_city character varying(25),
	  phys_addr_state character(2),
	  phys_addr_zip character(5),
	  mail_addr1 character varying(25),
	  mail_addr2 character varying(25),
	  mail_addr_city character varying(25),
	  mail_addr_state character(2),
	  mail_addr_zip character(5),
	  election_div_phone character varying(15),
	  fax character varying(15),
	  election_div_website text,
	  election_div_doc_name character varying(35),
	  election_div_doc_link text,
	  district_state character(2),
	  district_name character varying(50),
	  level_name character varying(12),
	  bad_insert_flag bit default B'0',					-- 0 := good, 1 := bad
	  message text
  )ON COMMIT DROP;

  EXECUTE format('
  	Copy bulk_staging_districts ( district_name
					, district_state
					, level_name
					, election_div_name
					, phys_addr1
					, phys_addr2
					, phys_addr_city
					, phys_addr_state
					, phys_addr_zip
					, mail_addr1
					, mail_addr2
					, mail_addr_city
					, mail_addr_state
					, mail_addr_zip
					, election_div_phone
					, fax
					, election_div_website
					, election_div_doc_name
					, election_div_doc_link)
  FROM %L
  WITH
    DELIMITER ''|''
    NULL ''''
    CSV HEADER', input_file);

UPDATE bulk_staging_districts
	SET bad_insert_flag = B'1'
		, message = 'Expected non-empty string in district_state, district_name and election_div_name!'
	WHERE district_name = ''
		  OR district_state = ''
		  OR election_div_name = '';

UPDATE bulk_staging_districts
	SET bad_insert_flag = B'1'
		, message = 'District already exists!'
	FROM district d, election_div ed
	WHERE district_name = d.name
		  and district_state = d.state
		  and d.election_div_id = (SELECT e.id FROM election_div e WHERE e.name = election_div_name and e.phys_addr_state = d.state);


FOR dist IN districts LOOP

	IF (dist.bad_insert_flag <> B'1') THEN
		--Retrieve existing election_div_id if it exists
		SELECT ed.id into ed_id FROM election_div ed WHERE ed.name = dist.election_div_name and ed.phys_addr_state = dist.phys_addr_state;

		IF (ed_id IS NULL) THEN
			with tmp as (INSERT into election_div (name
								  , phys_addr_addr1
								  , phys_addr_addr2
								  , phys_addr_city
								  , phys_addr_state
								  , phys_addr_zip
								  , mail_addr_addr1
								  , mail_addr_addr2
								  , mail_addr_city
								  , mail_addr_state
								  , mail_addr_zip
								  , phone
								  , fax
								  , website
								  , doc_name
								  , doc_link)
							VALUES( dist.election_div_name
								  , dist.phys_addr1
								  , dist.phys_addr2
								  , dist.phys_addr_city
								  , dist.phys_addr_state
								  , dist.phys_addr_zip
								  , dist.mail_addr1
								  , dist.mail_addr2
								  , dist.mail_addr_city
								  , dist.mail_addr_state
								  , dist.mail_addr_zip
								  , dist.election_div_phone
								  , dist.fax
								  , dist.election_div_website
								  , dist.election_div_doc_name
								  , dist.election_div_doc_link)
								  RETURNING id)
			SELECT * into ed_id FROM tmp LIMIT 1;
		END IF;

  		-- link districts
  		INSERT into district (state
				  , name
				  , level_id
				  , election_div_id)
  			VALUES (dist.district_state
  					, dist.district_name
  					, (SELECT l.id FROM level l WHERE l.name = dist.level_name)
  					, ed_id);

  		-- link election div docs
		IF (dist.election_div_doc_name <> '' or dist.election_div_doc_link <> '') THEN
			IF(dist.doc_name <> '' and dist.doc_link <> '') THEN
				INSERT into election_div_docs (name, link, election_div_id)
	    			VALUES (doc_name, doc_link, ed_id);
	    		ELSE
				UPDATE bulk_staging_districts
					SET bad_insert_flag = B'1'
						, message = 'Encountered empty value in office document fields!'
				WHERE CURRENT OF district;
			END IF;

  		END IF;		
	END IF;
END LOOP;

  IF((SELECT COUNT(*) FROM bulk_staging_districts WHERE bad_insert_flag = B'1') > 0) THEN
  	EXECUTE format('
  		COPY (SELECT * FROM bulk_staging_districts WHERE bad_insert_flag = B''1'')
  		TO %L
  		WITH
		    DELIMITER ''|''
		    NULL ''''
		    CSV HEADER', output_file);
  END IF;

RETURN output_file;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;