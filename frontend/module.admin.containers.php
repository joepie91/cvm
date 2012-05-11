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

$sContainerList = array();

if($result = mysql_query_cached("SELECT * FROM containers"))
{
	foreach($result->data as $row)
	{
		$sContainer = new Container($row);
		
		$sContainerList[] = array(
			'id'			=> $sContainer->sId,
			'hostname'		=> $sContainer->sHostname,
			'node'			=> $sContainer->sNode->sName,
			'node-hostname'		=> $sContainer->sNode->sHostname,
			'template'		=> $sContainer->sTemplate->sName,
			'diskspace'		=> number_format($sContainer->sDiskSpace / 1024),
			'diskspace-unit'	=> "GB",
			'guaranteed-ram'	=> $sContainer->sGuaranteedRam,
			'guaranteed-ram-unit'	=> "MB",
			'status'		=> $sContainer->sStatusText,
			'virtualization-type'	=> $sContainer->sVirtualizationType
		);
	}
}

$sPageContents = Templater::InlineRender("admin.containers", $locale->strings, array(
	'containers'		=> $sContainerList
));