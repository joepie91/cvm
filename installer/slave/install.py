import sys, os, subprocess, stat, shutil, re, urllib
import setuplib

BOLD_START = '\033[1m'
BOLD_END = '\033[0m'

stfu = open("/dev/null", "wb")

mirrors = {
	"perl-LockFile-Simple-0.207-1.el5.rf.noarch.rpm":	
		["http://pkgs.repoforge.org/perl-LockFile-Simple/perl-LockFile-Simple-0.207-1.el5.rf.noarch.rpm",
		 "http://cvm.cryto.net/packages/perl-LockFile-Simple-0.207-1.el5.rf.noarch.rpm"],
	"perl-LockFile-Simple-0.207-1.el6.rf.noarch.rpm":	
		["http://pkgs.repoforge.org/perl-LockFile-Simple/perl-LockFile-Simple-0.207-1.el6.rf.noarch.rpm",
		 "http://cvm.cryto.net/packages/perl-LockFile-Simple-0.207-1.el6.rf.noarch.rpm"],
	"cstream-2.7.4-3.el5.rf.x86_64.rpm":			
		["http://pkgs.repoforge.org/cstream/cstream-2.7.4-3.el5.rf.x86_64.rpm",
		 "http://cvm.cryto.net/packages/cstream-2.7.4-3.el5.rf.x86_64.rpm"],
	"cstream-2.7.4-3.el6.rf.x86_64.rpm":			
		["http://pkgs.repoforge.org/cstream/cstream-2.7.4-3.el6.rf.x86_64.rpm",
		 "http://cvm.cryto.net/packages/cstream-2.7.4-3.el6.rf.x86_64.rpm"],
	"cstream-2.7.4-3.el5.rf.i386.rpm":			
		["http://pkgs.repoforge.org/cstream/cstream-2.7.4-3.el5.rf.i386.rpm",
		 "http://cvm.cryto.net/packages/cstream-2.7.4-3.el5.rf.i386.rpm"],
	"cstream-2.7.4-3.el6.rf.i686.rpm":			
		["http://pkgs.repoforge.org/cstream/cstream-2.7.4-3.el6.rf.i686.rpm",
		 "http://cvm.cryto.net/packages/cstream-2.7.4-3.el6.rf.i686.rpm"],
	"vzdump-1.2-4.noarch.rpm":				
		["http://download.openvz.org/contrib/utils/vzdump/vzdump-1.2-4.noarch.rpm",
		 "http://cvm.cryto.net/packages/vzdump-1.2-4.noarch.rpm"]
}

# Determine distro
if os.path.exists("/etc/centos-release"):
	distro = "centos"
	
	# Determine the version of CentOS
	version_data = open("/etc/centos-release", "r").read()
	ver = re.match("CentOS\s+(Linux\s+)?release\s+([0-9]+)", version_data)
	
	if ver is None:
		sys.stderr.write("Could not determine your version of CentOS.\n")
		exit(1)
	
	if ver.group(2) == "5":
		centos_version = 5
	elif ver.group(2) == "6":
		centos_version = 6
	else:
		sys.stderr.write("Only CentOS 5 and 6 are supported by this installer.\n")
		exit(1)
elif os.path.exists("/etc/debian_version"):
	distro = "debian"
else:
	sys.stderr.write("This installer only supports Debian and CentOS.\n")
	exit(1)

# Determine architecture
architecture = os.uname()[4]

if architecture not in ["i386", "i686", "x86_64"]:
	sys.stderr.write("This installer only works on i386/i686/x86_64 architectures. If you believe\n")
	sys.stderr.write("this is in error, please file a bug report with the output of uname -m.\n")
	exit(1)

sys.stdout.write("#######################################\n")
sys.stdout.write("###     CVM Slave Node Installer    ###\n")
sys.stdout.write("#######################################\n")
sys.stdout.write("\n")
sys.stdout.write("Thanks for using CVM! I'll ask you a few questions regarding the\n")
sys.stdout.write("setup of your server - after that you can grab a cup of coffee and\n")
sys.stdout.write("I'll continue setting up your server for you.\n")

# We will keep asking until we get valid answers.
pubkey = ""

while pubkey == "":
	sys.stdout.write("\n")
	sys.stdout.write(BOLD_START + "Question 1: What is the public SSH key CVM should use (in OpenSSH format)?" + BOLD_END + "\n")
	sys.stdout.write("Note that this should be the public key of the master node, *not* your personal public key!\n")
	pubkey = raw_input()

	if not re.match("ssh-(rsa|dss) [a-zA-Z0-9/+]( .+)?", pubkey):
		sys.stderr.write("\nERROR: No valid public key was specified. The public key has to be in OpenSSH format.\n")
		pubkey = ""

