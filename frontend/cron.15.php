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

if($result = $database->CachedQuery("SELECT * FROM containers"))
{
	foreach($result->data as $row)
	{
		$sVps = new Vps($row);
		
		try
		{
			$sVps->UpdateTraffic();
		}
		catch (VpsTrafficRetrieveException $e)
		{
			if($sVps->sCurrentStatus == CVM_STATUS_STARTED)
			{
				// This is not supposed to fail, as the VPS is running.
				// Something shady going on.
				// TODO: Log exception
			}
		}
	}
}
