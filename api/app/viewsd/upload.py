#***********************************************************************************************************
# Copyright BallotPath 2014
# Developed by Matt Clyde, Andrew Erland, Shawn Forgie, Andrew Hobbs, Kevin Mark, Darrell Sam, Blake Clough
# Open source under GPL v3 license (https://github.com/mclyde/BallotPath/blob/v0.3/LICENSE)
#***********************************************************************************************************

from flask import Response, url_for, send_from_directory, render_template, redirect, after_this_request, abort
from flask_restful import request
from werkzeug.utils import secure_filename
from app import app, db, models
import bulkimport
import os
import uuid
import hashlib

def get_users():
    ret = []
    user_file = open('/var/www/BallotPath/html/secr/users.php')
    # Ignore the first line (PHP die)
    user_file.readline()
    for line in user_file:
        if line != '\n':
            info = line.split(',')
            ret.append({'username':info[0],'password':info[1]})
    return ret

def is_cookie_valid(cookie):
    if not cookie:
        return False
    users = get_users()
    for user in users:
        hasher = hashlib.md5()
        hasher.update(user['username'] + '%' + user['password'])
        if cookie == hasher.hexdigest():
            return True
    return False


@app.route('/bulkupload')
def bulkupload():
    if is_cookie_valid(request.cookies.get('verify')):
        return render_template('upload.html')
    abort(401)


@app.route('/upload', methods=['POST'])
def upload_file():
    if is_cookie_valid(request.cookies.get('verify')):
        ifile = request.files['file']
        if ifile and allowed_file(ifile.filename): 
            filename = str(uuid.uuid4()) + '_'  + secure_filename(ifile.filename) 
            ifile.save(os.path.join(app.config['UPLOAD_FOLDER'], filename)) 
            result = bulkimport.begin(filename) 
            return redirect(url_for('error_file', filename=result))
        return redirect(url_for('bulkupload'))
    abort(401)


@app.route('/errors/')
@app.route('/errors/<filename>')
def error_file(filename=None):
    if not filename:
	# return to upload page
        return redirect(url_for('bulkupload'))
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

