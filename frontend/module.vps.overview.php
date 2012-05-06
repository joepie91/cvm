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

if(!empty($router->uParameters[2]))
{
	if($router->uParameters[2] == "start")
	{
		require("submodule.start.php");
	}
	elseif($router->uParameters[2] == "stop")
	{
		require("submodule.stop.php");
	}
	elseif($router->uParameters[2] == "restart")
	{
		require("submodule.restart.php");
	}
}

$sPageContents = Templater::InlineRender("vps.overview", $locale->strings, array(
	'id'			=> $sContainer->sId,
	'server-location'	=> $sContainer->sNode->sPhysicalLocation,
	'operating-system'	=> $sContainer->sTemplate->sName,
	'guaranteed-ram'	=> "{$sContainer->sGuaranteedRam}MB",
	'burstable-ram'		=> "{$sContainer->sBurstableRam}MB",
	'disk-space'		=> "{$sContainer->sDiskSpace}MB",
	'total-traffic-limit'	=> "{$sContainer->sTotalTrafficLimit} bytes",
	'bandwidth-limit'	=> "100mbit",
	'status'		=> $sContainer->sStatusText
));
?>
