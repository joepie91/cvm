#!/usr/bin/env python

import zlib, base64, sys, os, random, shlex, subprocess

run_after_extract = {%run_after_extract}
targz = {%targz}
extension = "{%extension}"
command = "{%command}"

try:
	if sys.argv[1] != "-q":
		quiet = True
	else:
		quiet = False
except IndexError:
	quiet = False
	
if quiet == False:
	sys.stdout.write("PySFX 1.0 by Sven Slootweg    http://cryto.net/pysfx\n")
	sys.stdout.write("PySFX may be reused, modified, and redistributed freely without restriction under the WTFPL.\n\n")
	
identifier = "pysfx-%s" % "".join(["abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"[random.randint(0, 61)] for i in xrange(0, 16)])
directory_destination = "/var/tmp/%s" % identifier
file_destination = "/var/tmp/%s.%s" % (identifier, extension)

if targz == True:
	name = "/var/tmp/%s"
else:
	name = "/var/tmp/%s.%s" % (identifier, extension)

reader = open(__file__, "rb")
reading_data = False

writer = open(file_destination, "wb")

dobj = zlib.decompressobj()

total_bytes = 0
original_bytes = 0

for line in reader:
	if line.startswith('"""'):
		reading_data = False
	
	if reading_data == True:
		data = dobj.decompress(base64.b64decode(line.rstrip("\r\n")))
		writer.write(data)
		total_bytes += (len(line) - 1)
		original_bytes += len(data)
	
	if line.startswith('"""EOFDATA'):
		reading_data = True

writer.write(dobj.flush())
writer.close()

reader.close()

if quiet == False:
	sys.stdout.write("Processed %d bytes, of which %d bytes were written to %s.\n" % (total_bytes, original_bytes, file_destination))

if targz == True:
	stfu = open(os.devnull, 'w')
	
	if quiet == False:
		sys.stdout.write("Unpacking archive...\n")
	
	os.makedirs(directory_destination)
	
	result = subprocess.call(["tar", "-xzf", file_destination, "-C", directory_destination], stdout=stfu, stderr=stfu)
	
	if result != 0:
		sys.stderr.write("Extraction of inner archive failed. The file may be corrupted.\n")
		exit(1)

if run_after_extract == True:
	tokens = shlex.split(command)
	result = subprocess.call(tokens, cwd=directory_destination)
	
	if result != 0:
		sys.stderr.write("Autorun command failed. The file may be corrupted.\n")
		exit(1)
	
"""EOFDATA
"""
