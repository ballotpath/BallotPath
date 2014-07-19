from app import app, db
from flask import Response, url_for
from sqlalchemy import text
from sqlalchemy.exc import SQLAlchemyError
import csv
import os
import time

hol_pos_off_header = ['first_name', 'middle_name', 'last_name', 'holder_addr1', 'holder_addr1_city', 'holder_addr1_state', 'holder_addr1_zip', 'holder_addr2', 'holder_phone', 'holder_email', 'holder_website', 'photo_link', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'position_notes', 'position_rank', 'title', 'number_of_positions', 'responsibilities', 'term_length_months', 'filing_fee', 'partisan', 'age_reqs', 'residency_reqs', 'professional_reqs', 'salary', 'office_notes', 'office_rank', 'office_doc_name', 'office_doc_link', 'district_name', 'district_state', 'election_div_name']


el_div_dist_header = ['district_name', 'district_state', 'level_name', 'election_div_name', 'phys_addr1', 'phys_addr2', 'phys_addr_city', 'phys_addr_state', 'phys_addr_zip', 'mail_addr1', 'mail_addr2', 'mail_addr_city', 'mail_addr_state', 'mail_addr_zip', 'election_div_phone', 'fax', 'election_div_website', 'election_div_doc_name', 'election_div_doc_link']

def begin(filename):
    result = ''
    try:
        with open(os.path.join(app.config['UPLOAD_FOLDER'], filename)) as impfile:
            reader = csv.reader(impfile, delimiter='|', dialect=csv.excel)
            headers = reader.next()
    except IOError:
        #file could not be read
        result = validation_error("File could not be opened!")

    if headers is not None:
        if headers == hol_pos_off_header:
            result = validate_holder_position_office(reader, filename)
        else:
            if headers == el_div_dist_header:
                result = validate_election_division_district(reader, filename)
            else:
		print "headers don't match"
                #file headers do not match what we want
                result = validation_error("File headers do not match required format!\nReceived:    " + str(headers) + "Expected:   " + str(hol_pos_off_header) + "\nOR\n" + str(el_div_dist_header))

    #remove import file from import folder
    #cleanup(filename)
    return result


# Validation for the holder, postion and office csv file
def validate_holder_position_office(reader, filename):
    
    return import_holder_position_office(filename)

# Validation for the election division and district csv file
def validate_election_division_district(reader, filename):
    
    return import_election_division_district(filename)
    

def import_holder_position_office(name):
    try:
        result = db.engine.execute(text('SELECT bp_import_off_pos_hol_csv_to_staging_tables(:filename);'), filename=name)
        for row in result:
            return row['bp_import_off_pos_hol_csv_to_staging_tables']
    except SQLAlchemyError as sqle:
        return validation_error(str(sqle).split("SQL statement")[0])


def import_election_division_district(name):
    try:
        result = db.engine.execute(text('SELECT bp_import_dist_elec_div_csv_to_staging_tables(:filename);'), filename=name)
        for row in result:
            return row['bp_import_dist_elec_div_csv_to_staging_tables']  
    except SQLAlchemyError as sqle:
        return validation_error(str(sqle).split("SQL statement")[0])


def cleanup(filename):
    rfile = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    try:  
        if os.path.exists(rfile):
            os.remove(rfile)
    except OSError:
        pass


def validation_error(error):
    filename = 'bad_inserts_'+ time.strftime("%Y-%m-%d-%H_%M_%S") + '.csv'
    efile = os.path.join(app.config['ERROR_FOLDER'], filename)
    errorfile = open(efile, 'w')
    errorfile.write(error)
    return filename