enable_dropper = ""

while enable_dropper == "":
	sys.stdout.write("\n")
	sys.stdout.write(BOLD_START + "Question 2: Should I enable the shell dropper (recommended)?" + BOLD_END + " (Y/n)\n")
	enable_dropper = raw_input()

	if enable_dropper.strip() in ["y", "Y", "yes", "YES", "Yes", ""]:
		enable_dropper = "y"
	elif enable_dropper.strip() in ["n", "N", "no", "NO", "No"]:
		enable_dropper = "n"
	else:
		sys.stderr.write("\nERROR: Please enter 'y' or 'n'.\n")
		enable_dropper = ""

sys.stdout.write("\n" + BOLD_START + "Starting installation of CVM..." + BOLD_END + "\n")

# Install dependencies
sys.stdout.write("Installing dependencies...\n")
packages = ["python-setuptools", "sudo"]

if distro == "debian":
	packages.append("bsdutils")
	packages.append("cron")
	subprocess.call(["apt-get", "update", "-y"], stdout=stfu, stderr=stfu)
	result = subprocess.call(["apt-get", "install", "-y"] + packages, stdout=stfu, stderr=stfu)
elif distro == "centos":
	packages.append("vixie-cron")
	packages.append("crontabs")
	result = subprocess.call(["yum", "install", "-y"] + packages, stdout=stfu, stderr=stfu)

if result != 0:
	sys.stderr.write("Dependency installation failed.\n")
	exit(1)

result = subprocess.call(["easy_install", "pip"], stdout=stfu, stderr=stfu)

if result != 0:
	sys.stderr.write("Failed to install pip.\n")

# Check if /etc/passwd or /etc/group is already locked.
if os.path.exists("/etc/passwd.lock") or os.path.exists("/etc/group.lock"):
	sys.stderr.write("Whoops, either /etc/passwd or /etc/group is locked, so I can't edit it.\n")
	sys.stderr.write("Exit the process that is currently editing it, and try again.\n")
	exit(1)

# Lock /etc/passwd and /etc/group so we can safely add a user.
open("/etc/passwd.lock", "w").close()
sys.stdout.write("Lock on /etc/passwd created.\n")

open("/etc/group.lock", "w").close()
sys.stdout.write("Lock on /etc/group created.\n")

# Find highest non-reserved UID in the user list
passwd = open("/etc/passwd", "r+")
highest_uid = 1000

for line in passwd:
	username, password, uid, gid, name, homedir, shell = line.split(":")
	
	if username == "cvm":
		sys.stderr.write("The cvm user was already found. You probably already installed CVM.\n")
		exit(1)
	
	if enable_dropper == "y":
		if username == "vz":
			sys.stderr.write("The vz user was already found. You probably already installed CVM.\n")
			exit(1)
	
	if int(uid) < 32000 and int(uid) > highest_uid:
		highest_uid = int(uid)
		
cvm_uid = highest_uid + 1
vz_uid = highest_uid + 2

# Find highest non-reserved GID in the group list - we will assume same restrictions as for UID
grp = open("/etc/group", "r+")
highest_gid = 1000

for line in grp:
	groupname, password, gid, users = line.split(":")
	
	if groupname == "cvm":
		sys.stderr.write("The cvm group was already found. You probably already installed CVM.\n")
		exit(1)
	
	if enable_dropper == "y":
		if groupname == "vz":
			sys.stderr.write("The vz group was already found. You probably already installed CVM.\n")
			exit(1)
	
	if int(gid) < 32000 and int(gid) > highest_gid:
		highest_gid = int(gid)

cvm_gid = highest_gid + 1
vz_gid = highest_gid + 2

# Append new users and groups
passwd.seek(0, 2)
grp.seek(0, 2)

setuplib.create_directory("/home/cvm", True, cvm_uid, cvm_gid, "u+rwx g+rx")
passwd.write("cvm::%d:%d:CVM Control User:/home/cvm:/home/cvm/cvmshell\n" % (cvm_uid, cvm_gid))
sys.stdout.write("Created cvm user.\n")

grp.write("cvm::%d:\n" % cvm_gid)
sys.stdout.write("Created cvm group.\n")

setuplib.create_directory("/home/cvm/.ssh", True, cvm_uid, cvm_gid, "u+rwx")
authkeys = open("/home/cvm/.ssh/authorized_keys", "a")
authkeys.write("%s\n" % pubkey)
authkeys.close()
sys.stdout.write("Installed public key for cvm user.\n")

