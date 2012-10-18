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
 
$timing_start = microtime(true);
 
$_CVM = true;
require("includes/include.base.php");

$sTemplateParameters = array();

if(!empty($_SESSION['userid']))
{
	$sUser = new User($_SESSION['userid']);
	$sLoggedIn = true;
	$template_global_vars['accesslevel'] = $sUser->sAccessLevel;
	$sTemplateParameters = array_merge($sTemplateParameters, array(
		'username'	=> $sUser->sUsername
	));
}
else
{
	$sUser = new User(0);
	$sLoggedIn = false;
	$template_global_vars['accesslevel'] = 0;
}

$sMainContents = "";
$sMainClass = "";
$sPageTitle = "";

// Initialize some variables to ensure they are available through the application.
// This works around the inability of CPHP to retain variables set in the first rewrite.
$sContainer = null;
$sPageContents = "";
$router = null;
$sError = null;

try
{
	$router = new CPHPRouter();
	
	$router->ignore_query = true;

	$router->routes = array(
		0 => array(
			'^/?$'				=> "module.list.php",
			'^/account/?$'			=> "module.account.php",
			'^/login/?$'			=> "module.login.php",
			'^/logout/?$'			=> "module.logout.php",
			'^/([0-9]+)/?$'			=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/(start)/?$'		=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/(stop)/?$'		=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/(restart)/?$'	=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/reinstall/?$'	=> array(
				'target'			=> "module.vps.reinstall.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/password/?$'	=> array(
				'target'			=> "module.vps.password.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/([0-9]+)/console/?$'		=> array(
				'target'			=> "module.vps.console.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			'^/admin/?$'			=> array(
				'target'			=> "module.admin.overview.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/users/?$'		=> array(
				'target'			=> "module.admin.users.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/containers/?$'		=> array(
				'target'			=> "module.admin.containers.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/user/([0-9]+)/?$'	=> array(
				'target'			=> "module.admin.user.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/nodes/?$'		=> array(
				'target'			=> "module.admin.nodes.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/node/([0-9]+)/?$'	=> array(
				'target'			=> "module.admin.node.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			'^/admin/container/([0-9]+)/suspend/?$'		=> array(
				'target'					=> "module.admin.container.suspend.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			'^/admin/container/([0-9]+)/transfer/?$'	=> array(
				'target'					=> "module.admin.container.transfer.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			'^/admin/container/([0-9]+)/terminate/?$'	=> array(
				'target'					=> "module.admin.container.terminate.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			'^/test/?$'			=> "module.test.php"
		)
	);
	
	try
	{
		$router->RouteRequest();
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container is suspended", $e->getMessage());
		$sError .= $err->Render();
	}
	catch (ContainerTerminatedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Container is terminated", $e->getMessage());
		$sError .= $err->Render();
	}
	
	if($router->uVariables['menu'] == "vps" && $router->uVariables['display_menu'] === true)
	{
		$sMainContents .= Templater::AdvancedParse("main.vps", $locale->strings, array(
			'error'			=> $sError,
			'contents'		=> $sPageContents,
			'id'			=> $sContainer->sId
		));
	}
	elseif($router->uVariables['menu'] == "admin" && $router->uVariables['display_menu'] === true)
	{
		$sMainContents .= Templater::AdvancedParse("main.admin", $locale->strings, array(
			'error'			=> $sError,
			'contents'		=> $sPageContents
		));
	}
}
catch (UnauthorizedException $e)
{
	$sPageTitle = "Unauthorized";
	$sMainContents = "You are not authorized to view this page.";
}

$sTemplateParameters = array_merge($sTemplateParameters, array(
	'logged-in'		=> $sLoggedIn,
	'title'			=> $sPageTitle,
	'main'			=> $sMainContents,
	'main-class'		=> (isset($router->uVariables['menu']) && $router->sAuthenticated === true) ? "shift" : "",
	'generation'		=> "<!-- page generated in " . (round(microtime(true) - $timing_start, 6)) . " seconds. -->"
));

echo(Templater::AdvancedParse("main", $locale->strings, $sTemplateParameters));
