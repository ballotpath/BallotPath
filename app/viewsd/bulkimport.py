from app import app, db
from flask import Response, url_for
from sqlalchemy import text
from sqlalchemy.exc import SQLAlchemyError
import csv
import os
import time
import sys
from csvvalidator import *

hol_pos_off_header = ['first_name', 'middle_name', 'last_name', 'holder_addr1', 'holder_addr1_city', 'holder_addr1_state', 'holder_addr1_zip', 'holder_addr2', 'holder_phone', 'holder_email', 'holder_website', 'photo_link', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'position_notes', 'position_rank', 'title', 'number_of_positions', 'responsibilities', 'term_length_months', 'filing_fee', 'partisan', 'age_reqs', 'residency_reqs', 'professional_reqs', 'salary', 'office_notes', 'office_rank', 'office_doc_name', 'office_doc_link', 'district_name', 'district_state', 'election_div_name']


el_div_dist_header = ['district_name', 'district_state', 'level_name', 'election_div_name', 'phys_addr1', 'phys_addr2', 'phys_addr_city', 'phys_addr_state', 'phys_addr_zip', 'mail_addr1', 'mail_addr2', 'mail_addr_city', 'mail_addr_state', 'mail_addr_zip', 'election_div_phone', 'fax', 'election_div_website', 'election_div_doc_name', 'election_div_doc_link']

def begin(filename):
    result = ''
    try:
        impfile = open(os.path.join(app.config['UPLOAD_FOLDER'], filename))
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
    cleanup(filename)
    return result


# Validation for the holder, postion and office csv file
def validate_holder_position_office(reader, filename):
    validator = CSVValidator(hol_pos_off_header)
    validator.add_header_check('EX1', 'bad header')
    validator.add_record_check(check_holder_addr_string_length)

    validator.add_record_check(check_valid_dates_holder)
    #datemsg = ' should be in the format: month, dd yyyy'
    #validator.add_value_check('term_start', datetime_string('%m %d, %Y'), 'EX2', 'term_start'+datemsg)
    #validator.add_value_check('term_end', datetime_string('%m %d, %Y'), 'EX3', 'term_end'+datemsg)
    #validator.add_value_check('filing_deadline', datetime_string('%m %d, %Y'), 'EX4', 'filing_deadline'+datemsg)
    #validator.add_value_check('next_election', datetime_string('%m %d, %Y'), 'EX5', 'next_election'+datemsg)

    validator.add_value_check('term_length_months', int, 'EX2', 'term_length_months must be an integer')
    validator.add_value_check('partisan', bool, 'EX3', 'partisan must be True or False')
    validator.add_value_check('salary', int, 'EX4', 'salary must be an integer')

    problems = validator.validate(reader)

    if problems is not None:
        fname = 'bad_inserts_'+ time.strftime("%Y-%m-%d-%H_%M_%S") + '.csv'
        efile = open(os.path.join(app.config['ERROR_FOLDER'], fname), 'w')
        write_problems(problems, efile)
        return fname

    return import_holder_position_office(filename)


def check_holder_addr_string_length(r):
    addr1 = str(r['holder_addr1'])
    addr2 = str(r['holder_addr2'])
    valid = (len(addr1) <= 25 and len(addr2) <= 25)
    if not valid:
        raise RecordError('EX5', 'holder_addr1 or holder_addr2 length is too long, must be less than or equal to 25 characters')



def check_valid_dates_holder(r):
    start = str(r['term_start'])
    end = str(r['term_end'])
    f_deadline = str(r['filing_deadline'])
    next_el = str(r['next_election'])

    valid = ((try_parse_date(start, '%b %d, %Y') or try_parse_date(start, '%B %d, %Y')) 
              and (try_parse_date(end, '%b %d, %Y') or try_parse_date(end, '%B %d, %Y'))
              and (try_parse_date(f_deadline, '%b %d, %Y') or try_parse_date(f_deadline, '%B %d, %Y'))
              and (try_parse_date(next_el, '%b %d, %Y') or try_parse_date(next_el, '%B %d, %Y')))
    if not valid:
        raise RecordError('EX6', 'Incorrect date format: ' +  start + ', ' + end + ', ' + f_deadline + ', ' + next_el)


def try_parse_date(s, f):
    if s is not None:
        try: 
            datetime.strptime(s, f)
        except ValueError:
            return False
    return True


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
