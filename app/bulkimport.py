from app import app, db, models
from flask import Response, url_for
from sqlalchemy import text
from sqlalchemy.exc import SQLAlchemyError
import csv
import os


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
        if headers == models.el_div_dist_header:
            result = validate_election_division_district(reader, filename)
        else:
            return #file headers do not match what we want
        #remove import file from import folder
        finish(filename)
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
        print sqle

def import_election_division_district(name):
    try:
        result = db.engine.execute(text('SELECT bp_import_dist_elec_div_csv_to_staging_tables(:filename);'), filename=name)
        for row in result:
            return row['bp_import_dist_elec_div_csv_to_staging_tables']  
    except SQLAlchemyError as sqle:
        print sqle

def finish(filename):
    rfile = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    try:
        if os.path.exists(rfile):
            os.remove(rfile)
    except OSError:
        pass
