#***********************************************************************************************************
# Copyright BallotPath 2014
# Developed by Matt Clyde, Andrew Erland, Shawn Forgie, Andrew Hobbs, Kevin Mark, Darrell Sam, Blake Clough
# Open source under GPL v3 license (https://github.com/mclyde/BallotPath/blob/v0.3/LICENSE)
#***********************************************************************************************************

from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

# Office Holder:
@app.route("/office_holder/<int:office_holder_id>/", methods = ['POST'])
def post_office_holder(office_holder_id):
    # First make sure we got a JSON object with the request
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        office_holder = models.office_holder(first_name=json['first_name'], middle_name=json['middle_name'], last_name=json['last_name'], party_affiliation=json['party_affiliation'], address1=json['address1'], address2=json['address2'], city=json['city'], state=json['state'], zip=json['zip'], phone=json['phone'], fax=json['fax'], email_address=json['email_address'], website=json['website'], photo_link=json['photo_link'])
        # db.session.add(office_holder)
        # db.session.commit()
        return Response("", status=200)
    else:
        # If we don't have one, about with HTML code 415
        # 10.4.16 415 Unsupported Media Type
        # The server is refusing to service the request because the 
        # entity of the request is in a format not supported by the
        # requested resource for the requested method.
        abort(415)

@app.route("/office_holder/<int:office_holder_id>/", methods = ['PUT'])
def put_office_holder(office_holder_id):
    if request.headers['Content-Type'] == 'application/json':
        json = request.json
        office_holder = models.office_holder.get(office_holder_id)
        # Only update if the office already exists, otherwise do nothing
        if office_holder != None:
            office_holder.first_name=json['first_name']
            office_holder.middle_name=json['middle_name']
            office_holder.last_name=json['last_name']
            office_holder.party_affiliation=json['party_affiliation']
            office_holder.address1=json['address1']
            office_holder.address2=json['address2']
            office_holder.city=json['city']
            office_holder.state=json['state']
            office_holder.zip=json['zip']
            office_holder.phone=json['phone']
            office_holder.fax=json['fax']
            office_holder.email_address=json['email_address']
            office_holder.website=json['website']
            office_holder.photo_link=json['photo_link']
            # db.session.commit()
            return Response("", status=200)
        else:
            return Response("", status=404)
    else:
        abort(415)

@app.route("/office_holder/<int:office_holder_id>/", methods = ['DELETE'])
def delete_office_holder(office_holder_id):
    office_holder = models.office_holder.get(office_holder_id);
    if(office_holder == None):
        # Give a 404 error if an item with the given ID doesn't exist
        abort(404)
    else:
        # db.session.delete(office_holder)
        # db.session.commit()
        return Response("", status=200)