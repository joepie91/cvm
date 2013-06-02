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

$sErrors = array();

if(isset($_POST['submit']))
{
	if(empty($_POST['name']))
	{
		$sErrors[] = $locale->strings['error-admin-nodes-add-name'];
	}
	
	if(empty($_POST['hostname']) || preg_match(REGEX_HOSTNAME, $_POST['hostname']) === 0)
	{
		$sErrors[] = $locale->strings['error-admin-nodes-add-hostname'];
	}
	
	if(empty($_POST['location']))
	{
		$sErrors[] = $locale->strings['error-admin-nodes-add-location'];
	}
	
	if(isset($_POST['customkey']))
	{
		$sKeyId = random_string(20);
		
		/* TODO: Ensure validity of the custom keys. */
		
		if($_FILES["publickey"]["error"] == UPLOAD_ERR_OK)
		{
			$sPublicKeyName = "{$sKeyId}.public.key";
		}
		else
		{
			$sErrors[] = $locale->strings['error-admin-nodes-add-publickey'];
		}
		
		if($_FILES["privatekey"]["error"] == UPLOAD_ERR_OK)
		{
			$sPrivateKeyName = "{$sKeyId}.private.key";
		}
		else
		{
			$sErrors[] = $locale->strings['error-admin-nodes-add-privatekey'];
		}
	}
	
	if(empty($sErrors))
	{
		if(isset($_POST['customkey']) == false ||
			(move_uploaded_file($_FILES['publickey']['tmp_name'], "/etc/cvm/keys/{$sPublicKeyName}") &&
			move_uploaded_file($_FILES['privatekey']['tmp_name'], "/etc/cvm/keys/{$sPrivateKeyName}")))
		{
			$sNode = new Node(0);
			$sNode->uName = $_POST['name'];
			$sNode->uHostname = $_POST['hostname'];
			$sNode->uPhysicalLocation = $_POST['location'];
			$sNode->uHasCustomKey = isset($_POST['customkey']);
			$sNode->uPublicKey = $sPublicKeyName;
			$sNode->uPrivateKey = $sPrivateKeyName;
			$sNode->uUser = "cvm";
			$sNode->uPort = 22;
			$sNode->InsertIntoDatabase();
			
			redirect("/admin/nodes/");
		}
		else
		{
			$sErrors[] = $locale->strings['error-admin-nodes-add-upload'];
		}
	}
}

$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/node/add", $locale->strings, array(
	'errors'	=> $sErrors
));
