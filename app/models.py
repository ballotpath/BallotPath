from app import db

class Office(db.Model):
    id = db.Column(db.INTEGER, primary_key = True)
    district_scope_id = db.Column(db.CHAR(1)) 
    name = db.Column(db.VARCHAR(25))
    number_of_positions = db.Column(db.INTEGER)
    partisan = db.Column(db.BOOLEAN)
    notes = db.Column(db.TEXT)

    def __repr__(self):
        return '<Office %r>' % (self.name)
