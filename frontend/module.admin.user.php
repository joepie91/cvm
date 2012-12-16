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

/* TODO: Add "create VPS" button. */

if(!isset($_CVM)) { die("Unauthorized."); }

try
{
	$sUserEntry = new User($router->uParameters[1]);
	
	$sVpsList = array();
	
	if($result = mysql_query_cached("SELECT * FROM containers WHERE `UserId` = '{$sUserEntry->sId}'"))
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
	
	$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/user/lookup", $locale->strings, array(
		'id'			=> $sUserEntry->sId,
		'username'		=> $sUserEntry->sUsername,
		'email'			=> $sUserEntry->sEmailAddress,
		'accesslevel'		=> $sUserEntry->sAccessLevel,
		'vpscount'		=> $sUserEntry->sVpsCount,
		'vpses'			=> $sVpsList
	));
}
catch (NotFoundException $e)
{
	$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-admin-user-title'],
		'message'	=> $locale->strings['error-admin-user-text']
	));
}

