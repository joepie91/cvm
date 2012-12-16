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
	// TODO: alert the user that he will switch to a different account, or disable if it is not allowed to have multiple accounts
}

$sError = "";
$sFieldUsername = "";
$render_form = true;

if(isset($_POST['submit']))
{
	$sUsername = mysql_real_escape_string($_POST['username']);
	$sFieldUsername = htmlentities($_POST['username']);
	
	if($result = mysql_query_cached("SELECT * FROM users WHERE `Username` = '{$sUsername}'"))
	{
		$sLoginUser = new User($result);
		
		if($sLoginUser->VerifyPassword($_POST['password']))
		{
			$_SESSION['userid'] = $sLoginUser->sId;
			header("Location: /");
			die();
		}
		else
		{
			$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-login-invalid-title'],
				'message'	=> $locale->strings['error-login-invalid-text']
			));
		}
	}
	else
	{
		$sError .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-login-invalid-title'],
			'message'	=> $locale->strings['error-login-invalid-text']
		));
	}
}

$sMainContents = Templater::AdvancedParse("{$sTheme}/shared/login", $locale->strings, array(
	'error'			=> $sError,
	'field-username'	=> $sFieldUsername
));
