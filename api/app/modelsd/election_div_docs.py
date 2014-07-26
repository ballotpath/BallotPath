from app import db
from sqlalchemy.dialects import postgresql

class election_div_docs(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    election_div_id = db.Column(db.INTEGER, db.ForeignKey('election_div.id'))
    name = db.Column(db.VARCHAR(35), nullable = False)
    link = db.Column(db.TEXT, nullable = False)
