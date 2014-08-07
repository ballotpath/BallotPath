from flask import Response, url_for, send_from_directory, render_template, redirect, after_this_request
from flask_restful import request
from flask.ext.httpauth import HTTPBasicAuth
from werkzeug.utils import secure_filename
from app import app, db, models
import bulkimport
import os
import uuid
import hashlib

auth = HTTPBasicAuth()

def hash_password(password):
    hasher = hashlib.sha512()
    hasher.update(password)
    return hasher.hexdigest()

@auth.verify_password
def verify_password(username, password):
    user = User.query.filter_by(name = username).first()
    if user == None:
        return False
    if user.password != hash_password(password):
        return False
    return True

@app.route('/bulkupload')
def bulkupload():
    return render_template('upload.html') 


@app.route('/upload', methods=['POST'])
@auth.login_required
def upload_file():
    # first check for valid user/password
    # username = request.json.get('username')
    # password = request.json.get('password')
    # user = User.query.filter_by(name = username).first()
    # if user == None:
    #     abort(404)
    # else:
    #     hasher = hashlib.sha512()
    #     hasher.update(user.password)
    #     if hasher.hexdigest() != user.password:
    #         abort(404)
    # then check the file
    ifile = request.files['file']
    if ifile and allowed_file(ifile.filename): 
        filename = str(uuid.uuid4()) + '_'  + secure_filename(ifile.filename) 
        ifile.save(os.path.join(app.config['UPLOAD_FOLDER'], filename)) 
        result = bulkimport.begin(filename) 
        return redirect(url_for('error_file', filename=result))
    return redirect(url_for('bulkupload'))


@app.route('/errors/')
@app.route('/errors/<filename>')
def error_file(filename=None):
    if not filename:
	# return to upload page
        return redirect(url_for('index'))
    @after_this_request
    def cleanup(response):
        """
        Remove the uploaded file from the system.
        """
        rfile = os.path.join(app.config['ERROR_FOLDER'], filename)
        try:
            if os.path.exists(rfile):
                os.remove(rfile)
        except OSError:
            pass
        return response

    # file is downloaded to client machine
    return send_from_directory(app.config['ERROR_FOLDER'], filename)



def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1] in 'csv'

