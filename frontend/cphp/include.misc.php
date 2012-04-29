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

function random_string($length)
{
	$output = "";
	for ($i = 0; $i < $length; $i++) 
	{ 
		$output .= substr("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 61), 1); 
	}
	return $output;
}

function extract_globals()
{
    $vars = array();
    
    foreach($GLOBALS as $key => $value){
        $vars[] = "$".$key;
    }
    
    return "global " . join(",", $vars) . ";";
}

function utf8entities($utf8) 
{
	// Credits to silverbeat@gmx.at (http://www.php.net/manual/en/function.htmlentities.php#96648)
	$encodeTags = true;
	$result = '';
	for ($i = 0; $i < strlen($utf8); $i++) 
	{
		$char = $utf8[$i];
		$ascii = ord($char);
		if ($ascii < 128) 
		{
			$result .= ($encodeTags) ? htmlentities($char) : $char;
		} 
		else if ($ascii < 192) 
		{
			// Do nothing.
		} 
		else if ($ascii < 224) 
		{
			$result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
			$i++;
		} 
		else if ($ascii < 240) 
		{
			$ascii1 = ord($utf8[$i+1]);
			$ascii2 = ord($utf8[$i+2]);
			$unicode = (15 & $ascii) * 4096 +
			(63 & $ascii1) * 64 +
			(63 & $ascii2);
			$result .= "&#$unicode;";
			$i += 2;
		} 
		else if ($ascii < 248) 
		{
			$ascii1 = ord($utf8[$i+1]);
			$ascii2 = ord($utf8[$i+2]);
			$ascii3 = ord($utf8[$i+3]);
			$unicode = (15 & $ascii) * 262144 +
			(63 & $ascii1) * 4096 +
			(63 & $ascii2) * 64 +
			(63 & $ascii3);
			$result .= "&#$unicode;";
			$i += 3;
		}
	}
	return $result;
}

function clean_array($arr)
{
	$result = array();
	foreach($arr as $key => $value)
	{
		if(!empty($value))
		{
			$result[$key] = $value;
		}
	}
	return $result;
}

function pretty_dump($input)
{
	ob_start();
	var_dump($input);
	$output = ob_get_contents();
	ob_end_clean();
	$output = nl2br(str_replace(" ", "&nbsp;&nbsp;&nbsp;", $output));
	echo($output);
}

/*function is_empty($variable)
{
	return (trim($variable) == "");
}*/
