<?php
/*
 * CVM is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */
 
if(!isset($_CVM)) { die("Unauthorized."); }

class SshConnector extends CPHPBaseClass
{
	public $connected = false;
	public $authenticated = false;
	public $connection = null;
	public $failed = false;
	
	public $host = "localhost";
	public $port = 22;
	public $user = "root";
	public $key = "";
	public $pubkey = "";
	public $keytype = "ssh-rsa";
	
	public $helper = "~/runhelper";
	
	private $cache = "";
	
	public function RunCommand($command, $throw_exception = false)
	{
		try
		{
			return $this->DoCommand($command, $throw_exception);
		}
		catch (SshConnectException $e)
		{
			$error = $e->getMessage();
			$command = implode(" ", $command);
			$this->failed = true;
			throw new SshConnectException("Could not run command {$command}: Failed to connect: {$error}");
		}
		catch (SshAuthException $e)
		{
			$error = $e->getMessage();
			$command = implode(" ", $command);
			$this->failed = true;
			throw new SshConnectException("Could not run command {$command}: Failed to authenticate: {$error}");
		}
	}
	
	public function RunCommandCached($command, $throw_exception = false)
	{
		if(!isset($this->cache[serialize($command)]))
		{
			$result = $this->RunCommand($command, $throw_exception);
			$this->cache[serialize($command)] = $result;
			return $result;
		}
		else
		{
			return $this->cache[serialize($command)];
		}
	}
	
	public function Connect()
	{
		/* TODO: TIME_WAIT status for a previous socket on the same port may cause issues
		 *       when attempting to restart the command daemon. There is currently no way
		 *       to detect this from the code, and it makes all subsequent requests fail
		 *       (silently?) because the tunnel is available but nothing is listening on
		 *       the other end. This kind of edge case should be detected and dealt with.
		 *       A browser displays a 'no data received' error in this case. */
		if($this->failed)
		{
			throw new SshConnectException("A previous connection attempt failed.");
		}
		
		$sHost = escapeshellarg($this->host);
		$sUser = escapeshellarg($this->user);
		$sPort = $this->tunnel_port = $this->node->uTunnelPort = $this->ChoosePort();
		$sKeyFile = escapeshellarg($this->key);
		$this->node->uTunnelKey = $this->tunnel_key = random_string(16);
		$sSessionKey = escapeshellarg($this->node->uTunnelKey);
		
		$command = "python /etc/cvm/start_tunnel.py {$sHost} {$sUser} {$sPort} {$sKeyFile} {$sSessionKey}";
		
		$steps = array();
		
		foreach(debug_backtrace() as $step)
		{
			try
			{
				$allargs = implode(", ", $step['args']);
			}
			catch (Exception $e)
			{
				$allargs = "[unserializable]";
			}
			
			$steps[] = "{$step['file']}:{$step['line']} => {$step['class']}{$step['type']}{$step['function']}({$allargs})";
		}
		
		cphp_debug_snapshot(array(
			"action" => "start tunnel",
			"db-tunnelkey" => $this->node->sTunnelKey,
			"db-utunnelkey" => $this->node->uTunnelKey,
			"ssh-tunnelkey" => $this->tunnel_key,
			"arg-tunnelkey" => $sSessionKey,
			"trace" => $steps
		));
		
		exec($command, $output, $returncode);
		
		if($returncode === 0)
		{
			/* autossh returns before the SSH connection has actually been established. We'll make the
			 * script sleep until a connection has been established, with a timeout of 10 seconds, after
			 * which an exception is raised. The polling interval is 100ms. */
			
			$start_time = time();
			
			while(true)
			{
				if(time() > $start_time + 10)
				{
					throw new SshConnectException("The SSH tunnel could not be fully established within the timeout period.");
				}
				
				if($pollsock = @fsockopen("localhost", $this->tunnel_port, $errno, $errstr, 1))
				{
					/* The tunnel has been fully established. */
					
					fclose($pollsock);
					break;
				}
				
				usleep(100000);
			}
			
			cphp_debug_snapshot(array(
				"action" => "pre insert db",
				"db-tunnelkey" => $this->node->sTunnelKey,
				"db-utunnelkey" => $this->node->uTunnelKey,
				"ssh-tunnelkey" => $this->tunnel_key,
				"arg-tunnelkey" => $sSessionKey
			));
			
			$this->node->InsertIntoDatabase();
			
			cphp_debug_snapshot(array(
				"action" => "inserted db",
				"db-tunnelkey" => $this->node->sTunnelKey,
				"db-utunnelkey" => $this->node->uTunnelKey,
				"ssh-tunnelkey" => $this->tunnel_key,
				"arg-tunnelkey" => $sSessionKey
			));
			
			return true;
		}
		else
		{
			throw new SshConnectException("Could not establish tunnel to {$this->host}:{$this->port}.");
		}
	}
	
