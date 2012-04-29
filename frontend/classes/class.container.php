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

class Container extends CPHPDatabaseRecordClass
{
	public $table_name = "containers";
	public $fill_query = "SELECT * FROM containers WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM containers WHERE `Id` = '%d'";
	
	public $prototype = array(
		'string' => array(
			'Hostname'		=> "Hostname",
			'InternalId'		=> "InternalId",
			'RootPassword'		=> "RootPassword"
		),
		'numeric' => array(
			'NodeId'		=> "NodeId",
			'TemplateId'		=> "TemplateId",
			'VirtualizationType'	=> "VirtualizationType",
			'DiskSpace'		=> "DiskSpace",
			'GuaranteedRam'		=> "GuaranteedRam",
			'BurstableRam'		=> "BurstableRam",
			'CpuCount'		=> "CpuCount",
			'Status'		=> "Status",
			'IncomingTrafficUsed'	=> "IncomingTrafficUsed",
			'IncomingTrafficLast'	=> "IncomingTrafficLast",
			'OutgoingTrafficUsed'	=> "OutgoingTrafficUsed",
			'OutgoingTrafficLast'	=> "OutgoingTrafficLast"
		),
		'node' => array(
			'Node'			=> "NodeId"
		),
		'template' => array(
			'Template'		=> "TemplateId"
		)
	);
	
	public function __get($name)
	{
		switch($name)
		{
			case "sRamUsed":
				return $this->GetRamUsed();
				break;
			case "sDiskUsed":
				return $this->GetDiskUsed();
				break;
			case "sBandwidthUsed":
				return $this->GetBandwidthUsed();
				break;
			default:
				return null;
				break;
		}
	}
	
