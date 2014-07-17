from app import db
from sqlalchemy.dialects import postgresql

class level(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(12))

class office(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    title = db.Column(db.VARCHAR(35), nullable = False)
    num_positions = db.Column(db.INTEGER)
    responsibilities = db.Column(db.TEXT)
    term_length_months = db.Column(db.INTEGER)
    filing_fee = db.Column(db.TEXT)
    partisan = db.Column(db.BOOLEAN)
    age_requirements = db.Column(db.INTEGER)
    res_requirements = db.Column(db.TEXT)
    prof_requirements = db.Column(db.TEXT)
    # salary should become an integer? currently is money
    salary = db.Column(db.TEXT)
    notes = db.Column(db.TEXT)

class office_position(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    district_id = db.Column(db.INTEGER, db.ForeignKey('district.id'))
    office_id = db.Column(db.INTEGER, db.ForeignKey('office.id'))
    office_holder_id = db.Column(db.INTEGER, db.ForeignKey('office_holder.id'))
    position_name = db.Column(db.VARCHAR(25))
    term_start = db.Column(db.DATE)
    term_end = db.Column(db.DATE)
    filing_deadline = db.Column(db.DATE)
    next_election = db.Column(db.DATE)
    notes = db.Column(db.TEXT)

class office_holder(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    first_name = db.Column(db.VARCHAR(25), nullable = False)
    middle_name = db.Column(db.VARCHAR(25))
    last_name = db.Column(db.VARCHAR(25), nullable = False)
    party_affiliation = db.Column(db.CHAR(1))
    address1 = db.Column(db.VARCHAR(25))
    address2 = db.Column(db.VARCHAR(25))
    city = db.Column(db.VARCHAR(25))
    state = db.Column(db.CHAR(2))
    zip = db.Column(db.CHAR(5))
    phone = db.Column(db.VARCHAR(15))
    fax = db.Column(db.VARCHAR(15))
    email_address = db.Column(db.VARCHAR(30))
    website = db.Column(db.VARCHAR(50))
    photo_link = db.Column(db.TEXT)
    notes = db.Column(db.TEXT)

class office_docs(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    office_id = db.Column(db.INTEGER, db.ForeignKey('office.id'))
    name = db.Column(db.VARCHAR(35), nullable = False)
    link = db.Column(db.TEXT, nullable = False)

class district(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(50))
    level_id = db.Column(db.CHAR)
    election_div_id = db.Column(db.INTEGER)

class election_division(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(50), nullable = False)
    phys_addr_addr1 = db.Column(db.VARCHAR(25))
    phys_addr_addr2 = db.Column(db.VARCHAR(25))
    phys_addr_city = db.Column(db.VARCHAR(25))
    phys_addr_state = db.Column(db.CHAR(2))
    phys_addr_zip = db.Column(db.CHAR(5))
    mail_addr_addr1 = db.Column(db.VARCHAR(25))
    mail_addr_addr2 = db.Column(db.VARCHAR(25))
    mail_addr_city = db.Column(db.VARCHAR(25))
    mail_addr_state = db.Column(db.CHAR(2))
    mail_addr_zip = db.Column(db.CHAR(5))
    phone = db.Column(db.VARCHAR(15))
    fax = db.Column(db.VARCHAR(15))
    website = db.Column(db.TEXT)
    notes = db.Column(db.TEXT)

class election_division_docs(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    election_div_id = db.Column(db.INTEGER, db.ForeignKey('election_div.id'))
    name = db.Column(db.VARCHAR(35), nullable = False)
    link = db.Column(db.TEXT, nullable = False)

hol_pos_off_header = ['first_name', 'middle_name', 'last_name', 'holder_addr1', 'holder_addr1_city', 'holder_addr1_state', 'holder_addr1_zip', 'holder_addr2', 'holder_phone', 'holder_email', 'holder_website', 'photo_link', 'position_name', 'term_start', 'term_end', 'filing_deadline', 'next_election', 'position_notes', 'position_rank', 'title', 'number_of_positions', 'responsibilities', 'term_length_months', 'filing_fee', 'partisan', 'age_reqs', 'residency_reqs', 'professional_reqs', 'salary', 'office_notes', 'office_rank', 'office_doc_name', 'office_doc_link', 'district_name', 'district_state', 'election_div_name']


class el_div_dist_headers():
    ['district_name', 'district_state', 'election_division_name']
