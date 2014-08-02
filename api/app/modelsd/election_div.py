from app import db
from sqlalchemy.dialects import postgresql

class election_div(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    name = db.Column(db.VARCHAR(125), nullable = False)
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
