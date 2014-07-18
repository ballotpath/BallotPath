from app import app, db, models
from flask import Response, url_for
from sqlalchemy import text
from sqlalchemy.exc import SQLAlchemyError
import csv
import os
import time


def begin(filename):
    result = ''
    try:
        with open(os.path.join(app.config['UPLOAD_FOLDER'], filename)) as impfile:
            reader = csv.reader(impfile, delimiter='|', dialect=csv.excel)
            headers = reader.next()
    except IOError:
        #file could not be read
        result = validation_erro("File could not be opened!")

    if headers is not None:
        if headers == models.hol_pos_off_header:
            result = validate_holder_position_office(reader, filename)
        else:
            if headers == models.el_div_dist_header:
                result = validate_election_division_district(reader, filename)
            else:
                #file headers do not match what we want
                result = validation_error("File headers do not match required format!\n" + headers)

    #remove import file from import folder
    cleanup(filename)
    return result


# Validation for the holder, postion and office csv file
def validate_holder_position_office(reader, filename):
    
    return import_holder_position_office(filename)

# Validation for the election division and district csv file
def validate_election_division_district(reader, filename):
    
    return import_election_division_district(name)
    

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
