from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

# Utility function for get_offices to parse a single row of the
# database and convert it a representation using Python built-ins
def parse_office_row(row):
    # First, extract office position information from the query
    office_pos = {}
    office_pos['id'] = row.office_position_id
    office_pos['district_id'] = row.office_position_district_id
    office_pos['office_id'] = row.office_position_office_id
    office_pos['office_holder_id'] = row.office_position_office_holder_id
    office_pos['position_name'] = row.office_position_position_name
    office_pos['term_start'] = str(row.office_position_term_start)
    office_pos['term_end'] = str(row.office_position_term_end)
    office_pos['filing_deadline'] = str(row.office_position_filing_deadline)
    office_pos['next_election'] = str(row.office_position_next_election)
    office_pos['notes'] = row.office_position_notes
    # Next, extract office information
    office = {}
    office['id'] = row.office_id
    office['title'] = row.office_title
    office['num_positions'] = row.office_num_positions
    office['responsibilities'] = row.office_responsibilities
    office['term_length_months'] = row.office_term_length_months
    office['filing_fee'] = row.office_filing_fee
    office['partisan'] = row.office_partisan
    office['age_requirements'] = row.office_age_requirements
    office['res_requirements'] = row.office_res_requirements
    office['prof_requirements'] = row.office_prof_requirements
    office['salary'] = row.office_salary
    office['notes'] = row.office_notes
    # Put it in as a sub-object
    office_pos['office'] = office
    # Finally, extract office_holder information
    office_holder = {}
    office_holder['id'] = row.office_holder_id
    office_holder['first_name'] = row.office_holder_first_name
    office_holder['middle_name'] = row.office_holder_middle_name
    office_holder['last_name'] = row.office_holder_last_name
    office_holder['party_affiliation'] = row.office_holder_party_affiliation
    office_holder['address1'] = row.office_holder_address1
    office_holder['address2'] = row.office_holder_address2
    office_holder['city'] = row.office_holder_city
    office_holder['state'] = row.office_holder_state
    office_holder['zip'] = row.office_holder_zip
    office_holder['phone'] = row.office_holder_phone
    office_holder['fax'] = row.office_holder_fax
    office_holder['email_address'] = row.office_holder_email_address
    office_holder['website'] = row.office_holder_website
    office_holder['photo_link'] = row.office_holder_photo_link
    office_holder['notes'] = row.office_holder_notes
    # And also add it as a sub-object
    office_pos['office_holder'] = office_holder
    return office_pos
# Office:
@app.route("/office/<latitude>/<longitude>/")
def get_offices(latitude, longitude, methods = ['GET']):
    office_positions = []
    #result = db.session.query(models.office, models.office_holder, models.office_position).filter(models.office.id == models.office_position.office_id).filter(models.office_holder.id == models.office_position.id).all()
    cmd = """
SELECT office_position.id as office_position_id
       , office_position.district_id as office_position_district_id
       , office_position.office_id as office_position_office_id
       , office_position.office_holder_id as office_position_office_holder_id
       , office_position.position_name as office_position_position_name
       , office_position.term_start as office_position_term_start
       , office_position.term_end as office_position_term_end
       , office_position.filing_deadline as office_position_filing_deadline
       , office_position.next_election as office_position_next_election
       , office_position.notes as office_position_notes
    --OFFICES
       , office.id as office_id
       , office.title as office_title
       , office.num_positions as office_num_positions
       , office.responsibilities as office_responsibilities
       , office.term_length_months as office_term_length_months
       , office.filing_fee as office_filing_fee
       , office.partisan as office_partisan
       , office.age_requirements as office_age_requirements
       , office.res_requirements as office_res_requirements
       , office.prof_requirements as office_prof_requirements
       , office.salary as office_salary
       , office.notes as office_notes
    --OFFICEHOLDER
       , office_holder.id as office_holder_id
       , office_holder.first_name as office_holder_first_name
       , office_holder.middle_name as office_holder_middle_name
       , office_holder.last_name as office_holder_last_name
       , office_holder.party_affiliation as office_holder_party_affiliation
       , office_holder.address1 as office_holder_address1
       , office_holder.address2 as office_holder_address2
       , office_holder.city as office_holder_city
       , office_holder.state as office_holder_state
       , office_holder.zip as office_holder_zip
       , office_holder.phone as office_holder_phone
       , office_holder.fax as office_holder_fax
       , office_holder.email_address as office_holder_email_address
       , office_holder.website as office_holder_website
       , office_holder.photo_link as office_holder_photo_link
       , office_holder.notes as office_holder_notes
   FROM bp_get_officeids_from_point("""+str(longitude)+", "+str(latitude)+""") sp 
       JOIN office_position ON sp.district_id = office_position.district_id
       JOIN office ON office_position.office_id = office.id
       JOIN office_holder ON office_position.office_holder_id = office_holder.id"""
    result = db.session.execute(cmd)
    for row in result:
        office_positions.append(parse_office_row(row))
    return Response(json.dumps({ "office_positions" : office_positions }), status=200, mimetype='application/json')

@app.route("/office/<int:office_id>/", methods = ['GET'])
def get_office(office_id):
    # Use our model to connect to the DB and get our particular office
    office = models.office.query.get(office_id)
    result = db.session.execute("SELECT id as office_id FROM office WHERE id=1;")
    for row in result:
        error.text
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
