#!/usr/bin/env python

import paramiko, socket, sys, termios, tty, select, urllib, urllib2, json

key = "abcde"
endpoint = "http://cvm.local/api.local.php"

def posix_shell(chan):
	oldtty = termios.tcgetattr(sys.stdin)
	
	try:
		tty.setraw(sys.stdin.fileno())
		tty.setcbreak(sys.stdin.fileno())
		chan.settimeout(0.0)

		while True:
			r, w, e = select.select([chan, sys.stdin], [], [])
			if chan in r:
				try:
					buff = chan.recv(1024)
					if len(buff) == 0:
						print '\r\nYou have been logged out of your container. Goodbye!\r\n',
						break
					sys.stdout.write(buff)
					sys.stdout.flush()
				except socket.timeout:
					pass
			if sys.stdin in r:
				buff = sys.stdin.read(1)
				if len(buff) == 0:
					break
				chan.send(buff)

	finally:
		termios.tcsetattr(sys.stdin, termios.TCSADRAIN, oldtty)
		
def api_request(parameters, method="GET"):
	if method == "GET":
		querystring = urllib.urlencode(parameters)
		req = urllib2.Request(endpoint + "?" + querystring)
		response = urllib2.urlopen(req)
		return json.loads(response.read())


print "#############################################################"
print "###               CVM OpenVZ shell dropper                ###"
print "#############################################################"
print ""
print "Please enter your VPS panel login details to continue."
print ""
username = raw_input("Username: ")
password = raw_input("Password: ")
print ""

auth_result = api_request({
	'key': key,
	'action': "verify_user",
	'username': username,
	'password': password
})

if auth_result['data']['correct'] == True:
	vpslist = api_request({
		'key': key,
		'action': "list_vps",
		'userid': auth_result["data"]["userid"]
	})
	
	print "Select the container you wish to log in to."
	print ""
	
	i = 1
	vpsmap = {}
	nodelist = []
	nodemap = {}
	
	for vps in vpslist["data"]:
		vpsmap[i] = vps
		
		if vps["NodeId"] not in nodelist:
			nodelist.append(vps["NodeId"])
		
		i += 1
		
	for node in nodelist:
		nodemap[node] = api_request({
			'key': key,
			'action': "node_info",
			'nodeid': node
		})['data']

	for key, vps in vpsmap.items():
		node = nodemap[vps['NodeId']]
		print "%s. %s (%s [%s], %s)" % (key, vps['Hostname'], node['Name'], node['Hostname'], node['PhysicalLocation'])
		
	print ""
	choice = raw_input("Make your choice: ")
	
	exit(0)
else:
	print "The supplied login details are invalid."
	print "Your session will now be closed."
	exit(1)





ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

sshkey = paramiko.RSAKey.from_private_key_file('/etc/cvm/key')

ssh.connect('cvm-vz.cryto.net', username='root', pkey=sshkey)

chan = ssh.invoke_shell()
posix_shell(chan)

chan.close()
ssh.close()