	public function Deploy()
	{
		$sGuaranteedRamPages = $this->sGuaranteedRam * 256;
		$sBurstableRamPages = $this->sBurstableRam * 256;
		$sRootPassword = random_string(20);
		
		$this->uRootPassword = $sRootPassword;
		$this->InsertIntoDatabase();
		
		$command = shrink_command("vzctl create {$this->sInternalId}
			--ostemplate {$this->sTemplate->sTemplateName}
		");
		
		$result = $this->sNode->ssh->RunCommand($command, false);

		if($result->returncode == 0)
		{
			// TODO: set sensible values depending on container resource configuration
			// http://wiki.openvz.org/UBC_consistency_check
			
			$this->uStatus = CVM_STATUS_CREATED;
			$this->InsertIntoDatabase();
			
			$command = shrink_command("vzctl set {$this->sInternalId}
				--onboot yes
				--setmode restart
				--hostname {$this->sHostname}
				--nameserver 8.8.8.8
				--nameserver 8.8.4.4
				--numproc {$this->sCpuCount}
				--vmguarpages {$sGuaranteedRamPages}:unlimited
				--privvmpages {$sBurstableRamPages}:{$sBurstableRamPages}
				--quotatime 0
				--diskspace {$this->sDiskSpace}M:{$this->sDiskSpace}M
				--userpasswd root:{$sRootPassword}
				--kmemsize 14372700:14790164
				--lockedpages 256:256
				--shmpages 21504:21504
				--physpages 0:unlimited
				--oomguarpages 26112:unlimited
				--numtcpsock 360:360
				--numflock 188:206
				--numpty 16:16
				--numsiginfo 256:256
				--tcpsndbuf 1720320:2703360
				--tcprcvbuf 1720320:2703360
				--othersockbuf 1126080:2097152
				--dgramrcvbuf 262144:262144
				--numothersock 360:360
				--numfile 9312:9312
				--dcachesize 3409920:3624960
				--numiptent 128:128
				--diskinodes 200000:220000
				--avnumproc 180:180
				--save
			");
			
			/* 
			This may be useful if we turn out to have a kernel that supports vswap
			
			$command = shrink_command("vzctl set {$this->sInternalId}
				--onboot yes
				--setmode restart
				--hostname {$this->sHostname}
				--nameserver 8.8.8.8
				--nameserver 8.8.4.4
				--numproc {$this->sCpuCount}
				--quotatime 0
				--diskspace {$this->sDiskSpace}M:{$this->sDiskSpace}M
				--userpasswd root:{$sRootPassword}
				--numtcpsock 360:360
				--numflock 188:206
				--numpty 16:16
				--numsiginfo 256:256
				--tcpsndbuf 1720320:2703360
				--tcprcvbuf 1720320:2703360
				--othersockbuf 1126080:2097152
				--dgramrcvbuf 262144:262144
				--numothersock 360:360
				--numfile 9312:9312
				--dcachesize 3409920:3624960
				--numiptent 128:128
				--diskinodes 200000:220000
				--avnumproc 180:180
				--ram {$this->sGuaranteedRam}
				--swap {$this->sBurstableRam}
				--save
			");*/

			$result = $this->sNode->ssh->RunCommand($command, false);
			
			if($result->returncode == 0)
			{
				$this->uStatus = CVM_STATUS_CONFIGURED;
				$this->InsertIntoDatabase();
				
				return true;
			}
			else
			{
				throw new ContainerConfigureException($result->stderr, $result->returncode, $this->sInternalId);
			}
		}
		else
		{
			throw new ContainerCreateException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function Start()
	{
		$command = "vzctl start {$this->sInternalId}";
		$result = $this->sNode->ssh->RunCommand($command, false);
		
		if($result->returncode == 0)
		{
			$this->uStatus = CVM_STATUS_STARTED;
			$this->InsertIntoDatabase();
			return true;
		}
		else
		{
			throw new ContainerStartException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function Stop()
	{
		$command = "vzctl stop {$this->sInternalId}";
		$result = $this->sNode->ssh->RunCommand($command, false);
		
		// vzctl is retarded enough to return exit status 0 when the command fails because the container isn't running, so we'll have to check the stderr for specific error string(s) as well. come on guys, it's 2012.
		if($result->returncode == 0 && strpos($result->stderr, "Unable to stop") === false)
		{
			$this->uStatus = CVM_STATUS_STOPPED;
			$this->InsertIntoDatabase();
			return true;
		}
		else
		{
			throw new ContainerStopException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function AddIp($ip)
	{
		$command = shrink_command("vzctl set {$this->sInternalId}
			--ipadd {$ip}
			--save
		");
		
		$result = $this->sNode->ssh->RunCommand($command, false);
		
		if($result->returncode == 0)
		{
			return true;
		}
		else
		{
			throw new ContainerIpAddException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function RemoveIp($ip)
	{
		$command = shrink_command("vzctl set {$this->sInternalId}
			--ipdel {$ip}
			--save
		");
		
		$result = $this->sNode->ssh->RunCommand($command, false);
		
		if($result->returncode == 0)
		{
			return true;
		}
		else
		{
			throw new ContainerIpRemoveException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function UpdateTraffic()
	{
		$result = $this->sNode->ssh->RunCommand("vzctl exec {$this->sInternalId} cat /proc/net/dev | grep venet0", false);
		
		if($result->returncode == 0)
		{
			$lines = split_lines($result->stdout);
			$values = split_whitespace($lines[0]);
			
			$uIncoming = $values[1];
			$uOutgoing = $values[9];
			
			if($uIncoming < (int)$this->sIncomingTrafficLast || $uOutgoing < (int)$this->sOutgoingTrafficLast)
			{
				// the counter has reset (wrap-around, reboot, etc.)
				$uNewIncoming = $uIncoming;
				$uNewOutgoing = $uOutgoing;
			}
			else
			{
				$uNewIncoming = $uIncoming - $this->sIncomingTrafficLast;
				$uNewOutgoing = $uOutgoing - $this->sOutgoingTrafficLast;
			}
			
			$this->uIncomingTrafficUsed = $this->sIncomingTrafficUsed + $uNewIncoming;
			$this->uOutgoingTrafficUsed = $this->sOutgoingTrafficUsed + $uNewOutgoing;
			
			$this->uIncomingTrafficLast = $uIncoming;
			$this->uOutgoingTrafficLast = $uOutgoing;
			
			$this->InsertIntoDatabase();
		}
		else
		{
			throw new ContainerTrafficRetrieveException($result->stderr, $result->returncode, $this->sInternalId);
		}
	}
	
	public function EnableTunTap()
	{
		// TODO: Finish EnableTunTap function, check whether tun module is available on host
		$command = "vzctl set {$this->sInternalId} --devnodes net/tun:rw --save";
	}
}

?>
