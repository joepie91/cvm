import zlib, sys

class GzipReader(object):
	source = None
	cobj = None
	done = False
	buff = ""
	
	def __init__(self, source):
		self.source = source
		self.cobj = zlib.compressobj()
	
	def read(self, size = -1):
		if self.done == False:
			if size < 0:
				data = self.source.read()
				return self.cobj.compress(data) + self.cobj.flush(zlib.Z_FINISH)
			else:
				# Keep reading and compressing until we have something to return.
				while len(self.buff) < size:
					data = self.source.read(size)
					
					if data == "":
						# Process the last data left in the compressor buffer.
						self.buff += self.cobj.flush(zlib.Z_FINISH)
						
						# Mark as done to prevent calling flush(zlib.Z_FINISH) twice.
						self.done = True
						
						return self.buff
					
					self.buff += self.cobj.compress(data)
				
				returndata = self.buff[:size]
				self.buff = self.buff[size:]
				return returndata
		else:
			return ""

	def flush(self):
		pass
		
	def write(self, data):
		pass
	
	def close(self):
		self.source.close()
