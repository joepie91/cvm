import os, sys, subprocess
from StringIO import StringIO

BOLD_START = '\033[1m'
BOLD_END = '\033[0m'

# Determine distro
if os.path.exists("/etc/centos-release"):
	distro = "centos"
elif os.path.exists("/etc/debian_version"):
	distro = "debian"
else:
	sys.stderr.write("This installer only supports Debian and CentOS.\n")
	exit(1)

if os.path.exists("/etc/cvm/need_reboot"):
	sys.stderr.write(BOLD_START + "Please reboot the system to finish the installation of CVM." + BOLD_END + "\n")
	exit(1)
else:
	sys.stdout.write("Please wait while the CVM installation is being finished...\n")
	
	failed = False
	
	if distro == "debian":
		# Check kernel version
		kernel = os.uname()[2]
		
		if "openvz" not in kernel:
			sys.stderr.write("WARNING: No reference to openvz found in kernel name.\n")
			failed = True
		
	# Check vzmond process
	r, w = os.pipe()
	subprocess.call(["ps", "ax"], stdout=w)
	found = False

	for process in os.read(r, 134217728).splitlines():
		if "vzmond" in process:
			found = True
			
	if found == False:
		sys.stderr.write("WARNING: No vzmond process found.\n")
		failed = True
	
	
	r, w = os.pipe()
	subprocess.call(["ifconfig"], stdout=w)
	found = False

	for line in os.read(r, 134217728).splitlines():
		if "venet0" in line:
			found = True
			
	if found == False:
		sys.stderr.write("WARNING: No venet0 network interface found.\n")
		failed = True
	
	
	if failed == True:
		sys.stderr.write(BOLD_START + "One or more checks failed." + BOLD_END + " It is possible that OpenVZ was not\n")
		sys.stderr.write("  successfully installed. A more likely possibility is that the wrong\n")
		sys.stderr.write("  kernel was booted. Verify that your GRUB configuration is correct,\n")
		sys.stderr.write("  and reboot the system.\n")
	
	# Remove post-reboot scripts
	bashrc = open("/root/.bashrc", "r")
	bashrc_lines = bashrc.readlines()
	bashrc.close()
	
	bashrc = open("/root/.bashrc", "w")
	
	for line in bashrc_lines:
		if line.strip() != "python /root/cvm/post_reboot.py":
			bashrc.write(line)
	
	bashrc.close()
	
	rclocal = open("/etc/rc.local", "r")
	rclocal_lines = rclocal.readlines()
	rclocal.close()
	
	rclocal = open("/etc/rc.local", "w")
	
	for line in rclocal_lines:
		if line.strip() != "rm -f /etc/cvm/need_reboot":
			rclocal.write(line)
	
	rclocal.close()
	
	os.remove("/root/cvm/post_reboot.py")
	os.remove("/root/cvm/setuplib.py")
	
	sys.stdout.write(BOLD_START + "CVM slave node installation successfully finished!" + BOLD_END + "\n")
	
	exit(0)
