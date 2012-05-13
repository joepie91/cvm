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

if($sContainer->sCurrentStatus != CVM_STATUS_STARTED)
{
	try
	{
		$sContainer->Start();
		$sContainer->sCurrentStatus = CVM_STATUS_STARTED;
		
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-start-success-title'], $locale->strings['error-start-success-text']);
		$sError .= $err->Render();
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-start-suspended-title'], $locale->strings['error-start-suspended-text']);
		$sError .= $err->Render();
	}
	catch (ContainerStartException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-start-failed-title'], $locale->strings['error-start-failed-text']);
		$sError .= $err->Render();
	}
}
else
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-start-running-title'], $locale->strings['error-start-running-text']);
	$sError .= $err->Render();
}
