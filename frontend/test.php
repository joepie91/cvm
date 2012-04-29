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
	$result = $sContainer->sNode->ssh->RunCommand("vzctl exec {$sContainer->sInternalId} cat /proc/net/dev", false);
	$values = split_whitespace($result->stdout);
	pretty_dump($values[1], $values[9]);
}

echo("Done!");
?>
<br><br>
<a href="?action=start">Start</a><br>
<a href="?action=stop">Stop</a><br>

