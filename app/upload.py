from flask import Response, url_for, send_from_directory, render_template, redirect
from flask_restful import request
from werkzeug.utils import secure_filename
from app import app, bulkimport
import os



@app.route('/bulkupload')
def bulkupload():
    return render_template('upload.html') 

@app.route('/upload', methods=['GET', 'POST'])
def upload_file():
    ifile = request.files['file']
    print ifile.filename
    if ifile and allowed_file(ifile.filename):
        filename = secure_filename(ifile.filename)
        ifile.save(os.path.join(app.config['UPLOAD_FOLDER'], filename)) 
        result = bulkimport.begin(os.path.join(app.config['UPLOAD_FOLDER'], filename))
        return redirect(url_for('error_file', filename=result))
    return redirect(url_for('bulkupload'))
    #return '''
    #<!doctype html>
    #<title>Upload new File</title>
    #<h1>Upload new File</h1>
    #<form action="" method=post enctype=multipart/form-data>
    #  <p><input type=file name=file>
    #     <input type=submit value=Upload>
    #</form>
    #'''
   # Response("", status=400) #file was not appropriate


@app.route('/errors/<filename>')
def error_file(filename):
    if not filename:
	# return to upload page
        return redirect(url_for('index'))
    # display file TODO: make the file downloadable?
    return send_from_directory(app.config['ERROR_FOLDER'], filename)



def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1] in 'csv'
