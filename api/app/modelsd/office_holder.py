#***********************************************************************************************************
# Copyright BallotPath 2014
# Developed by Matt Clyde, Andrew Erland, Shawn Forgie, Andrew Hobbs, Kevin Mark, Darrell Sam, Blake Clough
# Open source under GPL v3 license (https://github.com/mclyde/BallotPath/blob/v0.3/LICENSE)
#***********************************************************************************************************

from app import db
from sqlalchemy.dialects import postgresql

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
    email_address = db.Column(db.TEXT)
    website = db.Column(db.VARCHAR(50))
    photo_link = db.Column(db.TEXT)
    notes = db.Column(db.TEXT)
