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
    """ 
    Start the importation process of a csv file located in the upload folder.
    Returns an empty string or the name of a file containing error messages.

    Arguments
    ---------

    'filename' - the name of the file that is to be validated and then imported

    """
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
            result = validate_holder_position_office(filename)
        else:
            if headers == el_div_dist_header:
                result = validate_election_division_district(filename)
            else:	
                #file headers do not match what we want
                result = validation_error("File headers do not match required format!\nReceived:    " + str(headers) + "\nExpected:   " + str(hol_pos_off_header) + "\nOR\n" + str(el_div_dist_header))

    #remove import file from import folder
    cleanup(filename)
    return result


# Validation for the holder, postion and office csv file
def validate_holder_position_office(filename):
    """
    Validate holders, positions and offices fields.

    Arguments
    ---------

    'filename' - the name of the file to be validated, expected location is in the 'UPLOAD_FOLDER'

    """
    validator = CSVValidator(hol_pos_off_header)
    validator.add_header_check('EX1', 'bad header')
    validator.add_record_check(check_term_length_salary_int_fields)
    validator.add_record_check(check_partisan_boolean)
    validator.add_record_check(check_holder_addr_string_length)
    validator.add_record_check(check_valid_dates_holder)
    validator.add_record_check(check_holder_not_null_fields)

    data = csv.reader(open(os.path.join(app.config['UPLOAD_FOLDER'], filename)), delimiter='|', dialect=csv.excel)
    problems = validator.validate(data)

    if problems is not None and problems != []:
        fname = 'bad_inserts_'+ time.strftime("%Y-%m-%d-%H_%M_%S") + '.csv'
        efile = open(os.path.join(app.config['ERROR_FOLDER'], fname), 'w')
        write_problems(problems, efile)
        return fname

    return import_holder_position_office(filename)


def check_term_length_salary_int_fields(r):
    """
    Check that term_length and salary fields are ints and accept ''
    """
    msg = ''
    term_length = str(r['term_length_months'])
    salary = str(r['salary'])
    
    term_int = try_parse(term_length)
    salary_int = try_parse(salary)
    
    if term_int is None:
        msg = 'term_length_months value recieved: {0}\n'.format(term_length)
    if salary_int is None:
        msg += 'salary value recieved: {0}\n'.format(salary)

    if msg:
        raise RecordError('EX2', 'Expected integer for {0}\n'.format(msg))


def try_parse(string, fail=None):
    try:
        if string:
            return float(string)
        return 0
    except Exception:
        return fail


def check_holder_not_null_fields(r):
    """
    Check that required holder_position_office.csv fields are not empty
    """
    dist_name = str(r['district_name'])
    dist_state = str(r['district_state'])
    el_name = str(r['election_div_name'])   
    pos_name = str(r['position_name'])
    title = str(r['title'])

    dname_valid = (dist_name.strip())
    dstate_valid = (dist_state.strip())
    ename_valid = (el_name.strip())
    pos_valid = (pos_name.strip())
    title_valid = (title.strip())

    msg = ''
    if not dname_valid:
        msg = 'district_name: {0}\n'.format(dist_name)
    if not dstate_valid:
        msg += 'district_state: {0}\n'.format(dist_state)
    if not ename_valid:
        msg += 'election_div_name: {0}\n'.format(el_name)

    if (pos_valid and not title_valid) or (not pos_valid and title_valid):
        if pos_valid:
            msg += 'title: {0}\n'.format(title)
        else:
            msg += 'position_name: {0}\n'.format(pos_name)

    if msg:
       raise RecordError('EX7', 'Expected non-empty string:\n{0}'.format(msg))


def check_partisan_boolean(r):
    """
    Check that partisan field is a valid boolean value that can be interpreted by postgresql
    """
    partisan = str(r['partisan']).strip()
    
    valid = partisan in ['TRUE', 'True', 't', 'true', 'y', 'yes', 'on', '1', 'FALSE', 'False', 'f', 'false', 'n', 'no', 'off', '0', '']
    if not valid:
        raise RecordError('EX3', 'partisan must be True or False. Encountered: {0}'.format(partisan))


def check_holder_addr_string_length(r):
    """
    Check that address fields are no more than 25 characters long
    Note: This check may be removed later if we change db to accept longer addresses
    """
    addr1 = str(r['holder_addr1'])
    addr2 = str(r['holder_addr2'])
    valid = (len(addr1) <= 100 and len(addr2) <= 100)
    if not valid:
        raise RecordError('EX5', 'Length is too long, must be less than or equal to 25 characters\nFor holder_addr1: {0}\nOR\nholder_addr2: {1}'.format(addr1, addr2))


def check_valid_dates_holder(r):
    """
    Check that date fields are in expected format.
    ex: 'Jan 12, 2012' or 'January 12, 2012'
    Really any format that postgres could interpret could be accepted but we will limit it for now.
    """
    start = str(r['term_start']).strip()
    end = str(r['term_end']).strip()
    f_deadline = str(r['filing_deadline']).strip()
    next_el = str(r['next_election']).strip()

    valid = ((try_parse_date(start, '%b %d, %Y') or try_parse_date(start, '%B %d, %Y') or not start) 
              and (try_parse_date(end, '%b %d, %Y') or try_parse_date(end, '%B %d, %Y') or not end)
              and (try_parse_date(f_deadline, '%b %d, %Y') or try_parse_date(f_deadline, '%B %d, %Y') or not f_deadline)
              and (try_parse_date(next_el, '%b %d, %Y') or try_parse_date(next_el, '%B %d, %Y') or not next_el))
    if not valid:
        raise RecordError('EX6', 'Incorrect date format in one of the following: {0}, {1}, {2}, {3}\nValid format examples are "Jan 22, 2012" or "January 22, 2012"'.format(start, end, f_deadline, next_el))


