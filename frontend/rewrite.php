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
 
$_CVM = true;
require("includes/include.base.php");

$sTemplateParameters = array();

//$_SESSION['userid'] = 1;

if(!empty($_SESSION['userid']))
{
	$sUser = new User($_SESSION['userid']);
	$sLoggedIn = true;
	$sTemplateParameters = array_merge($sTemplateParameters, array(
		'username'	=> $sUser->sUsername
	));
}
else
{
	$sUser = new User(0);
	$sLoggedIn = false;
}

$sMainContents = "";
$sMainClass = "";
$sPageTitle = "";

// Initialize some variables to ensure they are available through the application.
// This works around the inability of CPHP to retain variables set in the first rewrite.
$sContainer = null;
$sPageContents = null;
$router = null;
$sError = null;

try
{
	$mainrouter = new CPHPRouter();

	$mainrouter->routes = array(
		0 => array(
			'^/?$'			=> "module.list.php",
			'^/account/?$'		=> "module.account.php",
			'^/login/?$'		=> "module.login.php",
			'^/logout/?$'		=> "module.logout.php",
			'^/admin(/.*)?$'	=> "module.admin.php",
			'^/([0-9]+)(/.*)?$'	=> "module.vps.php"
		)
	);

	$mainrouter->RouteRequest();
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
	'main-class'		=> $sMainClass
));

echo(Templater::InlineRender("main", $locale->strings, $sTemplateParameters));

?>
