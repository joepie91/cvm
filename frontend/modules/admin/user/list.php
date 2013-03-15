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

$result = $database->CachedQuery("SELECT * FROM users ORDER BY `AccessLevel` DESC");

$sUserList = array();

foreach($result->data as $row)
{
	$sUserEntry = new User($row);
	$sUserList[] = array(
		'id'		=> $sUserEntry->sId,
		'username'	=> $sUserEntry->sUsername,
		'email'		=> $sUserEntry->sEmailAddress,
		'accesslevel'	=> $sUserEntry->sAccessLevel
	);
}

$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/user/list", $locale->strings, array(
	'users'		=> $sUserList
));
