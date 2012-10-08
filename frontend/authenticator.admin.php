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
	$sUser->RequireAccessLevel(20);
	
	$sRouterAuthenticated = true;
}
catch (InsufficientAccessLevelException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-unauthorized-title'], $locale->strings['error-unauthorized-text']);
	$sMainContents .= $err->Render();
	
	$sRouterAuthenticated = false;
	
	$router->uVariables['display_menu'] = false;
}
