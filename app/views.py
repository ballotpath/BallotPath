from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

@app.route("/")
@app.route("/index")
def index():
    return '<a href="'+url_for('get_offices', latitude=1, longitude=1)+'">Try looking for Offices for latitude 1 and longitutde 1</a><br /><a href="'+url_for('get_office', office_id=1)+'">Try looking for Office with id 1</a><br /><a href="'+url_for('get_districts')+'">Try looking for all Districts</a><br /><a href="'+url_for('get_district', district_id=5)+'">Try looking for District with id 5</a>'

# Utility function for get_offices to parse a single row of the
# database and convert it a representation using Python built-ins
def parse_office_row(row):
    # First, extract office position information from the query
    office_pos = {}
    office_pos['id'] = row.office_position.id
    office_pos['district_id'] = row.office_position.district_id
    office_pos['office_id'] = row.office_position.office_id
    office_pos['office_holder_id'] = row.office_position.office_holder_id
    office_pos['position_name'] = row.office_position.position_name
    office_pos['term_start'] = str(row.office_position.term_start)
    office_pos['term_end'] = str(row.office_position.term_end)
    office_pos['filing_deadline'] = str(row.office_position.filing_deadline)
    office_pos['next_election'] = str(row.office_position.next_election)
    office_pos['notes'] = row.office_position.notes
    # Next, extract office information
    office = {}
    office['id'] = row.office.id
    office['title'] = row.office.title
    office['num_positions'] = row.office.num_positions
    office['responsibilities'] = row.office.responsibilities
    office['term_length_months'] = row.office.term_length_months
    office['filing_fee'] = row.office.filing_fee
    office['partisan'] = row.office.partisan
    office['age_requirements'] = row.office.age_requirements
    office['res_requirements'] = row.office.res_requirements
    office['prof_requirements'] = row.office.prof_requirements
    office['salary'] = row.office.salary
    office['notes'] = row.office.notes
    # Put it in as a sub-object
    office_pos['office'] = office
    # Finally, extract office_holder information
    office_holder = {}
    office_holder['id'] = row.office_holder.id
    office_holder['first_name'] = row.office_holder.first_name
    office_holder['middle_name'] = row.office_holder.middle_name
    office_holder['last_name'] = row.office_holder.last_name
    office_holder['party_affiliation'] = row.office_holder.party_affiliation
    office_holder['address1'] = row.office_holder.address1
    office_holder['address2'] = row.office_holder.address2
    office_holder['city'] = row.office_holder.city
    office_holder['state'] = row.office_holder.state
    office_holder['zip'] = row.office_holder.zip
    office_holder['phone'] = row.office_holder.phone
    office_holder['fax'] = row.office_holder.fax
    office_holder['email_address'] = row.office_holder.email_address
    office_holder['website'] = row.office_holder.website
    office_holder['photo_link'] = row.office_holder.photo_link
    office_holder['notes'] = row.office_holder.notes
    # And also add it as a sub-object
    office_pos['office_holder'] = office_holder
    return office_pos

# Office:
@app.route("/office/<float:latitude>/<float:longitude>/")
def get_offices(latitude, longitude, methods = ['GET']):
    office_positions = []
    # For now, just query all office_positions
    result = db.session.query(models.office, models.office_holder, models.office_position).filter(models.office.id == models.office_position.office_id).filter(models.office_holder.id == models.office_position.id).all()
    for row in result:
        office_positions.append(parse_office_row(row))
    return Response(json.dumps({ "office_positions" : office_positions }), status=200, mimetype='application/json')

@app.route("/office/<int:office_id>/", methods = ['GET'])
def get_office(office_id):
    # Use our model to connect to the DB and get our particular office
    office = models.office.query.get(office_id)
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
        office = models.office(title=json['title'], num_positions=json['num_positions'], responsibilities=json['responsibilities'], term_length_months=json['term_length_months'], filing_fee=json['filing_fee'], partisan=json['partisan'], age_requirements=json['age_requirements'], res_requirements=json['res_requirements'], salary=json['salary'], notes=json['notes'])
        # db.session.add(office)
        # db.session.commit()
        return Response("", status=200)
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
        office = models.office.get(office_id)
        # Only update if the office already exists, otherwise do nothing
        if office != None:
            office.title=json['title']
            office.num_positions=json['num_positions']
            office.responsibilities=json['responsibilities']
            office.term_length_months=json['term_length_months']
            office.filing_fee=json['filing_fee']
            office.partisan=json['partisan']
            office.age_requirements=json['age_requirements']
            office.res_requirements=json['res_requirements']
            office.salary=json['salary']
            office.notes=json['notes']
            # db.session.commit()
            return Response("", status=200)
        else:
            return Response("", status=404)
    else:
        abort(415)

@app.route("/office/<int:office_id>/", methods = ['DELETE'])
def delete_office(office_id):
    office = models.office.get(office_id);
    if(office == None):
        # Give a 404 error if an item with the given ID doesn't exist
        abort(404)
    else:
        # db.session.delete(office)
        # db.session.commit()
        return Response("", status=200)
        
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