def try_parse_date(s, f):
    """
    Try to parse a datetime string with a given format.
    Returns a boolean true if successful, false if not.

    Arguments
    ---------

    's' - string to parse
    'f' - format to parse date as

    """
    if s is not None:
        try: 
            datetime.strptime(s, f)
        except ValueError:
            return False
    return True


def validate_election_division_district(filename):
    """
    Validate district and election_division fields.

    Arguments
    ---------

    'filename' - the name of the file to validate, expected location is 'UPLOAD_FOLDER'

    """
    validator = CSVValidator(el_div_dist_header)
    validator.add_header_check('EX1', 'bad header')
    validator.add_record_check(check_election_div_addr_length)
    validator.add_record_check(check_election_div_not_null_fields)

    data = csv.reader(open(os.path.join(app.config['UPLOAD_FOLDER'], filename)), delimiter='|', dialect=csv.excel)
    problems = validator.validate(data)

    if problems is not None and problems != []:
        fname = 'bad_inserts_'+ time.strftime("%Y-%m-%d-%H_%M_%S") + '.csv'
        efile = open(os.path.join(app.config['ERROR_FOLDER'], fname), 'w')
        write_problems(problems, efile)
        return fname

    return import_election_division_district(filename)
    

def check_election_div_not_null_fields(r):
    """
    Check that the required fields of the election division and district csv are not empty
    """
    dist_name = str(r['district_name'])
    dist_state = str(r['district_state'])
    el_name = str(r['election_div_name'])
    el_state = str(r['phys_addr_state'])

    dname_valid = (dist_name.strip())
    dstate_valid = (dist_state.strip())
    ename_valid = (el_name.strip())
    estate_valid = (el_state.strip())

    msg = ''
    if not dname_valid:
        msg = 'district_name: {0}\n'.format(dist_name)
    if not dstate_valid:
        msg += 'district_state: {0}\n'.format(dist_state)
    if not ename_valid:
        msg += 'election_div_name: {0}\n'.format(el_name)
    if not estate_valid:
        msg += 'phys_addr_state: {0}\n'.format(el_state)

    if msg:
       raise RecordError('EX3', 'Expected non-empty string:\n{0}'.format(msg))


def check_election_div_addr_length(r):
    """
    Check to see if address fields have too many characters.
    """
    phys_addr1 = str(r['phys_addr1'])
    phys_addr2 = str(r['phys_addr2'])
    mail_addr1 = str(r['mail_addr1'])
    mail_addr2 = str(r['mail_addr2'])
    
    p1_valid = (len(phys_addr1) <= 100)
    p2_valid = (len(phys_addr2) <= 100)
    m1_valid = (len(phys_addr1) <= 100)
    m2_valid = (len(phys_addr2) <= 100)
    
    msg = ''

    if not p1_valid:
        msg = 'phys_addr1: ' + phys_addr1 + '\n'
    if not p2_valid:
        msg += 'phys_addr2: ' + phys_addr2 + '\n'
    if not m1_valid:
        msg += 'mail_addr1: ' + mail_addr1 + '\n'
    if not m2_valid:
        msg += 'mail_addr2: ' + mail_addr2

    if msg:
        raise RecordError('EX2', 'Address length is too long expected 25 characters max\n{0}'.format(msg))


def import_holder_position_office(name):
    """
    Call the stored procedure to import office holders, positions and offices from a validated csv.
    """
    cmd = 'SELECT bp_import_off_pos_hol_csv_to_staging_tables(:filename);'
    return run_import_cmd(name, cmd, 0)


def run_import_cmd(name, cmd, sp):
    msg = ''
    conn = db.engine.connect()
    trans = conn.begin()
    try: 
        result = conn.execute(text(cmd), filename=name)
        row = result.fetchone()
        if sp == 0:
            msg = row['bp_import_off_pos_hol_csv_to_staging_tables']
        else:
            msg = row['bp_import_dist_elec_div_csv_to_staging_tables']
        trans.commit()
    except SQLAlchemyError as sqle:
        trans.rollback()
        conn.close()
        return validation_error(str(sqle))#.split("CONTEXT:")[0])
    conn.close()
    return msg


def import_election_division_district(name):
    """
    Call the stored procedure to import a validated election division and district csv.
    """
    cmd = 'SELECT bp_import_dist_elec_div_csv_to_staging_tables(:filename);'
    return run_import_cmd(name, cmd, 1)


def cleanup(filename):
    """
    Remove the uploaded file from the system.
    """
    rfile = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    try:  
        if os.path.exists(rfile):
            os.remove(rfile)
    except OSError:
        pass


def validation_error(error):
    """
    Write an error message to a file that can be returned to the client

    Arguments
    ---------

    'error' - the error message to write

    """
    filename = 'bad_inserts_'+ time.strftime("%Y-%m-%d-%H_%M_%S") + '.csv'
    efile = os.path.join(app.config['ERROR_FOLDER'], filename)
    with  open(efile, 'w') as errorfile:
        errorfile.write(error)
    return filename
