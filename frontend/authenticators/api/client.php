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

$public_token = $_SERVER['HTTP_API_PUBLIC_TOKEN'];
$private_token = $_SERVER['HTTP_API_PRIVATE_TOKEN'];

if($result = $database->CachedQuery("SELECT * FROM api_keys WHERE `PublicToken` = :Token", array(":Token" => $public_token)))
{
	$sApiKey = new ApiKey($result);
	
	if($sApiKey->VerifyToken($private_token))
	{
		if($sApiKey->sKeyType >= API_CLIENT)
		{
			$sRouterAuthenticated = true;
		}
		else
		{
			$sResponseCode = 403;
			$sResponse = array(
				"errors" => array(
					"The specified API token pair does not have access to this API."
				)
			);
		}
	}
	else
	{
		$sResponseCode = 401;
		$sResponse = array(
			"errors" => array(
				"No valid API token pair was specified."
			)
		);
	}
}
else
{
	$sResponseCode = 401;
	$sResponse = array(
		"errors" => array(
			"No valid API token pair was specified."
		)
	);
}
