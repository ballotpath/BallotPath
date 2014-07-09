-- Function: bp_insert_office_docs(character varying, text, integer, integer)

-- DROP FUNCTION bp_insert_office_docs(character varying, text, integer, integer);

CREATE OR REPLACE FUNCTION bp_insert_office_docs(doc_name character varying, doc_link text, o_id integer, d_id integer)
  RETURNS integer AS
$BODY$
BEGIN
  IF (doc_name <> '' or doc_link <> '') THEN
	INSERT into office_docs (name, link, office_id )
	    VALUES (NULLIF(doc_name, ''), NULLIF(doc_link, ''), o_id);
  END IF;

RETURN 0;
 EXCEPTION
  WHEN not_null_violation THEN
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
RETURN 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION bp_insert_office_docs(character varying, text, integer, integer)
  OWNER TO postgres;
