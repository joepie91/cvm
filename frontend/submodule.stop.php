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
		
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-stop-success-title'], $locale->strings['error-stop-success-text']);
		$sError .= $err->Render();
	}
	catch(ContainerStopException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-stop-failed-title'], $locale->strings['error-stop-failed-text']);
		$sError .= $err->Render();
	}
}
else
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-stop-stopped-title'], $locale->strings['error-stop-stopped-text']);
	$sError .= $err->Render();
}