if enable_dropper == "y":
	setuplib.create_directory("/home/vz", True, vz_uid, vz_gid, "u+rwx g+rx o+x")
	passwd.write("vz::%d:%d:CVM OpenVZ Shell Dropper:/home/vz:/home/vz/dropper\n" % (vz_uid, vz_gid))
	sys.stdout.write("Created vz user.\n")

	grp.write("vz::%d:\n" % vz_gid)
	sys.stdout.write("Created vz group.\n")

	setuplib.create_directory("/home/vz/.ssh", True, vz_uid, vz_gid, "u+rwx")
	authkeys = open("/home/vz/.ssh/authorized_keys", "a")
	authkeys.write("%s\n" % pubkey)
	authkeys.close()
	sys.stdout.write("Installed public key for vz user.\n")
	
	setuplib.create_file("/home/vz/.hushlogin", "", vz_uid, vz_gid, "u+rwx")
	sys.stdout.write("Hushed login for vz user.\n")

# We're done with /etc/passwd and /etc/group
passwd.close()
grp.close()

# Remove the locks on /etc/passwd and /etc/group
os.remove("/etc/passwd.lock")
sys.stdout.write("Lock on /etc/passwd removed.\n")

os.remove("/etc/group.lock")
sys.stdout.write("Lock on /etc/group removed.\n")

# Create the main CVM data directories
setuplib.create_directory("/etc/cvm", True, 0, cvm_gid, "u+rwx g+rwx o+rx")
setuplib.create_directory("/etc/cvm/log", True, 0, 0, "u+rwx")
setuplib.create_directory("/etc/cvm/command_daemon", True, 0, 0, "u+rwx")
sys.stdout.write("Created directories.\n")

# Copy the runhelper
setuplib.copy_file("runhelper", "/home/cvm/runhelper", True, cvm_uid, cvm_gid, "u+rwx")
sys.stdout.write("Installed runhelper.\n")

# Copy the command daemon
setuplib.copy_file("command_daemon", "/etc/cvm/command_daemon/command_daemon", True, cvm_uid, cvm_gid, "u+rwx")
sys.stdout.write("Installed command daemon.\n")

if enable_dropper == "y":
	# Copy the shell dropper
	setuplib.copy_file("dropper", "/home/vz/dropper", True, vz_uid, vz_gid, "u+rwx")
	sys.stdout.write("Installed OpenVZ shell dropper.\n")

# Copy the logged shell
setuplib.copy_file("logshell", "/home/cvm/logshell", True, cvm_uid, cvm_gid, "u+rwx")
setuplib.copy_file("cvmshell", "/home/cvm/cvmshell", True, cvm_uid, cvm_gid, "u+rwx")
setuplib.copy_file("logcmd", "/home/cvm/logcmd", True, cvm_uid, cvm_gid, "u+rwx")
sys.stdout.write("Installed logged shell.\n")

if os.path.exists("/etc/sudoers.lock"):
	sys.stderr.write("The /etc/sudoers file is already locked.\n")
	exit(1)

# Create lock on /etc/sudoers
open("/etc/sudoers.lock", "w").close()
sys.stdout.write("Lock on /etc/sudoers created.\n")

# Append new rules to /etc/sudoers
sudoers = open("/etc/sudoers", "a")
sudoers.write("cvm ALL = (root) NOPASSWD: /home/cvm/logshell, NOPASSWD: /usr/sbin/vzctl, NOPASSWD: /usr/sbin/vzlist, NOPASSWD: /home/cvm/logcmd\n")

if enable_dropper == "y":
	sudoers.write("vz ALL = (root) NOPASSWD: /usr/sbin/vzctl enter *\n")

sudoers.close()

sys.stdout.write("New /etc/sudoers rules appended.\n")

# Remove lock on /etc/sudoers
os.remove("/etc/sudoers.lock")
sys.stdout.write("Lock on /etc/sudoers removed.\n")

# Store the currently installed version of CVM
setuplib.create_file("/etc/cvm/version", "slave-0.1\n", cvm_uid, cvm_gid, "u+rwx g+rwx o+r")

