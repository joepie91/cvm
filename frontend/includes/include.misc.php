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

function check_fields($source, $fields, &$errors)
{
	/* This function runs through a GET/POST value array, checks if all values are filled in,
	 * and adds error messages to the specified array if this is not the case. */
	foreach($fields as $field_name => $field_error)
	{
		if(empty($source[$field_name]))
		{
			$errors[] = $field_error;
		}
	}
	
	return $errors;
}

function array_add(&$array, $value)
{
	/* Why use a separate function to add an item to an array if the $name[] construct exists?
	 * We only want to add an element to an array if the element isn't in the array yet, hence
	 * the need for a custom function.
	 * NOTE: This function works in-place. */
	if(in_array($value, $array) === false)
	{
		$array[] = $value;
	}
}

function validate_hostname($hostname)
{
	if(preg_match("/^[a-z\d](-*[a-z\d])*(\.[a-z\d](-*[a-z\d])*)*$/", $hostname))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function first_unused_ctid()
{
	/* [OpenVZ only] This function finds the first valid unused CTID and returns it. */
	global $database;
	
	$id_list = array();
	$highest = 101;
	
	/* Collect all known CTIDs and keep track of the highest CTID. */
	if($result = $database->CachedQuery("SELECT `InternalId` FROM containers WHERE `VirtualizationType` = 1", array(), 0))
	{
		foreach($result->data as $row)
		{
			$id = filter_var($row['InternalId'] ,FILTER_VALIDATE_INT);
			
			if($id !== false)
			{
				$id_list[] = $id;
				
				if($id > $highest)
				{
					$highest = $id;
				}
			}
		}
	}
	
	/* Generate a list of all possible CTIDs between 101 and the highest CTID, and find
	 * all possible CTIDs that do not exist in the known CTID list. We use array_merge
	 * because otherwise the array indexes may not start from 0. */
	$all_ids = range(101, $highest, 1);
	$missing = array_merge(array_diff($all_ids, $id_list));
	
	if(count($missing) > 0)
	{
		/* Return the first unused CTID. */
		return $missing[0];
	}
	else
	{
		/* All CTIDs up to the highest CTID have been used. We'll just return the CTID
		 * that is one above the highest known CTID. */
		return $highest + 1;
	}
}

function format_size($input, $multiplier = 1024, $group = false, $decimal_places = 0, $return_array = false)
{
	if($input > pow($multiplier, 8))
	{
		$unit = "Y";
		$number = $input / pow($multiplier, 8);
	}
	elseif($input > pow($multiplier, 7))
	{
		$unit = "Z";
		$number = $input / pow($multiplier, 7);
	}
	elseif($input > pow($multiplier, 6))
	{
		$unit = "E";
		$number = $input / pow($multiplier, 6);
	}
	elseif($input > pow($multiplier, 5))
	{
		$unit = "P";
		$number = $input / pow($multiplier, 5);
	}
	elseif($input > pow($multiplier, 4))
	{
		$unit = "T";
		$number = $input / pow($multiplier, 4);
	}
	elseif($input > pow($multiplier, 3))
	{
		$unit = "G";
		$number = $input / pow($multiplier, 3);
	}
	elseif($input > pow($multiplier, 2))
	{
		$unit = "M";
		$number = $input / pow($multiplier, 2);
	}
	elseif($input > $multiplier)
	{
		$unit = "K";
		$number = $input / $multiplier;
	}
	else
	{
		$unit = "";
		$number = $input;
	}
	
	if($group === true)
	{
		$number = number_format($number, $decimal_places);
	}
	else
	{
		$number = round($number, $decimal_places);
	}
	
	if($return_array == true)
	{
		return array($number, $unit);
	}
	else
	{
		return $number . $unit;
	}
}

function status_code($code)
{
	$codes = array(
		100 => "Continue",
		101 => "Switching Protocols",
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Moved Temporarily",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Time-out",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Large",
		415 => "Unsupported Media Type",
		418 => "I'm a teapot",
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Time-out",
		505 => "HTTP Version not supported",
	);
	
	if(array_key_exists($code, $codes))
	{
		$text = $codes[$code];
	}
	else
	{
		throw new Exception("The specified HTTP status code does not exist.");
	}
	
	if(strpos(php_sapi_name(), "cgi") !== false)
	{
		header("Status: {$code} {$text}");
	}
	else
	{
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header("{$protocol} {$code} {$text}");
	}
}
