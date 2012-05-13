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

if($sLoggedIn === true)
{
	unset($_SESSION['userid']);
	$sUser = new User(0);
	$sLoggedIn = false;
	
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-logout-success-title'], $locale->strings['error-logout-success-text']);
	$sMainContents .= $err->Render();
}
else
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-logout-notloggedin-title'], $locale->strings['error-logout-notloggedin-text']);
	$sMainContents .= $err->Render();
}
