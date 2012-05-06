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

$sMainContents = "";
$sMainClass = "";
$sPageTitle = "";

// Initialize some variables to ensure they are available through the application.
// This works about the inability of CPHP to retain variables set in the first rewrite.
$sContainer = null;
$sPageContents = null;
$router = null;
$sError = null;

$mainrouter = new CPHPRouter();

$mainrouter->routes = array(
	0 => array(
		'^/?$'			=> "module.home.php",
		'^/containers/?$'	=> "module.list.php",
		'^/login/?$'		=> "module.login.php",
		'^/logout/?$'		=> "module.logout.php",
		'^/([0-9]+)(/.*)?$'	=> "module.vps.php"
	)
);

$mainrouter->RouteRequest();

echo(Templater::InlineRender("main", $locale->strings, array(
	'title'			=> $sPageTitle,
	'main'			=> $sMainContents,
	'main-class'		=> $sMainClass
)));

?>
