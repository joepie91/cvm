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
 
$_APP = true;
require("includes/include.base.php");

$sTheme = "default";

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
$sResponse = array();
$sResponseCode = 200;

/* Initialize some variables to ensure that they are available throughout the application.
 * Due to the way PHP variable scoping works (and the way CPHP works around this), variables
 * are only available at the end of rewrite.php if they were set *before* routing the request. */
$sVps = null;
$sPageContents = "";
$router = null;
$sError = null;

try
{
	$router = new CPHPRouter();
	
	$router->ignore_query = true;

	$router->routes = array(
		0 => array(
			'^/?$'				=> "modules/client/vps/list.php",
			'^/list/?$'			=> "modules/client/vps/list.php",
			'^/account/?$'			=> "modules/client/account/index.php",
			'^/login/?$'			=> "modules/shared/login.php",
			'^/logout/?$'			=> "modules/shared/logout.php",
			/* Frontpage/overview */
			'^/([0-9]+)/?$'			=> array(
				'target'			=> "modules/client/vps/lookup.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Start */
			'^/([0-9]+)/start/?$'		=> array(
				'target'			=> "modules/client/vps/lookup.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps",
				'_action'			=> "start"
			),
			/* VPS - Stop */
			'^/([0-9]+)/stop/?$'		=> array(
				'target'			=> "modules/client/vps/lookup.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps",
				'_action'			=> "stop"
			),
			/* VPS - Restart */
			'^/([0-9]+)/restart/?$'	=> array(
				'target'			=> "modules/client/vps/lookup.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps",
				'_action'			=> "restart"
			),
			/* VPS - Reinstall */
			'^/([0-9]+)/reinstall/?$'	=> array(
				'target'			=> "modules/client/vps/reinstall.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Change password */
			'^/([0-9]+)/password/?$'	=> array(
				'target'			=> "modules/client/vps/password.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Console */
			'^/([0-9]+)/console/?$'		=> array(
				'target'			=> "modules/client/vps/console.php",
				'authenticator'			=> "authenticators/vps.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "vps"
			),
			/* Admin - Overview */
			'^/admin/?$'			=> array(
				'target'			=> "modules/admin/overview/index.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Users - Overview */
			'^/admin/users/?$'		=> array(
				'target'			=> "modules/admin/user/list.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Users - Lookup */
			'^/admin/user/([0-9]+)/?$'	=> array(
				'target'			=> "modules/admin/user/lookup.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Users - Create VPS */
			'^/admin/user/([0-9]+)/add/?$'	=> array(
				'target'			=> "modules/admin/vps/create.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin",
				'_prefilled_user'		=> true
			),
			/* Admin - VPSes - Overview */
			'^/admin/vpses/?$'		=> array(
				'target'			=> "modules/admin/vps/list.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - VPSes - Create VPS */
			'^/admin/vpses/add/?$'	=> array(
				'target'			=> "modules/admin/vps/create.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - VPSes - Suspend */
			'^/admin/vps/([0-9]+)/suspend/?$'	=> array(
				'target'				=> "modules/admin/vps/suspend.php",
				'authenticator'				=> "authenticators/admin.php",
				'auth_error'				=> "modules/error/access.php",
				'_menu'					=> "admin"
			),
			/* Admin - VPSes - Transfer */
			'^/admin/vps/([0-9]+)/transfer/?$'	=> array(
				'target'				=> "modules/admin/vps/transfer.php",
				'authenticator'				=> "authenticators/admin.php",
				'auth_error'				=> "modules/error/access.php",
				'_menu'					=> "admin"
			),
			/* Admin - VPSes - Terminate */
			'^/admin/vps/([0-9]+)/terminate/?$'	=> array(
				'target'				=> "modules/admin/vps/terminate.php",
				'authenticator'				=> "authenticators/admin.php",
				'auth_error'				=> "modules/error/access.php",
				'_menu'					=> "admin"
			),
			/* Admin - Nodes - Overview */
			'^/admin/nodes/?$'		=> array(
				'target'			=> "modules/admin/node/list.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Lookup */
			'^/admin/node/([0-9]+)/?$'	=> array(
				'target'			=> "modules/admin/node/lookup.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Add */
			'^/admin/nodes/add/?$' 		=> array(
				'target'			=> "modules/admin/node/add.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Create VPS */
			'^/admin/node/([0-9]+)/add/?$'	=> array(
				'target'			=> "modules/admin/vps/create.php",
				'authenticator'			=> "authenticators/admin.php",
				'auth_error'			=> "modules/error/access.php",
				'_menu'				=> "admin",
				'_prefilled_node'		=> true
			),
			/* API - Client - List VPSes */
			'^/api/client/list'		=> array(
				'target'			=> "modules/api/client/vps/list.php",
				'authenticator'			=> "authenticators/api/client.php",
				'auth_error'			=> "modules/error/api/access.php",
				'_raw'				=> true
			),
			'^/test/?$'			=> "modules/test.php"
		)
	);
	
	try
	{
		$router->RouteRequest();
	}
	catch (VpsSuspendedException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-suspended-title'],
			'message'	=> $e->getMessage()
		));
	}
	catch (VpsTerminatedException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-terminated-title'],
			'message'	=> $e->getMessage()
		));
	}
	
	if(empty($router->uVariables['raw']))
	{
		if(isset($router->uVariables['menu']) && $router->uVariables['menu'] == "vps" && $router->uVariables['display_menu'] === true)
		{
			$sMainContents .= Templater::AdvancedParse("{$sTheme}/client/vps/main", $locale->strings, array(
				'error'			=> $sError,
				'contents'		=> $sPageContents,
				'id'			=> $sVps->sId
			));
		}
		elseif(isset($router->uVariables['menu']) && $router->uVariables['menu'] == "admin" && $router->uVariables['display_menu'] === true)
		{
			$sMainContents .= Templater::AdvancedParse("{$sTheme}/admin/main", $locale->strings, array(
				'error'			=> $sError,
				'contents'		=> $sPageContents
			));
		}
	}
}
catch (UnauthorizedException $e)
{
	$sPageTitle = $locale->strings['title-unauthorized'];
	
	$sMainContents = NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
		'title'		=> $locale->strings['error-unauthorized-title'],
		'message'	=> $locale->strings['error-unauthorized-text']
	));
}

if(empty($router->uVariables['raw']))
{
	$sTemplateParameters = array_merge($sTemplateParameters, array(
		'logged-in'		=> $sLoggedIn,
		'title'			=> $sPageTitle,
		'main'			=> $sMainContents,
		'menu-visible'		=> (isset($router->uVariables['menu']) && $router->sAuthenticated === true),
		'generation'		=> round(microtime(true) - $timing_start, 6)
	));

	echo(Templater::AdvancedParse("{$sTheme}/shared/main", $locale->strings, $sTemplateParameters));
}
else
{
	status_code($sResponseCode);
	echo(json_encode($sResponse));
}
