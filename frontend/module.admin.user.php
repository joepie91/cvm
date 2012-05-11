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

try
{
	$sUserEntry = new User($router->uParameters[1]);
	
	$sPageContents = Templater::InlineRender("admin.user", $locale->strings, array(
		'id'			=> $sUserEntry->sId,
		'username'		=> $sUserEntry->sUsername,
		'email'			=> $sUserEntry->sEmailAddress,
		'accesslevel'		=> $sUserEntry->sAccessLevel,
		'containers'		=> $sUserEntry->sContainerCount
	));
}
catch (NotFoundException $e)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "That user does not exist", "The user you tried to look up does not exist.");
	$sPageContents .= $err->Render();
}

