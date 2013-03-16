import sys, subprocess

stfu = open("/dev/null", "w")

def run_command(args):
	pr = subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
	data = pr.communicate()
	pr.wait()
	return pr.returncode

host = sys.argv[1]
user = sys.argv[2]
port = int(sys.argv[3])
keyfile = sys.argv[4]
session_key = sys.argv[5]

if run_command(["ssh", "%s@%s" % (user, host), "-i", keyfile, "cd /etc/cvm/command_daemon; echo '%s' > session_key && ./command_daemon" % session_key]) == 0:
	if run_command(["autossh", "-f", "-i", keyfile, "-M", str(port + 1), "%s@%s" % (user, host), "-L", "%s:localhost:3434" % port, "-N"]) == 0:
		exit(0)
	else:
		sys.stderr.write("Failed to establish tunnel.\n")
else:
	sys.stderr.write("Failed to start daemon.\n")
