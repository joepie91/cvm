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
	try
	{
		$sContainer->Stop();
	}
	catch(ContainerStopException $e)
	{
		// we can make this silently fail, as the only important thing is that it starts again
	}
	
	$sContainer->Start();
	$sContainer->sCurrentStatus = CVM_STATUS_STARTED;
	
	$sError .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
		'title'		=> $locale->strings['error-stop-restart-success'],
		'message'	=> $locale->strings['error-stop-restart-success']
	));
}
catch (ContainerSuspendedException $e)
{
	$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-restart-suspended-title'],
		'message'	=> $locale->strings['error-restart-suspended-text']
	));
}
catch (ContainerTerminatedException $e)
{
	$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-restart-terminated-title'],
		'message'	=> $locale->strings['error-restart-terminated-text']
	));
}
catch(ContainerStartException $e)
{
	$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-restart-start-title'],
		'message'	=> $locale->strings['error-restart-start-text']
	));
}
