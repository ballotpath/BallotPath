from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json
from viewsd.office import *
from viewsd.office_holder import *
from viewsd.district import *
from viewsd.election_division import *

@app.route("/")
@app.route("/index")
def index():
    return '<a href="'+url_for('get_offices', latitude=1, longitude=1)+'">Try looking for Offices for latitude 1 and longitutde 1</a><br /><a href="'+url_for('get_office', office_id=1)+'">Try looking for Office with id 1</a><br /><a href="'+url_for('get_districts')+'">Try looking for all Districts</a><br /><a href="'+url_for('get_district', district_id=5)+'">Try looking for District with id 5</a>'
