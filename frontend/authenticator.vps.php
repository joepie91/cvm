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

$router->uVariables['display_menu'] = true;

try
{
	$sContainer = new Container($router->uParameters[1]);
	
	if($sContainer->sUserId != $sUser->sId && $sUser->sAccessLevel < 20)
	{
		throw new UnauthorizedException("You are not authorized to control this VPS.");
	}
	
	$sRouterAuthenticated = true;
	
	try
	{
		$sContainer->CheckAllowed();
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, $locale->strings['warning-suspended-title'], $locale->strings['warning-suspended-text']);
		$sMainContents .= $err->Render();
	}
	catch (ContainerTerminatedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, $locale->strings['warning-terminated-title'], $locale->strings['warning-terminated-text']);
		$sMainContents .= $err->Render();
	}
}
catch(NotFoundException $e)
{
	$router->uVariables['display_menu'] = false;
	$sMainContents = Templater::AdvancedParse("error.vps.notfound", $locale->strings, array());
	$sRouterAuthenticated = false;
}
catch(UnauthorizedException $e)
{
	$router->uVariables['display_menu'] = false;
	$sRouterAuthenticated = false;
}

