import os, subprocess, sys, re, json, setuplib, time

BOLD_START = '\033[1m'
BOLD_END = '\033[0m'

beancounter_keys = ["KMEMSIZE", "LOCKEDPAGES", "PRIVVMPAGES", "SHMPAGES", "NUMPROC", "PHYSPAGES", 
	"VMGUARPAGES", "OOMGUARPAGES", "NUMTCPSOCK", "NUMFLOCK", "NUMPTY", "NUMSIGINFO", 
	"TCPSNDBUF", "TCPRCVBUF", "OTHERSOCKBUF", "DGRAMRCVBUF", "NUMOTHERSOCK", 
	"DCACHESIZE", "NUMFILE", "AVNUMPROC", "NUMIPTENT", "DISKSPACE", "DISKINODES", 
	"QUOTATIME", "CPUUNITS"]

def run():
	# Check if the OpenVZ kernel is active
	r, w = os.pipe()
	subprocess.call(["ps", "ax"], stdout=w)
	found = False

	for process in os.read(r, 134217728).splitlines():
		if "vzmond" in process:
			found = True
			
	if found == False:
		sys.stderr.write("WARNING: OpenVZ kernel not detected as being active. Restart the\n")
		sys.stderr.write(" system with the OpenVZ kernel enabled and manually run the exporter.\n")
		return False
	
	# Start exporting
	
	containers = {}
	
	r, w = os.pipe()
	subprocess.call(["vzlist", "-Ha", "-o", "ctid,status"], stdout=w)

	for line in os.read(r, 134217728).splitlines():
		ctid, status = line.split()
		
		iplist = []
		nameservers = []
		beancounters = {}
		parameters = {}
		
		autorun = False
		hostname = None
		template = None
		
		for line in open("/etc/vz/conf/%s.conf" % ctid, "r"):
			if line.startswith("#"):
				continue
			
			if line.strip() == "":
				continue
			
			key, value = line.split("=", 1)
			
			key = key.strip()
			value = value.strip()[1:-1]
			
			if key in ["VE_ROOT", "VE_PRIVATE", "ORIGIN_SAMPLE"]:
				# We don't really care about these, so we can ignore them.
				continue
				
			elif key in beancounter_keys:
				# This is a beancounter.
				beancounters[key.lower()] = value
			
			elif key == "ONBOOT":
				autorun = (value == "yes")
				
			elif key == "NAMESERVER":
				nameservers += value.split()
			
			elif key == "IP_ADDRESS":
				iplist += value.split()
				
			elif key == "HOSTNAME":
				hostname = value
				
			elif key == "OSTEMPLATE":
				template = value
			
			else:
				parameters[key] = value
		
		containers[ctid] = {
			'ip_addresses': iplist,
			'beancounters': beancounters,
			'hostname': hostname,
			'autorun': autorun,
			'nameservers': nameservers,
			'parameters': parameters,
			'status': status,
			'template': template
		}
	
	setuplib.create_directory("/etc/cvm/exported", True, 0, 0, "u+rwx")
	
	export_path = "/etc/cvm/exported/%s.openvz" % time.strftime("%Y%m%d_%H%M%S")
	
	setuplib.create_file(export_path, json.dumps({"openvz": containers}), 0, 0, "u+rwx")
	
	sys.stdout.write("\n" + BOLD_START + "Your exported data has been stored in %s." % export_path + BOLD_END + "\n")
