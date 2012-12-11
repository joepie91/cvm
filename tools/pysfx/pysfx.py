#!/usr/bin/env python

import zlib, base64, sys, argparse, os
from gzipreader import GzipReader
from b64reader import Base64Reader

parser = argparse.ArgumentParser(description="Creates an SFX from a specified archive or file.")
parser.add_argument("-a", help="Treat the input file as a tar.gz archive that needs to be extracted upon running the SFX.", action="store_true", dest="is_archive")
parser.add_argument("-s", help="Define a command to be run after extraction of the SFX. %%NAME will be replaced with the path of the extracted file or folder. "
			       "For archives, the working directory is set to the extraction directory.", action="store", dest="command")
parser.add_argument("input_file", metavar="INPUTFILE", type=str, nargs=1, help="The file to read from. Use a dash (-) to read from STDIN instead.")
parser.add_argument("output_file", metavar="OUTPUTFILE", type=str, nargs=1, help="The file to write to. Use a dash (-) to write to STDOUT instead.")
options = vars(parser.parse_args())

if options['input_file'][0] == "-":
	infile = sys.stdin
	extension = "dat"
else:
	infile = open(options['input_file'][0], "rb")
	extension = os.path.splitext(options['input_file'][0])

if options['output_file'][0] == "-":
	outfile = sys.stdout
else:
	outfile = open(options['output_file'][0], "wb")

if options['is_archive'] == True:
	is_archive = "True"
	extension = "tar.gz"
else:
	is_archive = "False"

if options['command']:
	run_after_extract = "True"
	command = options['command']
else:
	run_after_extract = "False"
	command = ""

template = open("%s/unpack.template" % os.path.dirname(__file__), "r")

variables = {
	"run_after_extract": run_after_extract,
	"targz": is_archive,
	"extension": extension,
	"command": command
}

for curline in template:
	if curline.startswith('"""EOFDATA'):
		# Found the EOF data marker, insert packed data before 
		# moving on with the next line.
		outfile.write(curline)
		
		data = b""
		reader = Base64Reader(GzipReader(infile))
		chunk_size = 128

		while True:
			chunk = reader.read(chunk_size)
			
			if chunk == "":
				break
			
			outfile.write(chunk + "\n")
	else:
		if "{%" in curline:
			for variable_key, variable_value in variables.iteritems():
				curline = curline.replace("{%%%s}" % variable_key, variable_value)
		
		outfile.write(curline)

outfile.close()
