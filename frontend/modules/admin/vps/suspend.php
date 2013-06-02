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
	$sUser->RequireAccessLevel(20);
	
	$sVps = new Vps($router->uParameters[1]);
	
	if(isset($_POST['submit']))
	{
		if($_POST['action'] == "suspend")
		{
			$sVps->Suspend();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-suspend-success-title'],
				'message'	=> $locale->strings['error-suspend-success-text']
			));
		}
		elseif($_POST['action'] == "unsuspend")
		{
			$sVps->Unsuspend();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-unsuspend-success-title'],
				'message'	=> $locale->strings['error-unsuspend-success-text']
			));
		}
		
		$sVps->RefreshData();
		
		/* TODO: Flash message and redirect to VPS lookup page. */
	}
	
	$sSuspended = ($sVps->sStatus == CVM_STATUS_SUSPENDED) ? true : false;
	
	$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/vps/suspend", $locale->strings, array(
		'id'		=> $sVps->sId,
		'suspended'	=> $sSuspended
	));
}
catch (NotFoundException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-notfound-title'],
		'message'	=> $locale->strings['error-notfound-text']
	));
}
catch (VpsSuspendException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-suspend-error-title'],
		'message'	=> $locale->strings['error-suspend-error-text']
	));
}
catch (VpsUnsuspendException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-unsuspend-error-title'],
		'message'	=> $locale->strings['error-unsuspend-error-text']
	));
}
