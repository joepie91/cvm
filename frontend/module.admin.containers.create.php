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

$sErrors = array();

if(isset($_POST['submit']))
{
	check_fields($_POST, array(
		'node'		=> $locale->strings['error-admin-vpses-add-node'],
		'user'		=> $locale->strings['error-admin-vpses-add-user'],
		'diskspace'	=> $locale->strings['error-admin-vpses-add-disk'],
		'guaranteed'	=> $locale->strings['error-admin-vpses-add-guaranteed'],
		'burstable'	=> $locale->strings['error-admin-vpses-add-burstable'],
		'cpucount'	=> $locale->strings['error-admin-vpses-add-cpucount'],
		'traffic'	=> $locale->strings['error-admin-vpses-add-traffic'],
		'template'	=> $locale->strings['error-admin-vpses-add-template']
	), $sErrors);
	
	try
	{
		$disk_space = parse_size($_POST['diskspace']);
	}
	catch(ParsingException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-disk']);
	}
	
	try
	{
		$guaranteed_ram = parse_size($_POST['guaranteed']);
	}
	catch(ParsingException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-guaranteed']);
	}
	
	try
	{
		$burstable_ram = parse_size($_POST['burstable']);
	}
	catch(ParsingException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-burstable']);
	}
	
	try
	{
		$traffic = parse_size($_POST['traffic']);
	}
	catch(ParsingException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-traffic']);
	}
	
	if(is_numeric($_POST['cpucount']))
	{
		$cpu_count = (int) $_POST['cpucount'];
	}
	else
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-cpucount']);
	}
	
	try
	{
		$node = new Node($_POST['node']);
	}
	catch(NotFoundException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-node']);
	}
	
	try
	{
		$user = new User($_POST['user']);
	}
	catch(NotFoundException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-user']);
	}
	
	try
	{
		$template = new Template($_POST['template']);
	}
	catch(NotFoundException $e)
	{
		array_add($sErrors, $locale->strings['error-admin-vpses-add-template']);
	}
	
	if(!empty($_POST['hostname']))
	{
		if(validate_hostname($_POST['hostname']))
		{
			$hostname = $_POST['hostname'];
		}
		else
		{
			array_add($sErrors, $locale->strings['error-admin-vpses-add-hostname']);
		}
	}
	else
	{
		$hostname = random_string(12);
	}
	
	if(empty($sErrors))
	{
		$sContainer = new Container(0);
		$sContainer->uHostname = $hostname;
		$sContainer->uInternalId = first_unused_ctid();
		$sContainer->uNodeId = $node->sId;
		$sContainer->uTemplateId = $template->sId;
		$sContainer->uUserId = $user->sId;
		$sContainer->uVirtualizationType = CVM_VIRTUALIZATION_OPENVZ;
		$sContainer->uGuaranteedRam = ($guaranteed_ram / 1024 / 1024); /* MB */
		$sContainer->uBurstableRam = ($burstable_ram / 1024 / 1024); /* MB */
		$sContainer->uDiskSpace = ($disk_space / 1024 / 1024); /* MB */
		$sContainer->uCpuCount = $cpu_count;
		$sContainer->uStatus = CVM_STATUS_BLANK;
		$sContainer->uIncomingTrafficLimit = $traffic;
		$sContainer->uOutgoingTrafficLimit = $traffic;
		$sContainer->uTotalTrafficLimit = $traffic;
		$sContainer->InsertIntoDatabase();
		$sContainer->Deploy();
		
		/* TODO: Flash message. */
		
		redirect("/admin/node/{$node->sId}/");
	}
}

/* This is a bit hacky - there's no better method for this yet. If the node or user has to be
 * pre-determined (according to the requested URL), it is stored in the relevant POST variable
 * so that the templater will think it was an already selected option, thereby causing the
 * desired behaviour: pre-selecting the particular option. */

if(!empty($router->uVariables['prefilled_node']))
{
	$_POST['node'] = $router->uParameters[1];
}

if(!empty($router->uVariables['prefilled_user']))
{
	$_POST['user'] = $router->uParameters[1];
}

$result = $database->CachedQuery("SELECT * FROM nodes");

$sNodes = array();

foreach($result->data as $row)
{
	$sNode = new Node($row);
	
	$sNodes[] = array(
		'id'		=> $sNode->sId,
		'name'		=> $sNode->sName,
		'location'	=> $sNode->sPhysicalLocation
	);
}

$result = $database->CachedQuery("SELECT * FROM templates WHERE `Available` = 1");

$sTemplates = array();

foreach($result->data as $row)
{
	$sTemplate = new Template($row);
	
	$sTemplates[] = array(
		'id'		=> $sTemplate->sId,
		'name'		=> $sTemplate->sName
	);
}

$result = $database->CachedQuery("SELECT * FROM users WHERE `AccessLevel` > 0");

$sUsers = array();

foreach($result->data as $row)
{
	$sUserOption = new User($row);
	
	$sUsers[] = array(
		'id'		=> $sUserOption->sId,
		'username'	=> $sUserOption->sUsername
	);
}

$sPageContents = Templater::AdvancedParse("{$sTheme}/admin/vps/add", $locale->strings, array(
	'errors'	=> $sErrors,
	'nodes'		=> $sNodes,
	'users'		=> $sUsers,
	'templates'	=> $sTemplates
));
