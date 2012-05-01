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
	
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container restarted", "Your container was successfully restarted.");
	$sError .= $err->Render();
}
catch(ContainerStartException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container failed to start", "Your container could not be started. If this error persists, please file a support ticket.");
	$sError .= $err->Render();
}

?>
