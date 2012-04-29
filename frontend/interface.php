<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";
$settings['salt'] = "kAU0qM";

$sContainer = new Container(1);

echo(Templater::InlineRender("main", $locale->strings, array(
	'server-location'	=> $sContainer->sNode->sPhysicalLocation,
	'operating-system'	=> $sContainer->sTemplate->sName,
	'guaranteed-ram'	=> "{$sContainer->sGuaranteedRam}MB",
	'burstable-ram'		=> "{$sContainer->sBurstableRam}MB",
	'disk-space'		=> "{$sContainer->sDiskSpace}MB",
	'total-traffic-limit'	=> "{$sContainer->sTotalTrafficLimit} bytes",
	'bandwidth-limit'	=> "100mbit",
	'status'		=> Templater::InlineRender("status.{$sContainer->sStatusText}", $locale->strings)
)));

?>
