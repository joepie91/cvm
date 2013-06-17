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

if(!isset($_APP)) { die("Unauthorized."); }

try
{
	$sUser = new User($router->uParameters[1]);
}
catch (NotFoundException $e)
{
	throw new RouterException("Specified user does not exist.");
}

$sErrors = array();

if($router->uMethod == "post")
{	
	if(empty($_POST['username']) || preg_match("/^[a-z0-9_.-]+$/i", $_POST['username']) === 0)
	{
		$sErrors[] = "You did not enter a valid username.";
	}
	
	if(empty($_POST['email']) || filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false)
	{
		$sErrors[] = "You did not enter a valid e-mail address.";
	}
	
	if(empty($_POST['access']) || preg_match("/^[0-9]+$/", $_POST['access']) === 0)
	{
		$sErrors[] = "You did not specify a valid user type.";
	}
	else
	{
		if($sUser->sAccessLevel == 30 && $_POST['access'] < 30)
		{
			/* This user is a master admin, check if any other master admins exist before lowering
			 * the permissions of this one, to prevent lock-outs. */
			 
			try
			{
				User::CreateFromQuery("SELECT * FROM users WHERE `AccessLevel` = 30 AND `Id` != :Id", array(":Id" => $sUser->sId), 0);
			}
			catch (NotFoundException $e)
			{
				$sErrors[] = "You can't remove your master administrator permissions if no other master administrators exist!";
			}
		}
	}
	
	if(empty($sErrors))
	{
		$sUser->uUsername = $_POST['username'];
		$sUser->uEmailAddress = $_POST['email'];
		$sUser->uAccessLevel = $_POST['access'];
		$sUser->InsertIntoDatabase();
		redirect("/admin/user/{$sUser->sId}/");
	}
}

$sPageContents = NewTemplater::Render("{$sTheme}/admin/user/edit", $locale->strings, array(
	"errors"	=> $sErrors,
	"id"		=> $router->uParameters[1]
), array(
	"username"	=> $sUser->sUsername,
	"email"		=> $sUser->sEmailAddress,
	"access"	=> $sUser->sAccessLevel
));
