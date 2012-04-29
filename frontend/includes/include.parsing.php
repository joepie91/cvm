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

function split_whitespace($input)
{
	return preg_split("/\s+/", $input);
}

function split_lines($input)
{
	$lines = explode("/n", $input);
	
	foreach($lines as &$line)
	{
		$line = trim($line);
	}
	
	return $lines;
}

function shrink_command($command)
{
	$command = preg_replace("/(\t+|\n)/", " ", $command);
	$command = str_replace("/r", "", $command);
	return $command;
}

?>
