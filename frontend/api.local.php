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

/* TODO: Switch to PDO here. */

$_CVM = true;
require("includes/include.base.php");

$return_object = array();
$return_success = false;

if(isset($_GET['key']) && $_GET['key'] == $settings['local_api_key'])
{
	switch($_GET['action'])
	{
		case "verify_user":
			if($result = $database->CachedQuery("SELECT * FROM users WHERE `Username` = :Username", array(":Username" => $_GET['username'])))
			{
				$sUser = new User($result);
				
				if($sUser->VerifyPassword($_GET['password']) === true)
				{
					$return_object = array(
						'correct' => true,
						'userid' => $sUser->sId
					);
					$return_success = true;
				}
				else
				{
					$return_object = array(
						'correct' => false,
						'userid' => 0
					);
					$return_success = true;
				}
			}
			else
			{
				$return_object = array(
					'correct' => false,
					'userid' => 0
				);
			}
			break;
			
		case "list_vps":
			if(!empty($_GET['userid']))
			{
				$result = $database->CachedQuery("SELECT * FROM containers WHERE `UserId` = :UserId", array(":UserId" => $_GET['userid']));
			}
			else
			{
				$result = $database->CachedQuery("SELECT * FROM containers");
			}
			
			if($result)
			{
				$sVpses = array();
				
				foreach($result->data as $row)
				{
					$sVps = new Vps($row);
					$sVpses[] = array(
						'hostname'	=> $sVps->sHostname,
						'internal_id'	=> $sVps->sInternalId,
						'node_id'	=> $sVps->sNodeId,
						'status'	=> $sVps->sStatus
					);
				}
				
				$return_object = $sVpses;
				$return_success = true;
			}
			break;
			
		case "vps_info":
			// TODO: return VPS info
			break;
			
		case "node_info":
			try
			{
				$sNode = new Node($_GET['nodeid']);
				$return_object = array(
					'name'			=> $sNode->sName,
					'hostname'		=> $sNode->sHostname,
					'port'			=> $sNode->sPort,
					'user'			=> $sNode->sUser,
					'physical_location'	=> $sNode->sPhysicalLocation,
					'private_key'		=> $sNode->sPrivateKey,
					'public_key'		=> $sNode->sPublicKey,
					'has_custom_key'	=> $sNode->sHasCustomKey
				);
			}
			catch (NotFoundException $e)
			{
				// Silently pass.
			}
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