# Install OpenVZ - or, when it already exists, offer to export the current containers.
if os.path.exists("/etc/vz/vz.conf"):
	sys.stdout.write("OpenVZ is already installed.\n")
	
	sys.stdout.write("I can export your current OpenVZ containers for you, so that you can import them into\n")
	sys.stdout.write("your CVM panel. If you are already using another panel (such as HyperVM or SolusVM),\n")
	sys.stdout.write("however, you should answer 'no' and use the exporter specific for that panel instead.\n")
	sys.stdout.write(BOLD_START + "Do you wish to export existing OpenVZ containers?" + BOLD_END + " (Y/n)\n")
	q = raw_input()
	
	if q.lower() in ["n", "no"]:
		sys.stdout.write(BOLD_START + "CVM slave node installation successfully finished!" + BOLD_END + "\n")
	else:
		import exporter
		
		if exporter.run() == True:
			sys.stdout.write(BOLD_START + "CVM slave node installation successfully finished!" + BOLD_END + "\n")
		else:
			sys.stdout.write(BOLD_START + "Something went wrong during exporting. Otherwise, the CVM slave node installation successfully finished!" + BOLD_END + "\n")
else:
	sys.stdout.write("Installing OpenVZ...\n")
	packages = ["vzctl", "vzquota"]

	if distro == "debian":
		if architecture == "x86_64":
			packages.append("linux-image-openvz-amd64")
		elif architecture == "i386" or architecture == "i686":
			packages.append("linux-image-openvz-686")
		
		packages.append("vzdump")
		subprocess.call(["apt-get", "update", "-y"], stdout=stfu, stderr=stfu)
		result = subprocess.call(["apt-get", "install", "-y"] + packages, stdout=stfu, stderr=stfu)
		sys.stdout.write("Installed OpenVZ kernel and tools.\n")
		
		os.symlink("/var/lib/vz", "/vz")
		sys.stdout.write("Created symlink from /vz to /var/lib/vz for compatibility.\n")
		
		setuplib.copy_file("sysctl.conf", "/etc/sysctl.d/cvm_vz.conf", True, 0, 0, "u+rw a+r")
		result = subprocess.call(["sysctl", "-p", "/etc/sysctl.d/cvm_vz.conf"], stdout=stfu, stderr=stfu)
		
		if result != 0:
			# This gives an error about kernel.sysrq. Appears to have to do with this only being available
			# in the OpenVZ kernel, which isn't loaded yet. We'll just ignore it for now and move on, then
			# check at the next boot whether setting this key was successful.
			#   sys.stderr.write("Failed to load modified sysctl config. OpenVZ installation aborted.\n")
			#   exit(1)
			sys.stderr.write("WARNING: Failed to load complete sysctl config. This may indicate an error\n")
			sys.stderr.write(" during installation, but most likely it simply means that we need to boot\n")
			sys.stderr.write(" into the OpenVZ kernel before we can load the new settings.\n")
			pass
		
		sys.stdout.write("Configuration for sysctl updated.\n")
		
		if os.path.exists("/etc/grub.d/06_OVHkernel"):
			# OVH likes inserting a custom kernel before the standard installed kernels, and this
			# breaks the default kernel booting, leading to the OpenVZ kernel never being booted.
			# We'll rename this to lower its priority and ensure that the OpenVZ kernel comes first.
			os.rename("/etc/grub.d/06_OVHkernel", "/etc/grub.d/11_OVHkernel")
			result = subprocess.call(["update-grub"], stdout=stfu, stderr=stfu)
			
			if result != 0:
				sys.stderr.write("WARNING: Failed to update GRUB configuration. Please ensure you have a\n")
				sys.stderr.write("  valid GRUB configuration before rebooting, or you may brick your server.\n")
			
			sys.stdout.write("Successfully patched OVH's grub.cfg.\n")
	elif distro == "centos":
		if centos_version == 5:
			lockfile_name = "perl-LockFile-Simple-0.207-1.el5.rf.noarch.rpm"
			
			if architecture == "x86_64":
				cstream_name = "cstream-2.7.4-3.el5.rf.x86_64.rpm"
			elif architecture == "i386" or architecture == "i686":
				cstream_name = "cstream-2.7.4-3.el5.rf.i386.rpm"
			
			setuplib.copy_file("centos5.repo", "/etc/yum.repos.d/openvz.repo")
		elif centos_version == 6:
			lockfile_name = "perl-LockFile-Simple-0.207-1.el6.rf.noarch.rpm"
			
			if architecture == "x86_64":
				cstream_name = "cstream-2.7.4-3.el6.rf.x86_64.rpm"
			elif architecture == "i386" or architecture == "i686":
				cstream_name = "cstream-2.7.4-3.el6.rf.i686.rpm"
				
			setuplib.copy_file("centos6.repo", "/etc/yum.repos.d/openvz.repo")
		
		result = subprocess.call(["rpm", "--import", "http://download.openvz.org/RPM-GPG-Key-OpenVZ"], stdout=stfu, stderr=stfu)
		
		if result != 0:
			sys.stderr.write("Failed to import GPG key for the OpenVZ repository.\n")
			exit(1)
		
		sys.stdout.write("Added OpenVZ repository.\n")
		
		packages.append("vzkernel")
		result = subprocess.call(["yum", "install", "-y"] + packages, stdout=stfu, stderr=stfu)
		
		sys.stdout.write("Installed OpenVZ kernel and tools.\n")
		
		setuplib.install_remote_rpm(lockfile_name, mirrors)
		setuplib.install_remote_rpm(cstream_name, mirrors)
		setuplib.install_remote_rpm("vzdump-1.2-4.noarch.rpm", mirrors)
		
		# Set environment variable
		os.environ["PERL5LIB"] = "/usr/share/perl5/"
		
		# Update .profile and .bash_profile for the root user
		profile = open("/root/.profile", "a")
		profile.write("export PERL5LIB=/usr/share/perl5/\n")
		profile.close()
		
		profile = open("/root/.bash_profile", "a")
		profile.write("export PERL5LIB=/usr/share/perl5/\n")
		profile.close()
		
		# Update .profile and .bash_profile for the cvm user
		profile = open("/home/cvm/.profile", "a")
		profile.write("export PERL5LIB=/usr/share/perl5/\n")
		profile.close()
		
		profile = open("/home/cvm/.bash_profile", "a")
		profile.write("export PERL5LIB=/usr/share/perl5/\n")
		profile.close()
		
		# Done installing vzdump
		sys.stdout.write("Installed vzdump and dependencies.\n")
		
		# CentOS 6 apparently does not support /etc/sysctl.d anymore, so we'll just append to the
		# main sysctl config file.
		sysctl_template = open("sysctl.conf", "r")
		sysctl = open("/etc/sysctl.conf", "a")
		
		for line in sysctl_template:
			sysctl.write(line)
		
		sysctl_template.close()
		sysctl.close()
		
		result = subprocess.call(["sysctl", "-p"], stdout=stfu, stderr=stfu)
		
		if result != 0:
			# This gives an error about kernel.sysrq. Appears to have to do with this only being available
			# in the OpenVZ kernel, which isn't loaded yet. We'll just ignore it for now and move on, then
			# check at the next boot whether setting this key was successful.
			#   sys.stderr.write("Failed to load modified sysctl config. OpenVZ installation aborted.\n")
			#   exit(1)
			sys.stderr.write("WARNING: Failed to load complete sysctl config. This may indicate an error\n")
			sys.stderr.write(" during installation, but most likely it simply means that we need to boot\n")
			sys.stderr.write(" into the OpenVZ kernel before we can load the new settings.\n")
			pass
		
		sys.stdout.write("Configuration for sysctl updated.\n")
		
		setuplib.copy_file("selinux.cfg", "/etc/sysconfig/selinux")
		sys.stdout.write("SELinux disabled.\n")
		
	vzconf = open("/etc/vz/vz.conf", "a")
	vzconf.write("NEIGHBOUR_DEVS=all\n")
	vzconf.close()
	sys.stdout.write("Updated vz.conf.\n")
	
	# Install post-reboot scripts
	os.makedirs("/root/cvm")
	setuplib.copy_file("post_reboot.py", "/root/cvm/post_reboot.py", True, 0, 0, "u+rwx")
	setuplib.copy_file("setuplib.py", "/root/cvm/setuplib.py", True, 0, 0, "u+rwx")
	
	# Place reboot-required marker
	setuplib.create_file("/etc/cvm/need_reboot")
	
	# Append post-reboot script to initialization scripts.
	# In the case of rc.local, we have to leave the `exit 0` at the end intact.
	# Therefore we will insert the line before the last `exit 0`. If this line
	# is not found, we will append it at the end (as apparently `exit 0` isn't
	# required here).
	rclocal = open("/etc/rc.local", "r")
	lines = rclocal.readlines()
	rclocal.close()
	
	try:
		last = setuplib.rindex(lines, "exit 0")
	except ValueError, e:
		lines.append("rm -f /etc/cvm/need_reboot\n")
	else:
		lines.insert(last, "rm -f /etc/cvm/need_reboot\n")
	
	rclocal = open("/etc/rc.local", "w")
	rclocal.write("".join(lines))
	rclocal.close()
	
	bashrc = open("/root/.bashrc", "a")
	bashrc.write("\npython /root/cvm/post_reboot.py\n")
	bashrc.close()
	
	# Done, yay!
	sys.stdout.write("\n" + BOLD_START + "OpenVZ successfully installed." + BOLD_END + " Reboot the server and log in via SSH as root to\n")
	sys.stdout.write("complete the installation.\n")
	

stfu.close()
