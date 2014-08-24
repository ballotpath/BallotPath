----------------------------------------------------------
-- Bulk csv import procedure for office holders,        --
-- office positions and office                          --
--                                                      --
-- Imports a template csv delimited by '|' located      --
-- in /tmp/import/ and identified as a parameter passed --
-- in by the caller                                     --
--                                                      --
-- On import this procedure does validation and error   --
-- reporting. Any errors are flagged and written to a   --
-- csv located in /tmp/import/errors/ the resulting     --
-- filename is returned to the caller.                  --
--                                                      --
-- Authored by: Shawn Forgie                            --
-- For: BallotPath                                      --
-- Date: July 8, 2014                                   --
----------------------------------------------------------

CREATE OR REPLACE FUNCTION bp_import_off_pos_hol_csv_to_staging_tables(filename character varying)
  RETURNS character varying AS
$BODY$
  DECLARE
    input_file character varying := format(E'/tmp/import/%s', filename);
    outname character varying := format(E'bad_inserts_%s.csv', (SELECT * FROM to_char(current_timestamp, 'YYYY-MM-DD-HH24:MI:SS')));
    output_file character varying := format(E'/tmp/import/errors/%s', outname);
  BEGIN

  CREATE TEMPORARY TABLE bulk_staging (
    first_name character varying(25),
    middle_name character varying(25),
    last_name character varying(25),
    holder_addr1 character varying(100),
    holder_addr2 character varying(100),
    holder_city character varying(35),
    holder_state character(2),
    holder_zip character(5),
    holder_phone character varying(15),
    holder_email text,
    holder_website text,
    photo_link text,
    position_name character varying(125),
    term_start date,
    term_end date,
    filing_deadline date,
    next_election date,
    position_notes text,
    position_rank integer,
    title character varying(125),
    num_positions integer,
    responsibilities text,
    term_length_months integer,
    filing_fee text,
    partisan boolean,
    age_reqs character varying(100),
    residency_reqs text,
    professional_reqs text,
    salary numeric,
    office_notes text,
    office_rank integer,
    office_doc_name character varying(125),
    office_doc_link text,
    district_state character(2),
    district_name character varying(125),
    election_div_name character varying(125)
  ) ON COMMIT DROP;


  CREATE TEMPORARY TABLE position_staging (
  position_name character varying(125),
  term_start date,
  term_end date,
  filing_deadline date,
  next_election date,
  notes text,
  position_rank integer,
  title character varying(125),
  district_id integer,
  district_state character(2),
  district_name character varying(125),
  election_div_name character varying(125)
)ON COMMIT DROP;

CREATE TEMPORARY TABLE holder_staging (
  first_name character varying(25),
  middle_name character varying(25),
  last_name character varying(25),
  holder_addr1 character varying(100),
  holder_addr2 character varying(100),
  holder_city character varying(35),
  holder_state character(2),
  holder_zip character(5),
  holder_phone character varying(15),
  holder_email text,
  holder_website text,
  photo_link text,
  district_id integer,
  position_name character varying(125),
  district_state character(2),
  district_name character varying(125),
  election_div_name character varying(125)
)ON COMMIT DROP;


