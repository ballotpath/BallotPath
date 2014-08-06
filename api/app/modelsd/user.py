from app import db
from sqlalchemy.dialects import postgresql

class user(db.Model):
    name = db.Column(db.VARCHAR(100), primary_key = True)
    password = db.Column(db.VARCHAR(128), nullable = False)
