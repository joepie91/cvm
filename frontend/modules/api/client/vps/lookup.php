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

if(!isset($_APP)) { die("Unauthorized."); }

try
{
	$sVps = Vps::CreateFromQuery("SELECT * FROM containers WHERE `Id` = :Id AND `UserId` = :UserId", 
		array(":Id" => $router->uParameters[1], ":UserId" => $sApiKey->sUser->sId), 0, true);
}
catch (NotFoundException $e)
{
	http_status_code(404);
	$sResponse = array("errors" => array("The specified VPS ID does not exist or is not accessible to this user."));
	return;
}

try
{
	$sRamUsed = $sVps->sRamUsed;
}
catch (SshExitException $e)
{
	$sRamUsed = 0;
}

try
{
	$sDiskUsed = $sVps->sDiskUsed;
}
catch (SshExitException $e)
{
	$sDiskUsed = 0;
}

$sVpsData = array(
	"ram_used"	=> $sRamUsed,
	"disk_used"	=> $sDiskUsed,
	"status"	=> $sVps->sStatusText
);

if($sVps->sTotalTrafficLimit == 0)
{
	/* Split traffic accounting */
	$sVpsData['traffic_in_used'] = $sVps->uIncomingTrafficUsed;
	$sVpsData['traffic_out_used'] = $sVps->uOutgoingTrafficUsed;
}
else
{
	/* Combined traffic accounting */
	$sVpsData['traffic_used'] = $sVps->uIncomingTrafficUsed + $sVps->uOutgoingTrafficUsed;
}

$sResponse = array(
	"response" => $sVpsData
);