CREATE TEMPORARY TABLE office_staging (
    title character varying(125),
    num_positions integer,
    responsibilities text,
    term_length_months integer,
    filing_fee text,
    partisan boolean,
    age_requirements character varying(100),
    res_requirements text,
    prof_requirements text,
    salary numeric,
    office_notes text,
    office_rank integer,
    office_doc_name character varying(125),
    office_doc_link text,
    position_name character varying(125),
    district_id integer,
    district_state character(2),
    district_name character varying(125),
    election_div_name character varying(125)
)ON COMMIT DROP;


  CREATE TEMPORARY TABLE bad_inserts_offices (
  first_name character varying(25),
  middle_name character varying(25),
  last_name character varying(25),
  holder_addr1 character varying(100),
  holder_addr2 character varying(100),
  holder_city character varying(35),
  holder_state character(2),
  holder_zip character(5),
  holder_phone character varying(15),
  holder_email text,
  holder_website text,
  photo_link text,
  position_name character varying(125),
  term_start date,
  term_end date,
  filing_deadline date,
  next_election date,
  position_notes text,
  position_rank integer,
  title character varying(125),
  num_positions integer,
  responsibilities text,
  term_length_months integer,
  filing_fee text,
  partisan boolean,
  age_reqs character varying(100),
  residency_reqs text,
  professional_reqs text,
  salary numeric,
  office_notes text,
  office_rank integer,
  office_doc_name character varying(125),
  office_doc_link text,
  district_state character(2),
  district_name character varying(125),
  election_div_name character varying(125),
  message text
  )ON COMMIT DROP;


    EXECUTE format('
  Copy bulk_staging ( first_name
    , middle_name
    , last_name
    , holder_addr1
    , holder_city
    , holder_state
    , holder_zip
    , holder_addr2
    , holder_phone
    , holder_email
    , holder_website
    , photo_link
    , position_name
    , term_start
    , term_end
    , filing_deadline
    , next_election
    , position_notes
    , position_rank
    , title
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
    , district_name
    , district_state
    , election_div_name)
  FROM %L
  WITH
    DELIMITER ''|''
    NULL ''''
    CSV HEADER', input_file);

-- Positions need to be inserted first
INSERT into position_staging (  position_name
        , term_start
        , term_end
        , filing_deadline
        , next_election
        , notes
        , position_rank
        , title
        , district_id
        , district_name
        , district_state
        , election_div_name)
    (SELECT   bs.position_name
    , bs.term_start
    , bs.term_end
    , bs.filing_deadline
    , bs.next_election
    , bs.position_notes
    , bs.position_rank
    , bs.title
    , d.id
    , bs.district_name
    , bs.district_state
    , bs.election_div_name
  FROM bulk_staging bs LEFT OUTER JOIN district d on bs.district_name = d.name and bs.district_state = d.state
    LEFT OUTER JOIN election_div ed on d.election_div_id = ed.id
  WHERE ed.name = bs.election_div_name);


PERFORM bp_insert_office_positions();


INSERT into holder_staging (  first_name
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
        , district_id
        , district_name
        , district_state
        , election_div_name)
    (SELECT   bs.first_name
    , bs.middle_name
    , bs.last_name
    , bs.holder_addr1
    , bs.holder_addr2
    , bs.holder_city
    , bs.holder_state
    , bs.holder_zip
    , bs.holder_phone
    , bs.holder_email
    , bs.holder_website
    , bs.photo_link  
    , bs.position_name
    , d.id
    , bs.district_name
    , bs.district_state
    , bs.election_div_name
  FROM bulk_staging bs LEFT OUTER JOIN district d on bs.district_name = d.name and bs.district_state = d.state
    LEFT OUTER JOIN election_div ed on d.election_div_id = ed.id
  WHERE ed.name = bs.election_div_name
  and bs.first_name IS NOT NULL 
  and bs.last_name IS NOT NULL);

PERFORM bp_insert_office_holders();

INSERT into office_staging (title
        , num_positions
        , responsibilities
        , term_length_months
        , filing_fee
        , partisan
        , age_requirements
        , res_requirements
        , prof_requirements
        , salary
        , office_notes
        , office_rank
        , office_doc_name
        , office_doc_link
        , position_name
        , district_id
        , district_name
        , district_state
        , election_div_name)
    (SELECT DISTINCT  bs.title
    , bs.num_positions
    , bs.responsibilities
    , bs.term_length_months
    , bs.filing_fee
    , bs.partisan
    , bs.age_reqs
    , bs.residency_reqs
    , bs.professional_reqs
    , bs.salary
    , bs.office_notes
    , bs.office_rank
    , bs.office_doc_name
    , bs.office_doc_link
    , bs.position_name
    , d.id
    , bs.district_name
    , bs.district_state
    , bs.election_div_name
  FROM bulk_staging bs LEFT OUTER JOIN district d on bs.district_name = d.name and bs.district_state = d.state
    LEFT OUTER JOIN election_div ed on d.election_div_id = ed.id
  WHERE ed.name = bs.election_div_name);


  PERFORM bp_insert_offices();

  -- Write errors out to csv
  IF((SELECT COUNT(*) FROM bad_inserts_offices) > 0) THEN
    EXECUTE format('
      COPY bad_inserts_offices
      TO %L
      WITH
        DELIMITER ''|''
        NULL ''--''
        CSV HEADER', output_file);
  RAISE NOTICE 'bad inserts detected';
  RETURN outname;
  END IF;

  RETURN '';
END;  
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;