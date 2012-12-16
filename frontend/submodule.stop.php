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

if($sContainer->sCurrentStatus != CVM_STATUS_STOPPED)
{
	try
	{
		$sContainer->Stop();
		$sContainer->sCurrentStatus = CVM_STATUS_STOPPED;
		
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
			'title'		=> $locale->strings['error-stop-success-title'],
			'message'	=> $locale->strings['error-stop-success-text']
		));
	}
	catch (ContainerSuspendedException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-stop-suspended-title'],
			'message'	=> $locale->strings['error-stop-suspended-text']
		));
	}
	catch (ContainerTerminatedException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-stop-terminated-title'],
			'message'	=> $locale->strings['error-stop-terminated-text']
		));
	}
	catch(ContainerStopException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-stop-failed-title'],
			'message'	=> $locale->strings['error-stop-failed-text']
		));
	}
}
else
{
	$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-stop-stopped-title'],
		'message'	=> $locale->strings['error-stop-stopped-text']
	));
}
