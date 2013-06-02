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

$_CPHP_CONFIG = "../config.json";
$_CPHP = true;
require("cphp/base.php");
require("include.config.php");
require("include.exceptions.php");
require("include.constants.php");
require("include.parsing.php");
require("include.misc.php");

function __autoload($class_name) 
{
	global $_APP;
	
	if(strpos($class_name, "\\") !== false)
	{
		$class_name = str_replace("\\", "/", strtolower($class_name));
		require_once("classes/{$class_name}.php");
	}
	else
	{
		$class_name = strtolower($class_name);
		require_once("classes/{$class_name}.php");
	}
}
