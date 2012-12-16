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
	$sVps = new Vps($router->uParameters[1]);
	
	if($sVps->sUserId != $sUser->sId && $sUser->sAccessLevel < 20)
	{
		throw new UnauthorizedException("You are not authorized to control this VPS.");
	}
	
	$sRouterAuthenticated = true;
	
	try
	{
		$sVps->CheckAllowed();
	}
	catch (VpsSuspendedException $e)
	{
		$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/warning", $locale->strings, array(
			'title'		=> $locale->strings['warning-suspended-title'],
			'message'	=> $locale->strings['warning-suspended-text']
		));
	}
	catch (VpsTerminatedException $e)
	{
		$sMainContents .= NewTemplater::Render("{$sTheme}/shared/error/warning", $locale->strings, array(
			'title'		=> $locale->strings['warning-terminated-title'],
			'message'	=> $locale->strings['warning-terminated-text']
		));
	}
}
catch(NotFoundException $e)
{
	$router->uVariables['display_menu'] = false;
	$sMainContents = Templater::AdvancedParse("{$sTheme}/client/vps/error/notfound", $locale->strings, array());
	$sRouterAuthenticated = false;
}
catch(UnauthorizedException $e)
{
	$router->uVariables['display_menu'] = false;
	$sRouterAuthenticated = false;
}

