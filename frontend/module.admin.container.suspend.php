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
			
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container suspended", "The container has been suspended and can no longer be used by the owner.");
			$sMainContents .= $err->Render();
		}
		elseif($_POST['action'] == "unsuspend")
		{
			$sContainer->Unsuspend();
			
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Container unsuspended", "The container has been unsuspended and can now be used by the owner again.");
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
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "You are not authorized to view this page", "Your access level is not sufficient.");
	$sMainContents .= $err->Render();
}
catch (NotFoundException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container not found", "The container you selected was not found.");
	$sMainContents .= $err->Render();
}
catch (ContainerSuspendException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Failed to suspend container", "The container could not be suspended.");
	$sMainContents .= $err->Render();
}
catch (ContainerUnsuspendException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Failed to unsuspend container", "The container could not be unsuspended.");
	$sMainContents .= $err->Render();
}
