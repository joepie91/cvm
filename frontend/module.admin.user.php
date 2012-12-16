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
	
	$sContainerList = array();
	
	if($result = mysql_query_cached("SELECT * FROM containers WHERE `UserId` = '{$sUserEntry->sId}'"))
	{
		foreach($result->data as $row)
		{
			$sContainer = new Container($row);
			
			try
			{
				$sStatus = $sContainer->sStatusText;
			}
			catch (SshException $e)
			{
				$sStatus = "unknown";
			}
			
			$sContainerList[] = array(
				'id'			=> $sContainer->sId,
				'hostname'		=> $sContainer->sHostname,
				'node'			=> $sContainer->sNode->sName,
				'node-hostname'		=> $sContainer->sNode->sHostname,
				'template'		=> $sContainer->sTemplate->sName,
				'diskspace'		=> number_format($sContainer->sDiskSpace / 1024),
				'diskspace-unit'	=> "GB",
				'guaranteed-ram'	=> $sContainer->sGuaranteedRam,
				'guaranteed-ram-unit'	=> "MB",
				'status'		=> $sStatus,
				'virtualization-type'	=> $sContainer->sVirtualizationType
			);
		}
	}
	
	$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/user/lookup", $locale->strings, array(
		'id'			=> $sUserEntry->sId,
		'username'		=> $sUserEntry->sUsername,
		'email'			=> $sUserEntry->sEmailAddress,
		'accesslevel'		=> $sUserEntry->sAccessLevel,
		'containercount'	=> $sUserEntry->sContainerCount,
		'containers'		=> $sContainerList
	));
}
catch (NotFoundException $e)
{
	$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-admin-user-title'],
		'message'	=> $locale->strings['error-admin-user-text']
	));
}

