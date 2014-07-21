from app import db
from sqlalchemy.dialects import postgresql

class level(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(12))