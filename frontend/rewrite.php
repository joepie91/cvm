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
			/* Frontpage/overview */
			'^/([0-9]+)/?$'			=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Start */
			'^/([0-9]+)/(start)/?$'		=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Stop */
			'^/([0-9]+)/(stop)/?$'		=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Restart */
			'^/([0-9]+)/(restart)/?$'	=> array(
				'target'			=> "module.vps.overview.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Reinstall */
			'^/([0-9]+)/reinstall/?$'	=> array(
				'target'			=> "module.vps.reinstall.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Change password */
			'^/([0-9]+)/password/?$'	=> array(
				'target'			=> "module.vps.password.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* VPS - Console */
			'^/([0-9]+)/console/?$'		=> array(
				'target'			=> "module.vps.console.php",
				'authenticator'			=> "authenticator.vps.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "vps"
			),
			/* Admin - Overview */
			'^/admin/?$'			=> array(
				'target'			=> "module.admin.overview.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Users - Overview */
			'^/admin/users/?$'		=> array(
				'target'			=> "module.admin.users.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Users - Lookup */
			'^/admin/user/([0-9]+)/?$'	=> array(
				'target'			=> "module.admin.user.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Containers - Overview */
			'^/admin/containers/?$'		=> array(
				'target'			=> "module.admin.containers.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Create VPS */
			'^/admin/containers/add/?$'	=> array(
				'target'			=> "module.admin.containers.create.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Containers - Suspend */
			'^/admin/container/([0-9]+)/suspend/?$'		=> array(
				'target'					=> "module.admin.container.suspend.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			/* Admin - Containers - Transfer */
			'^/admin/container/([0-9]+)/transfer/?$'	=> array(
				'target'					=> "module.admin.container.transfer.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			/* Admin - Containers - Terminate */
			'^/admin/container/([0-9]+)/terminate/?$'	=> array(
				'target'					=> "module.admin.container.terminate.php",
				'authenticator'					=> "authenticator.admin.php",
				'auth_error'					=> "error.access.php",
				'_menu'						=> "admin"
			),
			/* Admin - Nodes - Overview */
			'^/admin/nodes/?$'		=> array(
				'target'			=> "module.admin.nodes.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Lookup */
			'^/admin/node/([0-9]+)/?$'	=> array(
				'target'			=> "module.admin.node.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Add */
			'^/admin/nodes/add/?$' 		=> array(
				'target'			=> "module.admin.nodes.add.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin"
			),
			/* Admin - Nodes - Create VPS */
			'^/admin/node/([0-9]+)/add/?$'	=> array(
				'target'			=> "module.admin.containers.create.php",
				'authenticator'			=> "authenticator.admin.php",
				'auth_error'			=> "error.access.php",
				'_menu'				=> "admin",
				'_prefilled_node'		=> true
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
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-suspended-title'],
			'message'	=> $e->getMessage()
		));
	}
	catch (ContainerTerminatedException $e)
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-terminated-title'],
			'message'	=> $e->getMessage()
		));
	}
	
	if($router->uVariables['menu'] == "vps" && $router->uVariables['display_menu'] === true)
	{
		$sMainContents .= Templater::AdvancedParse("{$sTheme}/client/vps/main", $locale->strings, array(
			'error'			=> $sError,
			'contents'		=> $sPageContents,
			'id'			=> $sContainer->sId
		));
	}
	elseif($router->uVariables['menu'] == "admin" && $router->uVariables['display_menu'] === true)
	{
		$sMainContents .= Templater::AdvancedParse("{$sTheme}/admin/main", $locale->strings, array(
			'error'			=> $sError,
			'contents'		=> $sPageContents
		));
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

$sTemplateParameters = array_merge($sTemplateParameters, array(
	'logged-in'		=> $sLoggedIn,
	'title'			=> $sPageTitle,
	'main'			=> $sMainContents,
	'main-class'		=> (isset($router->uVariables['menu']) && $router->sAuthenticated === true) ? "shift" : "",
	'generation'		=> "<!-- page generated in " . (round(microtime(true) - $timing_start, 6)) . " seconds. -->"
));

echo(Templater::AdvancedParse("{$sTheme}/shared/main", $locale->strings, $sTemplateParameters));
