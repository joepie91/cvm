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

$sVariables = array(
	'id'			=> $sContainer->sId,
	'server-location'	=> $sContainer->sNode->sPhysicalLocation,
	'operating-system'	=> $sContainer->sTemplate->sName,
	'guaranteed-ram'	=> "{$sContainer->sGuaranteedRam}MB",
	'burstable-ram'		=> "{$sContainer->sBurstableRam}MB",
	'disk-space'		=> "{$sContainer->sDiskSpace}MB",
	'total-traffic-limit'	=> "{$sContainer->sTotalTrafficLimit} bytes",
	'bandwidth-limit'	=> "100mbit",
	'status'		=> $sContainer->sStatusText,
	'traffic-used'		=> number_format(($sContainer->sIncomingTrafficUsed + $sContainer->sOutgoingTrafficUsed) / 1024 / 1024 / 1024, 2),
	'traffic-total'		=> number_format(($sContainer->sIncomingTrafficLimit + $sContainer->sOutgoingTrafficLimit) / 1024 / 1024 / 1024, 0),
	'traffic-percentage'	=> number_format(($sContainer->sIncomingTrafficUsed + $sContainer->sOutgoingTrafficUsed) / ($sContainer->sIncomingTrafficLimit + $sContainer->sOutgoingTrafficLimit), 2),
	'traffic-unit'		=> "GB"
);

try
{
	$sVariables = array_merge($sVariables, array(
		'disk-used'		=> number_format($sContainer->sDiskUsed / 1024, 2),
		'disk-total'		=> number_format($sContainer->sDiskTotal / 1024, 2),
		'disk-percentage'	=> number_format(($sContainer->sDiskUsed / $sContainer->sDiskTotal) * 100, 2),
		'disk-unit'		=> "GB"
	));
}
catch (SshExitException $e)
{
	$sVariables = array_merge($sVariables, array(
		'disk-used'		=> 0,
		'disk-total'		=> 0,
		'disk-percentage'	=> 0,
		'disk-unit'		=> "GB"
	));
}

try
{
	$sVariables = array_merge($sVariables, array(
		'ram-used'		=> $sContainer->sRamUsed,
		'ram-total'		=> $sContainer->sRamTotal,
		'ram-percentage'	=> number_format(($sContainer->sRamUsed / $sContainer->sRamTotal) * 100, 2),
		'ram-unit'		=> "MB"
	));
}
catch (SshExitException $e)
{
	$sVariables = array_merge($sVariables, array(
		'ram-used'		=> 0,
		'ram-total'		=> 0,
		'ram-percentage'	=> 0,
		'ram-unit'		=> "MB"
	));
}


$sPageContents = Templater::AdvancedParse("vps.overview", $locale->strings, $sVariables);

