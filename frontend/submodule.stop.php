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
		
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container stopped", "Your container was successfully stopped.");
		$sError .= $err->Render();
	}
	catch(ContainerStartException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container failed to stop", "Your container could not be stopped. If this error persists, please file a support ticket.");
		$sError .= $err->Render();
	}
}
else
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container can't be stopped", "Your container cannot be stopped because it is not running.");
	$sError .= $err->Render();
}
