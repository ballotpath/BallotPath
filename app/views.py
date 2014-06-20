from flask import render_template, flash, redirect, url_for
from app import app, db, models
import json

@app.route("/")
@app.route("/index")
def index():
    return '<a href="'+url_for('get_office', office_id=1)+'">Try looking for Office with id 1</a>'

@app.route("/office/<office_id>/")
def get_office(office_id):
    # Use our model to connect to the DB
    offices = models.Office.query.get(office_id)
    if offices == None:
        return json.dumps(['foo', {'bar': ('baz', None, 1.0, 2)}])
    else:
        return offices
