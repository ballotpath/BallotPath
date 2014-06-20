from flask import render_template, flash, redirect
from app import app
import json

@app.route("/")
@app.route("/index")
def index():
    return '<a href="/office/1/">Try looking for Office with id 1</a>'

@app.route("/office/<office_id>/")
def get_office(office_id):
    return json.dumps(['foo', {'bar': ('baz', None, 1.0, 2)}])
