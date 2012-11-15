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
	$result = $database->CachedQuery("SELECT `InternalId` FROM containers WHERE `VirtualizationType` = 1", array(), 0);
	
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