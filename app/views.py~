from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

@app.route("/")
@app.route("/index")
def index():
    return '<a href="'+url_for('get_offices', latitude=1, longitude=1)+'">Try looking for Offices for latitude 1 and longitutde 1</a><br /><a href="'+url_for('get_office', office_id=1)+'">Try looking for Office with id 1</a><br /><a href="'+url_for('get_districts')+'">Try looking for all Districts</a><br /><a href="'+url_for('get_district', district_id=5)+'">Try looking for District with id 5</a>'

@app.route("/office/<float:latitude>/<float:longitude>/")
def get_offices(latitude, longitude, methods = ['GET']):
    # offices = db.engine.execute("select * from PROCEDURE()")
    # Just query for all offices right now, don't use special code
    offices = models.Office.query.all()
    office_dicts = []
    for office in offices:
        office_dicts.append(dict(office.__dict__))
    for office_dict in office_dicts:
        del office_dict['_sa_instance_state']
    return Response(json.dumps(office_dicts), status=200, mimetype='application/json')

@app.route("/office/<int:office_id>/", methods = ['GET'])
def get_office(office_id):
    # Use our model to connect to the DB and get our particular office
    office = models.Office.query.get(office_id)
    if office == None:
        return Response(json.dumps(None), status=404, mimetype='application/json')
    else:
        # Make a copy of the dictionary so we can modify it
        office_dict = dict(office.__dict__)
        # Remove the extra SQLAlchemy data
        del office_dict['_sa_instance_state']
        # Then use Flask's Response class to make an HTML response with
        # the JSON in it
        return Response(json.dumps(office_dict), status=200, mimetype='application/json')

@app.route("/office/<int:office_id>/", methods = ['POST'])
def post_office(office_id):
    # First make sure we got a JSON object with the request
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        return 'got some json - ' + json.dumps(json)
    else:
        # If we don't have one, about with HTML code 415
        # 10.4.16 415 Unsupported Media Type
        # The server is refusing to service the request because the 
        # entity of the request is in a format not supported by the
        # requested resource for the requested method.
        abort(415)

@app.route("/office/<int:office_id>/", methods = ['PUT'])
def put_office(office_id):
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        return 'got some json - ' + json.dumps(json)
    else:
        abort(415)

@app.route("/office/<int:office_id>/", methods = ['DELETE'])
def delete_office(office_id):
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        return 'got some json - ' + json.dumps(json)
    else:
        abort(415)

@app.route("/district/", methods = ['GET'])
def get_districts():
    districts = models.District.query.all()
    district_dicts = []
    for district in districts:
        district_dicts.append(dict(district.__dict__))
    for district_dict in district_dicts:
        del district_dict['_sa_instance_state']
    resp = Response(json.dumps(district_dicts), status=200, mimetype='application/json')
    if district_dicts == []:
        resp.status_code = 404
    return resp

@app.route("/district/<int:district_id>/", methods = ['GET'])
def get_district(district_id):
    district = models.District.query.get(district_id)
    if district == None:
        return Response(json.dumps(None), status=404, mimetype='application/json')
    else:
        district_dict = dict(district.__dict__)
        del district_dict['_sa_instance_state']
        return Response(json.dumps(district_dict), status=200, mimetype='application/json')
