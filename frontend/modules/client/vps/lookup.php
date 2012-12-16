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
		require("modules/client/vps/action/start.php");
	}
	elseif($router->uParameters[2] == "stop")
	{
		require("modules/client/vps/action/stop.php");
	}
	elseif($router->uParameters[2] == "restart")
	{
		require("modules/client/vps/action/restart.php");
	}
}

if($sVps->sTotalTrafficLimit != 0)
{
	$sTrafficLimit = $sVps->sTotalTrafficLimit;
}
else
{
	$sTrafficLimit = $sVps->sIncomingTrafficLimit + $sVps->sOutgoingTrafficLimit;
}

$sVariables = array(
	'id'			=> $sVps->sId,
	'server-location'	=> $sVps->sNode->sPhysicalLocation,
	'operating-system'	=> $sVps->sTemplate->sName,
	'guaranteed-ram'	=> "{$sVps->sGuaranteedRam}MB",
	'burstable-ram'		=> "{$sVps->sBurstableRam}MB",
	'disk-space'		=> "{$sVps->sDiskSpace}MB",
	'total-traffic-limit'	=> format_size($sVps->sTotalTrafficLimit, 1024, true, 0) . "B",
	'incoming-traffic-limit'=> format_size($sVps->sIncomingTrafficLimit, 1024, true, 0) . "B",
	'outgoing-traffic-limit'=> format_size($sVps->sOutgoingTrafficLimit, 1024, true, 0) . "B",
	'bandwidth-limit'	=> "100mbit",
	'status'		=> $sVps->sStatusText,
	'traffic-used'		=> number_format(($sVps->sIncomingTrafficUsed + $sVps->sOutgoingTrafficUsed) / 1024 / 1024 / 1024, 2),
	'traffic-total'		=> number_format($sTrafficLimit / 1024 / 1024 / 1024, 0),
	'traffic-percentage'	=> number_format(($sVps->sIncomingTrafficUsed + $sVps->sOutgoingTrafficUsed) / $sTrafficLimit, 2),
	'traffic-unit'		=> "GB"
);

try
{
	$sVariables = array_merge($sVariables, array(
		'disk-used'		=> number_format($sVps->sDiskUsed / 1024, 2),
		'disk-total'		=> number_format($sVps->sDiskTotal / 1024, 2),
		'disk-percentage'	=> ($sVps->sDiskTotal == 0) ? 0 : number_format(($sVps->sDiskUsed / $sVps->sDiskTotal) * 100, 2),
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
		'ram-used'		=> $sVps->sRamUsed,
		'ram-total'		=> $sVps->sRamTotal,
		'ram-percentage'	=> ($sVps->sRamTotal == 0) ? 0 : number_format(($sVps->sRamUsed / $sVps->sRamTotal) * 100, 2),
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


$sPageContents = Templater::AdvancedParse("{$sTheme}/client/vps/lookup", $locale->strings, $sVariables);

