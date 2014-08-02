--------------------------------------------------------------
-- Bulk import procedure for office holders intended to     --
-- be called by bp_import_off_pos_hol_csv_to_staging_tables --
--                                                          --
-- Validates and imports office holders for a specified     --
-- office                                                   --
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

CREATE OR REPLACE FUNCTION bp_insert_office_holders()
  RETURNS void AS 
$BODY$
  DECLARE 
    holders CURSOR FOR SELECT * FROM holder_staging;
    h_id integer;
    p_id integer;

BEGIN

--Add office holders
FOR holder IN holders LOOP
  -- Get the position id this holder will be associated with
  SELECT op.id into p_id FROM office_position op WHERE op.position_name = holder.position_name and op.district_id = holder.district_id;
  
  IF (p_id IS NULL) THEN
  -- position does not exist or potentially district could not exist either way cannot insert
  INSERT into bad_inserts_offices (first_name
                        , middle_name
                        , last_name
                        , holder_addr1
                        , holder_addr2
                        , holder_city
                        , holder_state
                        , holder_zip
                        , holder_phone
                        , holder_email
                        , holder_website
                        , photo_link
                        , position_name
                        , district_name
                        , district_state
                        , election_div_name
                        , message)
            VALUES (holder.first_name
                   , holder.middle_name
                   , holder.last_name
                   , holder.holder_addr1
                   , holder.holder_addr2
                   , holder.holder_city
                   , holder.holder_state
                   , holder.holder_zip
                   , holder.holder_phone
                   , holder.holder_email
                   , holder.holder_website
                   , holder.photo_link
                   , holder.position_name
                   , holder.district_name
                   , holder.district_state
                   , holder.election_div_name
                   , 'Office Position does not exist cannot insert holder without an existing position!');
         
  -- Verify the position is not already linked to an office holder
  -- We are not updating just inserting new values             
  ELSEIF ((SELECT op.office_holder_id FROM office_position op where op.id = p_id) IS NULL) THEN

     with h_id as (INSERT into office_holder (first_name
                                             , middle_name
                                             , last_name
                                             , address1
                                             , address2
                                             , city
                                             , state
                                             , zip
                                             , phone
                                             , email_address
                                             , website
                                             , photo_link)
                      VALUES( holder.first_name
                        , holder.middle_name
                        , holder.last_name
                        , holder.holder_addr1
                        , holder.holder_addr2
                        , holder.holder_city
                        , holder.holder_state
                        , holder.holder_zip
                        , holder.holder_phone
                        , holder.holder_email
                        , holder.holder_website
                        , holder.photo_link)
                      RETURNING id)

      -- Link the new holder to the existing position
      UPDATE office_position 
          SET office_holder_id = (SELECT * FROM h_id LIMIT 1)
            WHERE id = p_id;
  ELSE
  INSERT into bad_inserts_offices(first_name
                        , middle_name
                        , last_name
                        , holder_addr1
                        , holder_addr2
                        , holder_city
                        , holder_state
                        , holder_zip
                        , holder_phone
                        , holder_email
                        , holder_website
                        , photo_link
                        , position_name
                        , district_name
                        , district_state
                        , election_div_name
                        , message)
            VALUES (holder.first_name
                   , holder.middle_name
                   , holder.last_name
                   , holder.holder_addr1
                   , holder.holder_addr2
                   , holder.holder_city
                   , holder.holder_state
                   , holder.holder_zip
                   , holder.holder_phone
                   , holder.holder_email
                   , holder.holder_website
                   , holder.photo_link
                   , holder.position_name
                   , holder.district_name
                   , holder.district_state
                   , holder.election_div_name
                   , 'Updates are not allowed in bulk inserts!');
  END IF;

END LOOP;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;