from app import db
from sqlalchemy.dialects import postgresql

class office_position(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    district_id = db.Column(db.INTEGER, db.ForeignKey('district.id'))
    office_id = db.Column(db.INTEGER, db.ForeignKey('office.id'))
    office_holder_id = db.Column(db.INTEGER, db.ForeignKey('office_holder.id'))
    position_name = db.Column(db.VARCHAR(125))
    term_start = db.Column(db.DATE)
    term_end = db.Column(db.DATE)
    filing_deadline = db.Column(db.DATE)
    next_election = db.Column(db.DATE)
    notes = db.Column(db.TEXT)
