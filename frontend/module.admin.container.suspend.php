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
			
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-suspend-success-title'], $locale->strings['error-suspend-success-text']);
			$sMainContents .= $err->Render();
		}
		elseif($_POST['action'] == "unsuspend")
		{
			$sContainer->Unsuspend();
			
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-unsuspend-success-title'], $locale->strings['error-unsuspend-success-text']);
			$sMainContents .= $err->Render();
		}
		
		$sContainer->RefreshData();
	}
	
	$sSuspended = ($sContainer->sStatus == CVM_STATUS_SUSPENDED) ? true : false;
	
	$sPageContents = Templater::AdvancedParse("admin.container.suspend", $locale->strings, array(
		'id'		=> $sContainer->sId,
		'suspended'	=> $sSuspended
	));
}
catch (InsufficientAccessLevelException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-unauthorized-title'], $locale->strings['error-unauthorized-text']);
	$sMainContents .= $err->Render();
}
catch (NotFoundException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-notfound-title'], $locale->strings['error-notfound-text']);
	$sMainContents .= $err->Render();
}
catch (ContainerSuspendException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-suspend-error-title'], $locale->strings['error-suspend-error-text']);
	$sMainContents .= $err->Render();
}
catch (ContainerUnsuspendException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-unsuspend-error-title'], $locale->strings['error-unsuspend-error-text']);
	$sMainContents .= $err->Render();
}
