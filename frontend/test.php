<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";

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
	$result = $sContainer->sNode->ssh->RunCommand("vzctl exec {$sContainer->sInternalId} cat /proc/net/dev | grep venet0", false);
	$lines = split_lines($result->stdout);
	
	$values = split_whitespace($lines[0]);
	pretty_dump($values);
}

echo("Done!");
?>
<title>CVM test</title>
<br><br>
<a href="?action=start">Start</a><br>
<a href="?action=stop">Stop</a><br>

