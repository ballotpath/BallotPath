-----------------------------------------------------------
-- Bulk import procedure for offices intended to be      --
-- called by bp_import_off_pos_hol_csv_to_staging_tables --
--                                                       --
-- Validates and imports offices residing in the         --
-- office_staging table.							     --
--                                                       --
-- On import this procedure does validation and error    --
-- reporting. Any errors are saved to an error table,    --
-- once import has been processed errors will be saved   --
-- to a csv that can be found in /tmp/import/errors/     --
--                                                       --
-- Potential improvements (at least on readability) make --
-- inserts into bad_inserts_offices another procedure    --
-- call with message and the row of data to insert.      --
--                                                       --
-- Authored by: Shawn Forgie                             --
-- For: BallotPath                                       --
-- Date: July 8, 2014                                    --
-----------------------------------------------------------

CREATE OR REPLACE FUNCTION bp_insert_offices()
  RETURNS void AS 
$BODY$
  DECLARE 
    offices CURSOR FOR SELECT * FROM office_staging;
    o_id integer;
    p_id integer;
    tmp integer;

BEGIN

FOR office IN offices LOOP

  SELECT op.id into p_id FROM office_position op WHERE op.position_name = office.position_name and op.district_id = office.district_id;

  IF (p_id IS NULL) THEN
    -- position does not exist
    -- Make bad_inserts into another procedure?
    INSERT into bad_inserts_offices (title
			  , num_positions
			  , responsibilities
			  , term_length_months
			  , filing_fee
			  , partisan
			  , age_reqs
			  , residency_reqs
			  , professional_reqs
			  , salary
			  , office_notes
			  , office_rank
			  , office_doc_name
			  , office_doc_link
			  , position_name
			  , district_name
			  , district_state
			  , election_div_name
			  , message)
		VALUES (office.title
			, office.num_positions
			, office.responsibilities
			, office.term_length_months
			, office.filing_fee
			, office.partisan
			, office.age_requirements
			, office.res_requirements
			, office.prof_requirements
			, office.salary
			, office.office_notes
			, office.office_rank
			, office.office_doc_name
			, office.office_doc_link
			, office.position_name
			, office.district_name
			, office.district_state
			, office.election_div_name
			, 'Office Position does not exist cannot insert office without an existing position!');

  ELSEIF ((SELECT op.office_id FROM office_position op where op.id = p_id) IS NULL) THEN
    --ADD offices
    with o_id as (INSERT into office (title
					, num_positions
					, responsibilities
					, term_length_months
					, filing_fee
					, partisan
					, age_requirements
					, res_requirements
					, prof_requirements
					, salary
					, notes
					, office_rank)
			VALUES (office.title
				, office.num_positions
				, office.responsibilities
				, office.term_length_months
				, office.filing_fee
				, office.partisan
				, office.age_requirements
				, office.res_requirements
				, office.prof_requirements
				, office.salary
				, office.office_notes
				, office.office_rank)
		RETURNING id)



      UPDATE office_position 
          SET office_id = (SELECT * FROM o_id LIMIT 1)
            WHERE id = p_id;
            
	 --Add Office docs
	 SELECT bp_insert_office_docs(office.office_doc_name, office.office_doc_link, o_id, office.district_id) into tmp;
	
  ELSE
    INSERT into bad_inserts_offices (title
			  , num_positions
			  , responsibilities
			  , term_length_months
			  , filing_fee
			  , partisan
			  , age_reqs
			  , residency_reqs
			  , professional_reqs
			  , salary
			  , office_notes
			  , office_rank
			  , office_doc_name
			  , office_doc_link
			  , position_name
			  , district_name
			  , district_state
			  , election_div_name
			  , message)
		VALUES (office.title
			, office.num_positions
			, office.responsibilities
			, office.term_length_months
			, office.filing_fee
			, office.partisan
			, office.age_requirements
			, office.res_requirements
			, office.prof_requirements
			, office.salary
			, office.office_notes
			, office.office_rank
			, office.office_doc_name
			, office.office_doc_link
			, office.position_name
			, office.district_name
			, office.district_state
			, office.election_div_name
			, 'Updates are not allowed in bulk inserts!');
  END IF;

END LOOP;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;