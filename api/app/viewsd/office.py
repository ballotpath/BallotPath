from flask import render_template, flash, redirect, url_for, jsonify, Response
from app import app, db, models
import json

# Utility function for get_offices to parse a single row of the
# database and convert it a representation using Python built-ins
def parse_office_row(row):
    # First, extract office position information from the query
    office_pos = {}
    office_pos['district_id'] = row.district_id
    office_pos['position_id'] = row.position_id
    office_pos['office_id'] = row.office_id
    office_pos['holder_id'] = row.holder_id
    office_pos['position_name'] = row.position_name
    office_pos['begin'] = str(row.term_start)
    office_pos['end'] = str(row.term_end)
    office_pos['filing_deadline'] = str(row.filing_deadline)
    office_pos['next_election'] = str(row.next_election)
    office_pos['position_notes'] = row.position_notes
    office_pos['office_title'] = row.office_title
    office_pos['num_positions'] = row.num_positions
    office_pos['term'] = row.term_length_months
    office_pos['office_notes'] = row.office_notes
    office_pos['first_name'] = row.first_name
    office_pos['middle_name'] = row.middle_name
    office_pos['last_name'] = row.last_name
    office_pos['party_affiliation'] = row.party_affiliation
    office_pos['photo_link'] = row.holder_photo_link
    office_pos['holder_notes'] = row.holder_notes
    office_pos['district_name'] = row.district_name
    office_pos['level'] = row.level_name
    return office_pos
# Office:
@app.route("/office/<latitude>/<longitude>/")
def get_offices(latitude, longitude, methods = ['GET']):
    office_positions = []
    #result = db.session.query(models.office, models.office_holder, models.office_position).filter(models.office.id == models.office_position.office_id).filter(models.office_holder.id == models.office_position.id).all()
    cmd = """
SELECT office_position.id as position_id
       , office_position.position_name
       , office_position.term_start
       , office_position.term_end
       , office_position.filing_deadline
       , office_position.next_election
       , office_position.notes as position_notes
    --OFFICES
       , office.id as office_id
       , office.title as office_title
       , office.num_positions
       , office.term_length_months
       , office.notes as office_notes
    --OFFICEHOLDER
       , office_holder.id as holder_id
       , office_holder.first_name
       , office_holder.middle_name
       , office_holder.last_name
       , office_holder.party_affiliation
       , office_holder.photo_link as holder_photo_link
       , office_holder.notes as holder_notes
    --DISTRICT
       , district.id as district_id
       , district.name as district_name
    --LEVEL
       , level.name as level_name
   FROM bp_get_officeids_from_point("""+str(longitude)+", "+str(latitude)+""") sp 
       JOIN office_position ON sp.district_id = office_position.district_id
       JOIN office ON office_position.office_id = office.id
       JOIN office_holder ON office_position.office_holder_id = office_holder.id
       JOIN district ON district.id = sp.district_id
       JOIN level ON level.id = district.level_id
   """
    result = db.session.execute(cmd)
    for row in result:
        office_positions.append(parse_office_row(row))
    return Response(json.dumps({ "positions" : office_positions }), status=200, mimetype='application/json')


def get_office_docs(office_id):
    ret = []
    office_docs = models.office_docs.query.filter(models.office_docs.office_id == office_id).all()
    # Add the document information to our office object
    for office_doc in office_docs:
        office_doc_dict = dict(office_doc.__dict__)
        del office_doc_dict['_sa_instance_state']
        ret.append(office_doc_dict)
    return ret

def get_election_div_docs(election_div_id):
    ret = []
    election_div_docs = models.election_div_docs.query.filter(models.election_div_docs.election_div_id == election_div_id).all()
    for election_div_doc in election_div_docs:
        election_div_doc_dict = dict(election_div_doc.__dict__)
        del election_div_doc_dict['_sa_instance_state']
        ret.append(election_div_doc_dict)
    return ret

def get_office_positions(office_id):
    ret = []
    office_positions = models.office_position.query.filter(models.office_position.office_id == office_id).all()
    for office_position in office_positions:
        office_position_dict = dict(office_position.__dict__)
        del office_position_dict['_sa_instance_state']
        # Convert the date/times to strings so they can be output
        office_position_dict['term_start'] = str(office_position_dict['term_start'])
        office_position_dict['term_end'] = str(office_position_dict['term_end'])
        office_position_dict['filing_deadline'] = str(office_position_dict['filing_deadline'])
        office_position_dict['next_election'] = str(office_position_dict['next_election'])
        # Get the office holder for this position
        office_holder = models.office_holder.query.get(office_position.office_holder_id)
        office_holder_dict = dict(office_holder.__dict__)
        del office_holder_dict['_sa_instance_state']
        office_position_dict['office_holder'] = office_holder_dict
        # Get the district for this position
        if office_position.district_id != None:
            district = models.district.query.get(office_position.district_id)
        else:
            district = models.district.query.get(1)
        district_dict = dict(district.__dict__)
        del district_dict['_sa_instance_state']
        office_position_dict['district'] = district_dict
        # Get the election division for this position from the district info
        election_div = models.election_div.query.get(district.election_div_id)
        election_div_dict = dict(election_div.__dict__)
        del election_div_dict['_sa_instance_state']
        office_position_dict['election_div'] = election_div_dict
        # Finally, get all the election division documents
        office_position_dict['election_div_docs'] = get_election_div_docs(election_div.id)
        ret.append(office_position_dict)
    return ret

@app.route("/office/<int:office_id>/", methods = ['GET'])
def get_office(office_id):
    # Use our model to connect to the DB and get our particular office
    ret = {}
    office = models.office.query.get(office_id)
    if office == None:
        return Response(json.dumps(None), status=404, mimetype='application/json')
    else:
        # Make a copy of the dictionary so we can modify it
        office_dict = dict(office.__dict__)
        # Remove the extra SQLAlchemy data
        del office_dict['_sa_instance_state']
        ret['office'] = office_dict
        # Get the office documents for this office
        ret['office_docs'] = get_office_docs(office.id)
        # Get the office positions for this office
        ret['office_positions'] = get_office_positions(office.id)
        # Then use Flask's Response class to make an HTML response with
        # the JSON in it
        return Response(json.dumps(ret), status=200, mimetype='application/json')

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
