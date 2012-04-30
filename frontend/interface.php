<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";
$settings['salt'] = "kAU0qM";

$sContainer = new Container(1);
$sError = "";

if($_GET['action'] == "start")
{
	if($sContainer->GetStatus() != CVM_STATUS_STARTED)
	{
		try
		{
			$sContainer->Start();
			
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container started", "Your container was successfully started.");
			$sError .= $err->Render();
		}
		catch(ContainerStartException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container failed to start", "Your container could not be started. If this error persists, please file a support ticket.");
			$sError .= $err->Render();
		}
	}
	else
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container can't be started", "Your container cannot be started because it is already running.");
		$sError .= $err->Render();
	}
}
elseif($_GET['action'] == "stop")
{
	if($sContainer->GetStatus() != CVM_STATUS_STOPPED)
	{
		try
		{
			$sContainer->Stop();
			
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
}
elseif($_GET['action'] == "restart")
{
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
		
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container restarted", "Your container was successfully restarted.");
		$sError .= $err->Render();
	}
	catch(ContainerStartException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container failed to start", "Your container could not be started. If this error persists, please file a support ticket.");
		$sError .= $err->Render();
	}
}

echo(Templater::InlineRender("main", $locale->strings, array(
	'error'			=> $sError,
	'server-location'	=> $sContainer->sNode->sPhysicalLocation,
	'operating-system'	=> $sContainer->sTemplate->sName,
	'guaranteed-ram'	=> "{$sContainer->sGuaranteedRam}MB",
	'burstable-ram'		=> "{$sContainer->sBurstableRam}MB",
	'disk-space'		=> "{$sContainer->sDiskSpace}MB",
	'total-traffic-limit'	=> "{$sContainer->sTotalTrafficLimit} bytes",
	'bandwidth-limit'	=> "100mbit",
	'status'		=> Templater::InlineRender("status.{$sContainer->sStatusText}", $locale->strings)
)));

?>
