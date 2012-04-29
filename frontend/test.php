<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";

if($_GET['action'] == "deploy")
{
	$sContainer = new Container(2);
	
	$sContainer->Deploy();
}
else
{
	$sContainer = new Container(1);

	if($_GET['action'] == "start")
	{
		$sContainer->Start();
	}
	elseif($_GET['action'] == "stop")
	{
		$sContainer->Stop();
	}
	elseif($_GET['action'] == "bw")
	{
		$sContainer->UpdateTraffic();
	}
	elseif($_GET['action'] == "ip")
	{
		$sContainer->AddIp($_GET['ip']);
	}
	else
	{
		echo("idk");
	}
}

echo("Done!");
?>
<title>CVM test</title>
<br><br>
<a href="?action=start">Start</a><br>
<a href="?action=stop">Stop</a><br>

