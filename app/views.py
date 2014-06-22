from flask import render_template, flash, redirect, url_for
from app import app, db, models
import json

@app.route("/")
@app.route("/index")
def index():
    return '<a href="'+url_for('get_offices', latitude=1, longitude=1)+'">Try looking for Offices for latitude 1 and longitutde 1</a><br /><a href="'+url_for('get_office', office_id=1)+'">Try looking for Office with id 1</a><br /><a href="'+url_for('get_districts')+'">Try looking for all Districts</a>'

@app.route("/office/<latitude>/<longitude>/")
def get_offices(latitude, longitude):
    # offices = db.engine.execute("select * from PROCEDURE()")
    # Just query for all offices right now, don't use special code
    offices = models.Office.query.all()
    office_dicts = []
    for office in offices:
        office_dicts.append(dict(office.__dict__))
    for office_dict in office_dicts:
        del office_dict['_sa_instance_state']
    return json.dumps(office_dicts)

@app.route("/office/<office_id>/")
def get_office(office_id):
    # Use our model to connect to the DB and get our particular office
    office = models.Office.query.get(office_id)
    if office == None:
        return json.dumps([])
    else:
        # Make a copy of the dictionary
        office_dict = dict(office.__dict__)
        # Remove the extra SQLAlchemy data
        del office_dict['_sa_instance_state']
        # Then we can just use the json.dumps method to dump the python
        # dictionary directly to a string
        return json.dumps(office_dict)

@app.route("/district/")
def get_districts():
    districts = models.District.query.all()
    district_dicts = []
    for district in districts:
        district_dicts.append(dict(district.__dict__))
    for district_dict in district_dicts:
        del district_dict['_sa_instance_state']
    return json.dumps(district_dicts)
