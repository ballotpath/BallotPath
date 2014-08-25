-----------------------------------------------------------
-- Bulk import procedure for offices intended to be      --
-- called by bp_insert_offices                           --
--                                                       --
-- Validates and imports office docs for a specified     --
-- office                                                --
--                                                       --
-- On import this procedure does validation and error    --
-- reporting. Any errors are saved to an error table,    --
-- once import has been processed errors will be saved   --
-- to a csv that can be found in /tmp/import/errors/     --
--                                                       --
-- Potential improvements pass the actual row of data    --
-- or work functionality back into bp_insert_offices     --
--                                                       --
-- Authored by: Shawn Forgie                             --
-- For: BallotPath                                       --
-- Date: July 8, 2014                                    --
-----------------------------------------------------------

CREATE OR REPLACE FUNCTION bp_insert_office_docs(doc_name character varying, doc_link text, o_id integer, d_id integer)
  RETURNS integer AS
$BODY$
BEGIN
	-- link office docs
	IF (doc_name <> '' or doc_link <> '') THEN
		IF(doc_name <> '' and doc_link <> '') THEN
		    -- do not reinsert documents
		    IF NOT EXISTS (SELECT * FROM office_docs WHERE name = doc_name and link = doc_link and office_id = o_id) THEN
			INSERT into office_docs (name, link, office_id)
			VALUES (doc_name, doc_link, o_id);
		    ELSE
			    INSERT into bad_inserts_offices (title
						    , office_doc_name
						    , office_doc_link
						    , district_name
						    , district_state
						    , election_div_name
						    , message)
				VALUES ((SELECT title FROM office WHERE id = o_id)
					, doc_name
					, doc_link
					, (SELECT name FROM district WHERE id = d_id)
					, (SELECT state FROM district WHERE id = d_id)
					, (SELECT ed.name FROM election_div ed JOIN district d on ed.id = d.election_div_id
						WHERE d.id = d_id)
					, 'Duplicate office document detected!');
		    END IF;
		ELSE
		    INSERT into bad_inserts_offices (title
					    , office_doc_name
					    , office_doc_link
					    , district_name
					    , district_state
					    , election_div_name
					    , message)
			VALUES ((SELECT title FROM office WHERE id = o_id)
				, doc_name
				, doc_link
				, (SELECT name FROM district WHERE id = d_id)
				, (SELECT state FROM district WHERE id = d_id)
				, (SELECT ed.name FROM election_div ed JOIN district d on ed.id = d.election_div_id
					WHERE d.id = d_id)
				, 'Encountered empty value in office document fields!');
		END IF;

	END IF;	
RETURN 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;