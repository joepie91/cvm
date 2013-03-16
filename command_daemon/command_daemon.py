#!/usr/bin/env python

import sys, os
import subprocess
import json, urlparse
import SocketServer, SimpleHTTPServer
from optparse import OptionParser

parser = OptionParser()
(options, cmdargs) = parser.parse_args()

try:
	f = open("session_key", "r")
	session_key = f.read().strip()
	f.close()
except IOError, e:
	sys.stderr.write("You must specify a session key.\n")
	exit(1)
	
os.remove("session_key")

class CommandHandler(SimpleHTTPServer.SimpleHTTPRequestHandler):
	def do_GET(self):
		global session_key
		
		req = urlparse.urlparse(self.path)
		get_params = urlparse.parse_qs(req.query)
		path = req.path
		
		if path=='/':
			try:
				command = json.loads(get_params['command'][0])
			except KeyError, e:
				self.send_404()
				return
			except IndexError, e:
				self.send_404()
				return
			except ValueError, e:
				self.send_404()
				return
				
			try:
				key = get_params['key'][0]
			except KeyError, e:
				self.send_403()
				return
			except IndexError, e:
				self.send_403()
				return
				
			if key != session_key:
				self.send_403()
				return
			
			try:
				result = json.dumps(self.run_command(command))
			except Exception, e:
				print e
				self.send_404()
				return
			
			self.send_response(200)
			self.send_header('Content-type','text/json')
			self.end_headers()
			self.wfile.write(result)
		else:
			self.send_404()
			return
			
	def send_404(self):
		self.send_response(404)
		self.send_header('Content-type','text/plain')
		self.end_headers()
		self.wfile.write("404 Not Found")
		
	def send_403(self):
		self.send_response(403)
		self.send_header('Content-type','text/plain')
		self.end_headers()
		self.wfile.write("403 Forbidden")
		
	def run_command(self, args):
		pr = subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
		data = pr.communicate()
		pr.wait()

		return {
			'stdout': data[0],
			'stderr': data[1],
			'returncode': pr.returncode
		}

if os.fork(): exit(0)
os.umask(0) 
os.setsid() 
if os.fork(): exit(0)

sys.stdout.flush()
sys.stderr.flush()
si = file('/dev/null', 'r')
so = file('/dev/null', 'a+')
se = file('/dev/null', 'a+', 0)
os.dup2(si.fileno(), sys.stdin.fileno())
os.dup2(so.fileno(), sys.stdout.fileno())
os.dup2(se.fileno(), sys.stderr.fileno())

httpd = SocketServer.ThreadingTCPServer(("localhost", 3434), CommandHandler)
httpd.serve_forever()
