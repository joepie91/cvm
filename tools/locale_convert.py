#!/usr/bin/env python

import sys, json, re

f = open(sys.argv[1])
output = {}

for line in f:
	if not line.startswith("#"):
		data = re.search("(.+?[^\\\]);(.+)", line)
		
		if data is not None:
			key = data.group(1).replace("\;", ";").strip()
			val = data.group(2).replace("\;", ";").strip()
			
			output[key] = {
				"message": val,
				"description": ""
			}
			
print json.dumps(output)
