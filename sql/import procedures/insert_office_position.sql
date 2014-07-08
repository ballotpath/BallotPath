CREATE OR REPLACE FUNCTION bp_insert_office_positions()
  RETURNS void AS 
$BODY$
BEGIN

  --TODO: Save duplicates to error table 
  --Add Office positions
  INSERT into office_position ( district_id
                              , position_name
                              , term_start
                              , term_end
                              , filing_deadline
                              , next_election
                              , notes
                              , office_rank)
      ((SELECT ps.district_id
              , ps.position_name
              , ps.term_start
              , ps.term_end
              , ps.filing_deadline
              , ps.next_election
              , ps.notes
              , ps.position_rank
      FROM position_staging ps
      WHERE ps.title <> '' and ps.district_id IS NOT NUll)

          EXCEPT

          (SELECT ps.district_id
                  , ps.position_name
                  , ps.term_start
                  , ps.term_end
                  , ps.filing_deadline
                  , ps.next_election
                  , ps.notes
                  , ps.position_rank
            FROM position_staging ps inner JOIN office_position op
                      on ps.district_id = op.district_id
                      and ps.position_name = op.position_name));


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
  LANGUAGE plpgsql VOLATILE;