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
	$sUser->RequireAccessLevel(30);
	
	$sVps = new Vps($router->uParameters[1]);
	
	if(isset($_POST['submit']))
	{
		if($_POST['action'] == "terminate")
		{
			$sVps->Terminate();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-terminate-success-title'],
				'message'	=> $locale->strings['error-terminate-success-text']
			));
		}
		elseif($_POST['action'] == "unterminate")
		{
			$sVps->Unterminate();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-unterminate-success-title'],
				'message'	=> $locale->strings['error-unterminate-success-text']
			));
		}
		
		$sVps->RefreshData();
		
		/* TODO: Flash message and redirect to VPS lookup page. */
	}
	
	$sTerminated = ($sVps->sStatus == CVM_STATUS_TERMINATED) ? true : false;
	
	$sPageContents = NewTemplater::Render("{$sTheme}/admin/vps/terminate", $locale->strings, array(
		'id'			=> $sVps->sId,
		'terminated'		=> $sTerminated,
		'can-unterminate'	=> !$sVps->IsTerminated
	));
}
catch (NotFoundException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-notfound-title'],
		'message'	=> $locale->strings['error-notfound-text']
	));
}
catch (VpsTerminateException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-terminate-error-title'],
		'message'	=> $locale->strings['error-terminate-error-text']
	));
}
catch (VpsUnterminateException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-unterminate-error-title'],
		'message'	=> $locale->strings['error-unterminate-error-text']
	));
}

