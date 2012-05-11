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
	
	$sError = "";
	$sPageContents = "";

	$sMainClass = "shift";

	$router = new CPHPRouter();
	
	$router->ignore_query = true;

	$router->routes = array(
		0 => array(
			'^/admin/?$'			=> "module.admin.overview.php",
			'^/admin/users/?$'		=> "module.admin.users.php",
			'^/admin/containers/?$'		=> "module.admin.containers.php",
			'^/admin/user/([0-9]+)/?$'	=> "module.admin.user.php"
		)
	);

	$router->RouteRequest();

	$sMainContents .= Templater::InlineRender("main.admin", $locale->strings, array(
		'contents'		=> $sPageContents
	));
}
catch (InsufficientAccessLevelException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "You are not authorized to view this page", "Your access level is not sufficient.");
	$sMainContents .= $err->Render();
}
?>
