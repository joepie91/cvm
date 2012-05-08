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

$return_object = array();
$return_success = false;

if(isset($_GET['key']) && $_GET['key'] == $settings['local_api_key'])
{
	switch($_GET['action'])
	{
		case "verify_user":
			$sUsername = mysql_real_escape_string($_GET['username']);
			if($result = mysql_query_cached("SELECT * FROM users WHERE `Username` = '{$sUsername}'"))
			{
				$sUser = new User($result);
				
				if($sUser->VerifyPassword($_GET['password']) === true)
				{
					$return_object = true;
					$return_success = true;
				}
				else
				{
					$return_object = false;
					$return_success = true;
				}
			}
			break;
			
		case "list_vps":
			if(!empty($_GET['userid']))
			{
				$sUserId = (is_numeric($_GET['userid'])) ? $_GET['userid'] : 0;
				$query = "SELECT * FROM containers WHERE `UserId` = '{$sUserId}'";
			}
			else
			{
				$query = "SELECT * FROM containers";
			}
			
			if($result = mysql_query_cached($query))
			{
				// TODO: output results
				$sContainers = array();
				
				foreach($result->data as $row)
				{
					$sContainer = new Container($row);
					$sContainers[] = $sContainer->Export();
				}
				
				$return_object = $sContainers;
				$return_status = true;
			}
			break;
			
		case "vps_info":
			// TODO: return VPS info
			break;
	}
}
else
{
	$return_object = "Authentication failure.";
}

echo(json_encode(array(
	'status'	=> $return_success,
	'data'		=> $return_object
)));

?>
