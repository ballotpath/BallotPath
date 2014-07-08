from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

# District:
@app.route("/district/", methods = ['GET'])
def get_districts():
    districts = models.district.query.all()
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
    district = models.district.query.get(district_id)
    if district == None:
        return Response(json.dumps(None), status=404, mimetype='application/json')
    else:
        district_dict = dict(district.__dict__)
        del district_dict['_sa_instance_state']
        return Response(json.dumps(district_dict), status=200, mimetype='application/json')

@app.route("/district/<int:district_id>/", methods = ['POST'])
def post_district(district_id):
    # First make sure we got a JSON object with the request
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        district = models.district(name=json['name'], level_id=json['level_id'], election_div_id=json['election_div_id'])
        # db.session.add(district)
        # db.session.commit()
        return Response("", status=200)
    else:
        # If we don't have one, about with HTML code 415
        # 10.4.16 415 Unsupported Media Type
        # The server is refusing to service the request because the 
        # entity of the request is in a format not supported by the
        # requested resource for the requested method.
        abort(415)

@app.route("/district/<int:district_id>/", methods = ['PUT'])
def put_district(district_id):
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        district = models.district.get(district_id)
        # Only update if the office already exists, otherwise do nothing
        if district != None:
            district.name=json['name']
            district.level_id=json['level_id']
            district.election_div_id=json['election_div_id']
            # db.session.commit()
            return Response("", status=200)
        else:
            return Response("", status=404)
        return Response("", status=200)
    else:
        abort(415)

@app.route("/district/<int:district_id>/", methods = ['DELETE'])
def delete_district(district_id):
    district = models.district.get(district_id);
    if(district == None):
        # Give a 404 error if an item with the given ID doesn't exist
        abort(404)
    else:
        # db.session.delete(district)
        # db.session.commit()
        return Response("", status=200)