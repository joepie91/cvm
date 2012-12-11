import base64, sys, math

class Base64Reader(object):
	def __init__(self, source):
		self.source = source
		self.buff = ""
		self.done = False
	
	def read(self, size = -1):
		if size < 0:
			return base64.b64encode(self.source.read())
		else:
			if self.done == False:
				if len(self.buff) < size:
					actual_size = int(math.ceil(size / 3) * 3)
					data = self.source.read(actual_size)
					
					if data == "":
						self.done = True
						return self.buff
					
					# TODO: Investigate whether the possibility exists that we get the wrong amount
					#       of bytes from the source read.
					self.buff += base64.b64encode(data)
					
					if len(self.buff) > size:
						returndata = self.buff[:size]
						self.buff = self.buff[size:]
					else:
						returndata = self.buff
						self.buff = ""
						
					return returndata
				else:
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
