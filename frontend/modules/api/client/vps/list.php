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

if($result = $database->CachedQuery("SELECT * FROM containers WHERE `UserId` = :UserId", array(':UserId' => $sApiKey->sUser->sId)))
{
	$sVpses = array();
	
	foreach($result->data as $row)
	{
		$sVps = new Vps($row);
		
		$sVpsData = array(
			'id'			=> $sVps->sId,
			'virtualization_type'	=> $sVps->sVirtualizationType,
			'hostname'		=> $sVps->sHostname,
			'guaranteed_ram'	=> $sVps->sGuaranteedRam,
			'burstable_ram'		=> $sVps->sBurstableRam,
			'disk_space'		=> $sVps->sDiskSpace,
			'cpu_count'		=> $sVps->sCpuCount,
			'node'			=> $sVps->sNodeId,
			'location'		=> $sVps->sNode->sPhysicalLocation
		);
		
		if(true /* TODO: Check if OpenVZ */)
		{
			$sVpsData['template'] = sTemplateId;
		}
		
		if($sVps->sTotalTrafficLimit == 0)
		{
			/* Split traffic accounting */
			$sVpsData['traffic_in_limit'] = $sVps->sIncomingTrafficLimit;
			$sVpsData['traffic_out_limit'] = $sVps->sOutgoingTrafficLimit;
			$sVpsData['traffic_in_used'] = $sVps->sIncomingTrafficUsed;
			$sVpsData['traffic_out_used'] = $sVps->sOutgoingTrafficUsed;
		}
		else
		{
			/* Combined traffic accounting */
			$sVpsData['traffic_limit'] = $sVps->sTotalTrafficLimit;
			$sVpsData['traffic_used'] = $sVps->sIncomingTrafficUsed + $sVps->sOutgoingTrafficUsed;
		}
		
		$sVpses[] = $sVpsData;
	}
	
	$sResponse = array(
		'response' => array(
			'vpses' => $sVpses
		)
	);
}
else
{
	$sResponse = array(
		'response' => array(
			'vpses' => array()
		)
	);
}
