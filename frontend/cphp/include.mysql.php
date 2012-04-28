<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if($_CPHP !== true) { die(); }

$cphp_mysql_connected = false;

if($cphp_mysql_enabled === true)
{
	if(mysql_connect($cphp_mysql_host, $cphp_mysql_user, $cphp_mysql_pass))
	{
		if(mysql_select_db($cphp_mysql_db))
		{
			$cphp_mysql_connected = true;
		}
	}
}
