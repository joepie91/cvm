<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";
$settings['salt'] = "kAU0qM";

if($_GET['action'] == "deploy")
{
	$sContainer = new Container(3);
	
	$sContainer->Deploy();
}
else
{
	$sContainer = new Container(3);

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
	elseif($_GET['action'] == "delip")
	{
		$sContainer->RemoveIp($_GET['ip']);
	}
	elseif($_GET['action'] == "user")
	{
		$sUser = new User(1);
		
		pretty_dump($sUser);
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

