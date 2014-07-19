from flask import Response, url_for, send_from_directory, render_template, redirect
from flask_restful import request
from werkzeug.utils import secure_filename
from app import app
import bulkimport
import os
import uuid


@app.route('/bulkupload')
def bulkupload():
    return render_template('upload.html') 


@app.route('/upload', methods=['POST'])
def upload_file():
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
    # file is downloaded to client machine
    return send_from_directory(app.config['ERROR_FOLDER'], filename)


def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1] in 'csv'
