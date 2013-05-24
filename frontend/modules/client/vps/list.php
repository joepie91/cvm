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

if($sLoggedIn === true)
{
	$sVpsList = array();
	
	if($result = $database->CachedQuery("SELECT * FROM containers WHERE `UserId` = :UserId", array(":UserId" => $sUser->sId)))
	{
		foreach($result->data as $row)
		{
			$sVps = new Vps($row);
			
			try
			{
				$sStatus = $sVps->sStatusText;
			}
			catch (SshException $e)
			{
				$sStatus = "unknown";
			}
			
			$sVpsList[] = array(
				'id'			=> $sVps->sId,
				'hostname'		=> $sVps->sHostname,
				'node'			=> $sVps->sNode->sName,
				'node-hostname'		=> $sVps->sNode->sHostname,
				'template'		=> $sVps->sTemplate->sName,
				'diskspace'		=> number_format($sVps->sDiskSpace / 1024),
				'diskspace-unit'	=> "GB",
				'guaranteed-ram'	=> $sVps->sGuaranteedRam,
				'guaranteed-ram-unit'	=> "MB",
				'status'		=> $sStatus,
				'virtualization-type'	=> $sVps->sVirtualizationType
			);
		}
	}
	
	$sMainContents = Templater::AdvancedParse("{$sTheme}/client/vps/list", $locale->strings, array(
		'vpses'	=> $sVpsList
	));
}
else
{
	redirect("/login");
}
