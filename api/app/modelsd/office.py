from app import db
from sqlalchemy.dialects import postgresql

class office(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    title = db.Column(db.VARCHAR(125), nullable = False)
    num_positions = db.Column(db.INTEGER)
    responsibilities = db.Column(db.TEXT)
    term_length_months = db.Column(db.INTEGER)
    filing_fee = db.Column(db.TEXT)
    partisan = db.Column(db.BOOLEAN)
    age_requirements = db.Column(db.VARCHAR(100))
    res_requirements = db.Column(db.TEXT)
    prof_requirements = db.Column(db.TEXT)
    # salary should become an integer? currently is money
    salary = db.Column(db.NUMERIC)
    notes = db.Column(db.TEXT)
    office_rank = db.Column(db.INTEGER)
