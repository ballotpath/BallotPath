from app import app, db, models
from flask import Response, url_for
from sqlalchemy import text
import csv
import os

hhhol_pos_off_header = ['first_name', 'middle_name', 'last_name', 'holder_addr1', 'holder_addr1_city', 'holder_addr1_state', 'holder_addr1_zip', 'holder_addr2', 'holder_phone', 'holder_email', 'holder_website', 'photo_link', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'position_notes', 'position_rank', 'title', 'number_of_positions', 'responsibilities', 'term_length_months', 'filing_fee', 'partisan', 'age_reqs', 'residency_reqs', 'professional_reqs', 'salary', 'office_notes', 'office_rank', 'office_doc_name', 'office_doc_link', 'district_name', 'district_state', 'election_div_name']


def begin(filename):
    result = ''
    try:
        with open(os.path.join(app.config['UPLOAD_FOLDER'], filename)) as impfile:
            reader = csv.reader(impfile, delimiter='|', dialect=csv.excel)
            headers = reader.next()
    except IOError:
        return Response("FILE COULD NOT BE READ", status=400) #file could not be read

    if headers == models.hol_pos_off_header:
        result = validate_holder_position_office(reader, filename)
    else:
        if headers == models.el_div_dist_headers():
            result = validate_election_division_district(reader, filename)
        else:
            return #file headers do not match what we want
    return result


def validate_holder_position_office(reader, filename):
    
    return import_holder_position_office(filename)


def validate_election_division_district(reader, filename):
    
    return import_election_division_district(name)
    

def import_holder_position_office(name):
    result = db.engine.execute(text('SELECT bp_import_off_pos_hol_csv_to_staging_tables(:filename);'), filename=name)

def import_election_division_district(name):
    result = db.engine.execute(text('SELECT bp_import_dist_elec_div_csv_to_staging_tables(:filename);'), filename=name) 
