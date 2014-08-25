--------------------------------------------------------------
-- Bulk import procedure for office positions intended to   --
-- be called by bp_import_off_pos_hol_csv_to_staging_tables --
--                                                          --
-- Validates and imports office positions found in the      --
-- positions_staging table                                  --
--                                                          --
-- On import this procedure does validation and error       --
-- reporting. Any errors are saved to an error table,       --
-- once import has been processed errors will be saved      --
-- to a csv that can be found in /tmp/import/errors/        --
--                                                          --
-- Potential improvements make bad_inserts into an external --
-- procedure.                                               --
--                                                          --
-- Authored by: Shawn Forgie                                --
-- For: BallotPath                                          --
-- Date: July 8, 2014                                       --
--------------------------------------------------------------

CREATE OR REPLACE FUNCTION bp_insert_office_positions()
  RETURNS void AS
$BODY$
  DECLARE 
    positions CURSOR FOR (SELECT position_name, title, district_id, district_state, district_name, election_div_name
        FROM position_staging
        GROUP by position_name, title, district_id, district_state, district_name, election_div_name
        EXCEPT

        SELECT op.position_name, o.title, d.id, d.state, d.name, ed.name
        FROM office_position op join office o on op.office_id = o.id
          join district d on op.district_id = d.id
          join election_div ed on d.election_div_id = ed.id);
BEGIN

FOR position IN positions LOOP

  INSERT into office_position ( district_id
                              , position_name
                              , term_start
                              , term_end
                              , filing_deadline
                              , next_election
                              , notes
                              , office_rank)
      (SELECT  ps.district_id
              , ps.position_name
              , ps.term_start
              , ps.term_end
              , ps.filing_deadline
              , ps.next_election
              , ps.notes
              , ps.position_rank
      FROM position_staging ps
      WHERE ps.title <> '' and ps.district_id IS NOT NUll
    and ps.position_name = position.position_name
    and ps.title = position.title
    and ps.district_id = position.district_id
    and ps.district_name = position.district_name
    and ps.election_div_name = position.election_div_name
     ORDER by ps.term_start, ps.term_end, ps.filing_deadline, ps.next_election, ps.notes, ps.position_rank
     LIMIT 1);

END LOOP;


INSERT into bad_inserts_offices(position_name
                              , term_start
                              , term_end
                              , filing_deadline
                              , next_election
                              , position_notes
                              , position_rank
                              , district_name
                              , district_state
                              , election_div_name
                              , message)
  (SELECT ps.position_name
          , ps.term_start
          , ps.term_end
          , ps.filing_deadline
          , ps.next_election
          , ps.notes
          , ps.position_rank
          , ps.district_name
          , ps.district_state
          , ps.election_div_name
          , 'Office Position must have a related Office!'
      FROM position_staging ps
      WHERE ps.title = '');



  INSERT into bad_inserts_offices(position_name
                              , term_start
                              , term_end
                              , filing_deadline
                              , next_election
                              , position_notes
                              , position_rank
                              , district_name
                              , district_state
                              , election_div_name
                              , message)
  (SELECT ps.position_name
          , ps.term_start
          , ps.term_end
          , ps.filing_deadline
          , ps.next_election
          , ps.notes
          , ps.position_rank
          , ps.district_name
          , ps.district_state
          , ps.election_div_name
          , 'Office Position cannot be inserted without an existing district and election division!'
      FROM position_staging ps
      WHERE ps.district_id ISNULL);


END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;