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
	$sContainer = new Container($mainrouter->uParameters[1]);
	
	if($sContainer->sUserId != $sUser->sId && $sUser->sAccessLevel < 20)
	{
		throw new UnauthorizedException("You are not authorized to control this container.");
	}
	
	$sError = "";
	$sPageContents = "";

	$sMainClass = "shift";

	$router = new CPHPRouter();
	
	$router->ignore_query = true;

	$router->routes = array(
		0 => array(
			'^/([0-9]+)/?$'			=> "module.vps.overview.php",
			'^/([0-9]+)/(start)/?$'		=> "module.vps.overview.php",
			'^/([0-9]+)/(stop)/?$'		=> "module.vps.overview.php",
			'^/([0-9]+)/(restart)/?$'	=> "module.vps.overview.php",
			'^/([0-9]+)/reinstall/?$'	=> "module.vps.reinstall.php",
			'^/([0-9]+)/password/?$'	=> "module.vps.password.php",
			'^/([0-9]+)/console/?$'		=> "module.vps.console.php"
		)
	);

	$router->RouteRequest();

	try
	{
		$sContainer->CheckAllowed();
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, "This container is suspended", "You cannot change any configuration or perform any actions on this container. If you feel this should not be the case, please contact support.");
		$sMainContents .= $err->Render();
	}
	catch (ContainerTerminatedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, "This container has been terminated", "You cannot change any configuration or perform any actions on this container, as it is terminated.");
		$sMainContents .= $err->Render();
	}

	$sMainContents .= Templater::InlineRender("main.vps", $locale->strings, array(
		'error'			=> $sError,
		'contents'		=> $sPageContents,
		'id'			=> $sContainer->sId
	));
}
catch(NotFoundException $e)
{
	$sMainContents = Templater::InlineRender("error.vps.notfound");
}
