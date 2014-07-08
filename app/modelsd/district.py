from app import db
from sqlalchemy.dialects import postgresql

class district(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(50))
    level_id = db.Column(db.CHAR)
    election_div_id = db.Column(db.INTEGER)