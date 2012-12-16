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

try
{
	$sUser->RequireAccessLevel(20);
	
	$sContainer = new Container($router->uParameters[1]);
	
	if(isset($_POST['submit']))
	{
		if($_POST['action'] == "suspend")
		{
			$sContainer->Suspend();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-suspend-success-title'],
				'message'	=> $locale->strings['error-suspend-success-text']
			));
		}
		elseif($_POST['action'] == "unsuspend")
		{
			$sContainer->Unsuspend();
			
			$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
				'title'		=> $locale->strings['error-unsuspend-success-title'],
				'message'	=> $locale->strings['error-unsuspend-success-text']
			));
		}
		
		$sContainer->RefreshData();
		
		/* TODO: Flash message and redirect to VPS lookup page. */
	}
	
	$sSuspended = ($sContainer->sStatus == CVM_STATUS_SUSPENDED) ? true : false;
	
	$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/vps/suspend", $locale->strings, array(
		'id'		=> $sContainer->sId,
		'suspended'	=> $sSuspended
	));
}
catch (InsufficientAccessLevelException $e)
{
	/* TODO: Is this really necessary? */
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-unauthorized-title'],
		'message'	=> $locale->strings['error-unauthorized-text']
	));
}
catch (NotFoundException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-notfound-title'],
		'message'	=> $locale->strings['error-notfound-text']
	));
}
catch (ContainerSuspendException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-suspend-error-title'],
		'message'	=> $locale->strings['error-suspend-error-text']
	));
}
catch (ContainerUnsuspendException $e)
{
	$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-unsuspend-error-title'],
		'message'	=> $locale->strings['error-unsuspend-error-text']
	));
}
