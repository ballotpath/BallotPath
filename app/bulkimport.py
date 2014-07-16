from app import app
from flask import Response, url_for
from sqlalchemy import text
import csv
import os


def begin(filename):
    result = ''
    try:
        with open(filename) as impfile:
            reader = csv.reader(impfile, delimiter='|', dialect=csv.excel)
            header = reader.next()
            print header
    except IOError:
        return Response("", status=400) #file could not be read
    return ''#filename