	private function ChoosePort()
	{
		try
		{
			$sPorts = array();
			
			foreach(Node::CreateFromQuery("SELECT * FROM nodes WHERE `TunnelPort` != 0") as $node)
			{
				$sPorts[] = $node->sTunnelPort;
				$sPorts[] = $node->sTunnelPort + 1;
				$sPorts[] = $node->sTunnelPort + 2;
			}
			
			/* TODO: Figure out a more intelligent way of choosing ports. */
			$start = max($sPorts) + 1;
		}
		catch (NotFoundException $e)
		{
			$start = 2000;
		}
		
		$current = $start;
		
		while(true)
		{
			if($current > 65534)
			{
				throw new SshConnectException("No free tunnel ports left.");
			}
			
			if(!$this->TestPort($current))
			{
				if(!$this->TestPort($current + 1))
				{
					if(!$this->TestPort($current + 2))
					{
						break;
					}
					else
					{
						$current = $current + 3;
					}
				}
				else
				{
					$current = $current + 2;
				}
			}
			else
			{
				$current = $current + 1;
			}
		}
		
		return $current;
	}
	
	private function TestPort($port)
	{
		if($testsock = @fsockopen("localhost", $port, $errno, $errstr, 1))
		{
			fclose($testsock);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	private function DoCommand($command, $throw_exception, $allow_retry = true)
	{
		cphp_debug_snapshot(array(
			"action" => "pre run command",
			"db-tunnelkey" => $this->node->sTunnelKey,
			"db-utunnelkey" => $this->node->uTunnelKey,
			"ssh-tunnelkey" => $this->tunnel_key,
			"command" => $command,
			"allow-retry" => $allow_retry
		));
		
		$cmd = urlencode(json_encode($command));
		$url = "http://localhost:{$this->tunnel_port}/?key={$this->tunnel_key}&command={$cmd}";
		
		$context = stream_context_create(array(
			'http' => array(
				'timeout' => 2.0
			)
		));

		$response = @file($url, 0, $context);

		cphp_debug_snapshot(array(
			"action" => "post run command",
			"db-tunnelkey" => $this->node->sTunnelKey,
			"db-utunnelkey" => $this->node->uTunnelKey,
			"ssh-tunnelkey" => $this->tunnel_key,
			"command" => $command,
			"allow-retry" => $allow_retry,
			"response" => $response
		));

		if($response === false)
		{
			/* Determine why the connection failed, and what we need to do to fix it. */
			if($testsock = @fsockopen("localhost", $this->tunnel_port, $errno, $errstr, 1))
			{
				/* The socket works fine. */
				fclose($testsock);
				
				/* Since the socket works but we can't make a request, there is most
				 * likely a serious problem with the command daemon (stuck, crashed,
				 * etc.) We'll throw an exception. TODO: Log error. */
				$this->failed = true;
				throw new SshCommandException("The command daemon is unavailable.");
			}
			else
			{
				/* The tunnel is gone for some reason. Either the connection broke
				 * and autossh is busy reconnecting, or autossh broke entirely. We
				 * will attempt to connect to the monitoring port to see if autossh
				 * is still running or not. */
				if($testsock = @fsockopen("localhost", ($this->tunnel_port + 2), $errno, $errstr, 1))
				{
					/* The socket works fine. */
					fclose($testsock);
					
					/* Most likely autossh is very busy trying to reconnect to the node. We'll throw a
					 * connection exception for now. TODO: Consider waiting with a specified timeout. */
					$this->failed = true;
					throw new SshConnectException("The SSH connection to this node is currently unavailable.");
				}
				else
				{
					if($allow_retry)
					{
						$this->Connect();
						$res = $this->DoCommand($command, $throw_exception, false);
					}
					else
					{
						$this->failed = true;
						throw new SshConnectException("Could not connect to node.");
						/* TODO: Log error, this is probably very bad. */
					}
				}
			}
		}
		else
		{
			$response = json_decode(implode("", $response));
		}
		
		if($response->returncode != 0 && $throw_exception === true)
		{
			throw new SshExitException("Non-zero exit code returned: {$response->stderr}", $response->returncode);
		}
		
		return $response;
	}
}
