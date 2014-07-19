from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

# Election Division
@app.route("/election_division/<int:election_division_id>/", methods = ['POST'])
def post_election_division(election_division_id):
    # First make sure we got a JSON object with the request
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        election_division = models.election_division(name=json['name'], phys_addr_addr1=json['phys_addr_addr1'], phys_addr_addr2=json['phys_addr_addr2'], phys_addr_city=json['phys_addr_city'], phys_addr_state=json['phys_addr_state'], phys_addr_zip=json['phys_addr_zip'], mail_addr_addr1=json['mail_addr_addr1'], mail_addr_addr2=json['mail_addr_addr2'], mail_addr_city=json['mail_addr_city'], mail_addr_state=json['mail_addr_state'], mail_addr_zip=json['mail_addr_zip'], phone=json['phone'], fax=json['fax'], website=json['website'], notes=json['notes'])
        # db.session.add(election_division)
        # db.session.commit()
        return Response("", status=200)
    else:
        # If we don't have one, about with HTML code 415
        # 10.4.16 415 Unsupported Media Type
        # The server is refusing to service the request because the 
        # entity of the request is in a format not supported by the
        # requested resource for the requested method.
        abort(415)

@app.route("/election_division/<int:election_division_id>/", methods = ['PUT'])
def put_election_division(election_division_id):
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        election_division = models.election_division.get(election_division_id)
        # Only update if the office already exists, otherwise do nothing
        if election_division != None:
            election_division.name=json['name']
            election_division.phys_addr_addr1=json['phys_addr_addr1']
            election_division.phys_addr_addr2=json['phys_addr_addr2']
            election_division.phys_addr_city=json['phys_addr_city']
            election_division.phys_addr_state=json['phys_addr_state']
            election_division.phys_addr_zip=json['phys_addr_zip']
            election_division.mail_addr_addr1=json['mail_addr_addr1']
            election_division.mail_addr_addr2=json['mail_addr_addr2']
            election_division.mail_addr_city=json['mail_addr_city']
            election_division.mail_addr_state=json['mail_addr_state']
            election_division.mail_addr_zip=json['mail_addr_zip']
            election_division.phone=json['phone']
            election_division.fax=json['fax']
            election_division.website=json['website']
            election_division.notes=json['notes']
            # db.session.commit()
            return Response("", status=200)
        else:
            return Response("", status=404)
        return Response("", status=200)
    else:
        abort(415)

@app.route("/election_division/<int:election_division_id>/", methods = ['DELETE'])
def delete_election_division(election_division_id):
    election_division = models.election_division.get(election_division_id);
    if(election_division == None):
        # Give a 404 error if an item with the given ID doesn't exist
        abort(404)
    else:
        # db.session.delete(election_division)
        # db.session.commit()
        return Response("", status=200)