#!/home/ahobbs/venv_dir/venv/bin/python
import sys
sys.path.insert(0,"/home/ahobbs/venv_dir/BallotPath/api")

from app import app as application
application.run(host='0.0.0.0', port=6112, debug=True);
