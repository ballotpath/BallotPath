#***********************************************************************************************************
# Copyright BallotPath 2014
# Developed by Matt Clyde, Andrew Erland, Shawn Forgie, Andrew Hobbs, Kevin Mark, Darrell Sam, Blake Clough
# Open source under GPL v3 license (https://github.com/mclyde/BallotPath/blob/v0.3/LICENSE)
#***********************************************************************************************************

from app import db
from sqlalchemy.dialects import postgresql

class level(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(12))
    rank = db.Column(db.INTEGER)
