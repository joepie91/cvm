#!/usr/bin/env python

import paramiko, socket, sys, termios, tty, select


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
						print '\r\nYou have been logged out of your container.\r\n',
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





ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

sshkey = paramiko.RSAKey.from_private_key_file('/etc/cvm/key')

ssh.connect('cvm-vz.cryto.net', username='root', pkey=sshkey)

chan = ssh.invoke_shell()
posix_shell(chan)

chan.close()
ssh.close()
