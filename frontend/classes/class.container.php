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
			'UserId'		=> "UserId",
			'VirtualizationType'	=> "VirtualizationType",
			'DiskSpace'		=> "DiskSpace",
			'GuaranteedRam'		=> "GuaranteedRam",
			'BurstableRam'		=> "BurstableRam",
			'CpuCount'		=> "CpuCount",
			'Status'		=> "Status",
			'IncomingTrafficUsed'	=> "IncomingTrafficUsed",
			'IncomingTrafficLast'	=> "IncomingTrafficLast",
			'OutgoingTrafficUsed'	=> "OutgoingTrafficUsed",
			'OutgoingTrafficLast'	=> "OutgoingTrafficLast",
			'IncomingTrafficLimit'	=> "IncomingTrafficLimit",
			'OutgoingTrafficLimit'	=> "OutgoingTrafficLimit",
			'TotalTrafficLimit'	=> "TotalTrafficLimit"
		),
		'node' => array(
			'Node'			=> "NodeId"
		),
		'template' => array(
			'Template'		=> "TemplateId"
		),
		'user' => array(
			'User'			=> "UserId"
		)
	);
	
	public function __get($name)
	{
		switch($name)
		{
			case "sRamUsed":
				return $this->GetRamUsed();
				break;
			case "sRamTotal":
				return $this->GetRamTotal();
				break;
			case "sDiskUsed":
				return $this->GetDiskUsed();
				break;
			case "sDiskTotal":
				return $this->GetDiskTotal();
				break;
			case "sBandwidthUsed":
				return $this->GetBandwidthUsed();
				break;
			case "sCurrentStatus":
				return (int)$this->GetCurrentStatus();
				break;
			case "sStatusText":
				return $this->GetStatusText();
				break;
			default:
				return null;
				break;
		}
	}
	
	public function GetBandwidthUsed()
	{
		return ($this->sOutgoingTrafficUsed + $this->IncomingTrafficUsed) / (1024 * 1024);
	}
	
	public function GetCurrentStatus()
	{
		$command = "vzctl status {$this->sInternalId}";
		
		$result = $this->sNode->ssh->RunCommandCached($command, false);
		
		if($result->returncode == 0)
		{
			$values = split_whitespace($result->stdout);
			
			if($values[4] == "running")
			{
				return CVM_STATUS_STARTED;
			}
			else
			{
				return CVM_STATUS_STOPPED;
			}
		}
	}
	
	public function GetStatusText()
	{
		$status = $this->sCurrentStatus;
	
		if($status == CVM_STATUS_STARTED)
		{
			return "running";
		}
		elseif($status == CVM_STATUS_STOPPED)
		{
			return "stopped";
		}
		elseif($status == CVM_STATUS_SUSPENDED)
		{
			return "suspended";
		}
		else
		{
			return "unknown";
		}
	}
	
	public function GetRamUsed()
	{
		$ram = $this->GetRam();
		return $ram['used'];
	}
	
	public function GetRamTotal()
	{
		$ram = $this->GetRam();
		return $ram['total'];
	}
	
	public function GetRam()
	{
		$result = $this->RunCommandCached("free -m", true);
		$lines = explode("\n", $result->stdout);
		array_shift($lines);
		
		$total_free = 0;
		$total_used = 0;
		$total_total = 0;

		foreach($lines as $line)
		{
			$line = trim($line);
			$values = split_whitespace($line);
			
			if(trim($values[0]) == "Mem:")
			{
				$total_total = $values[1];
				$total_used = $values[2];
				$total_free = $values[3];
			}
			
		}
		
		return array(
			'free'	=> $total_free,
			'used'	=> $total_used,
			'total'	=> $total_total
		);
	}
	
	public function GetDiskUsed()
	{
		$disk = $this->GetDisk();
		return $disk['used'];
	}
	
	public function GetDiskTotal()
	{
		$disk = $this->GetDisk();
		return $disk['total'];
	}
	
	public function GetDisk()
	{
		$result = $this->RunCommandCached("df -l -x tmpfs", true);
		$lines = explode("\n", $result->stdout);
		array_shift($lines);
		
		$total_free = 0;
		$total_used = 0;
		$total_total = 0;
		
		foreach($lines as $disk)
		{
			$disk = trim($disk);
			
			if(!empty($disk))
			{
				$values = split_whitespace($disk);
				$total_free += (int)$values[3] / 1024;
				$total_used += (int)$values[2] / 1024;
				$total_total += ((int)$values[2] + (int)$values[3]) / 1024;
			}
		}
		
		return array(
			'free'	=> $total_free,
			'used'	=> $total_used,
			'total'	=> $total_total
		);
	}
	
	public function RunCommand($command, $throw_exception = false)
	{
		return $this->sNode->ssh->RunCommand("vzctl exec {$this->sInternalId} $command", $throw_exception);
	}
	
	public function RunCommandCached($command, $throw_exception = false)
	{
		return $this->sNode->ssh->RunCommandCached("vzctl exec {$this->sInternalId} $command", $throw_exception);
	}
	
	public function Deploy($conf = array())
	{
		$sRootPassword = random_string(20);
		
		$this->uRootPassword = $sRootPassword;
		$this->InsertIntoDatabase();
		
		$command = shrink_command("vzctl create {$this->sInternalId}
			--ostemplate {$this->sTemplate->sTemplateName}
		");
		
		$result = $this->sNode->ssh->RunCommand($command, false);
		$result->returncode = 0;

		if($result->returncode == 0 && strpos($result->stderr, "ERROR") === false)
		{
			// TODO: set sensible defaults depending on container resource configuration
			// http://wiki.openvz.org/UBC_consistency_check
			// http://wiki.openvz.org/UBC_parameter_units
			// http://wiki.openvz.org/UBC_configuration_examples
			// http://wiki.openvz.org/UBC_parameters_table
			
			$this->uStatus = CVM_STATUS_CREATED;
			$this->InsertIntoDatabase();
			
			$sKMemSize = (isset($conf['sKMemSize'])) 		? $conf['sKMemSize'] : 		$this->sGuaranteedRam * 65000;
			$sKMemSizeLimit = (isset($conf['sKMemSizeLimit'])) 	? $conf['sKMemSizeLimit'] : 	(int)($sKMemSize * 1.1);
			$sLockedPages = (isset($conf['sLockedPages'])) 		? $conf['sLockedPages'] : 	(int)($this->sGuaranteedRam * 1.5);
			$sShmPages = (isset($conf['sShmPages'])) 		? $conf['sShmPages'] : 		$sLockedPages * 64;
			$sOomGuarPages = (isset($conf['sOomGuarPages'])) 	? $conf['sOomGuarPages'] : 	$this->sGuaranteedRam * 140;
			$sTcpSock = (isset($conf['sTcpSock'])) 			? $conf['sTcpSock'] : 		$this->sGuaranteedRam * 3;
			$sOtherSock = (isset($conf['sOtherSock'])) 		? $conf['sOtherSock'] : 	$this->sGuaranteedRam * 3;
			$sFLock = (isset($conf['sFLock'])) 			? $conf['sFLock'] : 		(int)($this->sGuaranteedRam * 0.6);
			$sFLockLimit = (isset($conf['sFLockLimit'])) 		? $conf['sFLockLimit'] : 	(int)($sFLock * 1.1);
			$sTcpSndBuf = (isset($conf['sTcpSndBuf'])) 		? $conf['sTcpSndBuf'] : 	(int)($this->sGuaranteedRam * 10000);
			$sTcpRcvBuf = (isset($conf['sTcpRcvBuf'])) 		? $conf['sTcpRcvBuf'] : 	(int)($this->sGuaranteedRam * 10000);
			$sOtherBuf = (isset($conf['sOtherBuf'])) 		? $conf['sOtherBuf'] : 		(int)($this->sGuaranteedRam * 10000);
			$sOtherBufLimit = (isset($conf['sOtherBufLimit'])) 	? $conf['sOtherBufLimit'] : 	(int)($sTcpSndBuf * 2);
			$sTcpSndBufLimit = (isset($conf['sTcpSndBufLimit'])) 	? $conf['sTcpSndBufLimit'] : 	(int)($sTcpSndBuf * 2);
			$sTcpRcvBufLimit = (isset($conf['sTcpRcvBufLimit'])) 	? $conf['sTcpRcvBufLimit'] : 	(int)($sTcpRcvBuf * 2);
			$sDgramBuf = (isset($conf['sDgramBuf'])) 		? $conf['sDgramBuf'] : 		(int)($sTcpRcvBuf / 40);
			$sNumFile = (isset($conf['sNumFile'])) 			? $conf['sNumFile'] : 		$this->sGuaranteedRam * 32;
			$sDCache = (isset($conf['sDCache'])) 			? $conf['sDCache'] : 		$this->sGuaranteedRam * 16000;
			$sDCacheLimit = (isset($conf['sDCacheLimit'])) 		? $conf['sDCacheLimit'] : 	(int)($sDCache * 1.1);
			$sAvgProc = (isset($conf['sAvgProc'])) 			? $conf['sAvgProc'] : 		(int)($this->sGuaranteedRam);
			
			$command = shrink_command("vzctl set {$this->sInternalId}
				--onboot yes
				--setmode restart
				--hostname {$this->sHostname}
				--nameserver 8.8.8.8
				--nameserver 8.8.4.4
				--numproc {$this->sCpuCount}
				--vmguarpages {$this->sGuaranteedRam}M:unlimited
				--privvmpages {$this->sBurstableRam}M:{$this->sBurstableRam}M
				--quotatime 0
				--diskspace {$this->sDiskSpace}M:{$this->sDiskSpace}M
				--userpasswd root:{$sRootPassword}
				--kmemsize {$sKMemSize}:{$sKMemSizeLimit}
				--lockedpages {$sLockedPages}:{$sLockedPages}
				--shmpages {$sShmPages}:{$sShmPages}
				--physpages 0:unlimited
				--oomguarpages {$sOomGuarPages}:unlimited
				--numtcpsock {$sTcpSock}:{$sTcpSock}
				--numflock {$sFLock}:{$sFLockLimit}
				--numpty 32:32
				--numsiginfo 512:512
				--tcpsndbuf {$sTcpSndBuf}:{$sTcpSndBufLimit}
				--tcprcvbuf {$sTcpRcvBuf}:{$sTcpRcvBufLimit}
				--othersockbuf {$sOtherBuf}:{$sOtherBufLimit}
				--dgramrcvbuf {$sDgramBuf}:{$sDgramBuf}
				--numothersock {$sOtherSock}:{$sOtherSock}
				--numfile {$sNumFile}:{$sNumFile}
				--dcachesize {$sDCache}:{$sDCacheLimit}
				--numiptent 128:128
				--diskinodes 200000:220000
				--avnumproc {$sAvgProc}:{$sAvgProc}
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
				--ram {$this->sGuaranteedRam}M
				--swap {$this->sBurstableRam}M
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
		
		pretty_dump($result);
		
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
			$values = split_whitespace(str_replace(":", " ", $lines[0]));
			
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
