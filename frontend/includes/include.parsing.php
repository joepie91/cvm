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

function parse_size($size, $multiplier = 1024)
{
	/* Note that this function will always assume bytes and the given multiplier, regardless of
	 * what is actually specified. */
	if(preg_match("/(-?[0-9.,]+)\s*(([kKmMgGtTpPeEzZyY]?)([iI]?)([bB]?))/", $size, $matches))
	{
		$number = (float) $matches[1];
		$unit = $matches[2];
		$prefix = $matches[3];
		$suffix = $matches[5];
		
		if(empty($prefix))
		{
			/* Size is in bytes. */
			return $number;
		}
		else
		{
			switch(strtolower($prefix))
			{
				case "y":
					$number = $number * $multiplier;
				case "z":
					$number = $number * $multiplier;
				case "e":
					$number = $number * $multiplier;
				case "p":
					$number = $number * $multiplier;
				case "t":
					$number = $number * $multiplier;
				case "g":
					$number = $number * $multiplier;
				case "m":
					$number = $number * $multiplier;
				case "k":
					$number = $number * $multiplier;
					break;
				default:
					throw new ParsingException("No valid unit was specified.");
			}
			
			return $number;
		}
	}
	elseif(is_numeric($size))
	{
		return (int) $size;
	}
	else
	{
		throw new ParsingException("The given size specification could not be parsed.");
	}
}

?>
