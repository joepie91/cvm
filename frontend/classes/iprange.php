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

define("IP_TYPE_NONE", 0);
define("IP_TYPE_IPV4", 4);
define("IP_TYPE_IPV6", 6);
define("INPUT_TYPE_IP", 1);
define("INPUT_TYPE_RANGE", 2);
define("SEGMENT_BITS_IPV4", 8);
define("SEGMENT_BITS_IPV6", 16);

class IpRange
{
	public $sCidrNotation = "";
	public $sType = 0;
	public $sInputType = 0;
	public $sInput = "";
	public $sStart = "";
	public $sEnd = "";
	public $sCount = "";
	
	public function __construct($input)
	{
		$this->sInput = $input;
		
		$slashcount = substr_count($input, "/");
		
		if($slashcount == 1)
		{
			/* The input is probably a CIDR range. */
			$this->sInputType = INPUT_TYPE_RANGE;
			$this->ValidateRangeFormat();
			
		}
		elseif($slashcount == 0)
		{
			/* The input is probably an IP. */
			$this->sInputType = INPUT_TYPE_IP;
			$this->ValidateIpFormat();
		}
		else
		{
			throw new InvalidArgumentException("The given input is not a valid IP or IP range.");
		}
		
		if($this->sInputType == INPUT_TYPE_RANGE)
		{
			if($this->sType == IP_TYPE_IPV6)
			{
				$this->ExpandIpv6();
				$result = IpRange::ParseRange($this->sIp, $this->sSize, ":", SEGMENT_BITS_IPV6, true);
			}
			elseif($this->sType == IP_TYPE_IPV4)
			{
				$result = IpRange::ParseRange($this->sIp, $this->sSize);
			}
			
			$this->sStart = $result['start'];
			$this->sEnd = $result['end'];
			$this->sCount = $result['count'];
		}
		else
		{
			$this->sStart = $this->sIp;
			$this->sEnd = $this->sIp;
			$this->sCount = 1;
		}
	}
	
	private function ValidateRangeFormat()
	{
		list($ip, $size) = explode("/", $this->sInput, 2);
		
		if($this->ValidateIpFormat($ip) === true)
		{
			if(is_numeric($size))
			{
				if($this->sType == IP_TYPE_IPV4 && (int)$size >= 0 && (int)$size <= 32)
				{
					$this->sSize = $size;
					return true;
				}
				elseif($this->sType == IP_TYPE_IPV6 && (int)$size >= 0 && (int)$size <= 128)
				{
					$this->sSize = $size;
					return true;
				}
			}
		}
		
		/* Fallback case. */
		throw new InvalidArgumentException("The given input is not a valid IP or IP range.");
	}
	
	private function ValidateIpFormat($ip = null)
	{
		if(is_null($ip))
		{
			$ip = $this->sInput;
		}
		
		if(strpos($ip, ".") !== false)
		{
			// Probably an IPv4 address.
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			{
				$this->sType = IP_TYPE_IPV4;
				$this->sIp = $ip;
				return true;
			}
		}
		elseif(strpos($ip, ":") !== false)
		{
			// Probably an IPv6 address.
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			{
				$this->sType = IP_TYPE_IPV6;
				$this->sIp = $ip;
				return true;
			}
		}
		
		/* Fallback case. */
		throw new InvalidArgumentException("The given input is not a valid IP or IP range.");
	}
	
	private function ExpandIpv6()
	{
		// Note: this function does NOT do any validation!
		$ip = $this->sIp;
		$parts = explode(":", $ip);
		$empty_part = false;
		$missing = 0;
		
		foreach($parts as $part)
		{
			if($part == "")
			{
				$empty_part = true;
				$missing = 1;
			}
		}
		
		$available_parts = count($parts) - $missing;
		
		if($available_parts < 8 || $empty_part === true)
		{
			$needed = 8 - $available_parts;
			
			$abbrev_position = strpos($ip, "::");
			
			$left = substr($ip, 0, $abbrev_position);
			$right = substr($ip, $abbrev_position + 2);
			
			$left_parts = explode(":", $left);
			$right_parts = explode(":", $right);
			$filler_parts = array_fill(0, $needed, "0000");
			
			$final_array = array_merge($left_parts, $filler_parts);
			$final_array = array_merge($final_array, $right_parts);
		}
		else
		{
			$final_array = $parts;
		}
		
		foreach($final_array as &$part)
		{
			$part = str_pad($part, 4, "0", STR_PAD_LEFT);
		}
		
		$this->sIp = implode(":", $final_array);
		return true;
	}
	
	public static function ParseRange($start, $size, $delimiter = ".", $segment_bits = 8, $hex = false)
	{
		$segments = explode($delimiter, $start);
		
		/* Determine the maximum size of one segment */
		$segment_size = pow(2, $segment_bits);
		
		/* Calculate the amount of bits in the entire IP address */
		$ip_bits = count($segments) * $segment_bits;
		
		/* Calculate the total amount of IPs possible for this IP format */
		$total_ips = pow(2, $ip_bits - $size);
		
		if($hex === true)
		{
			$segments = array_map("hexdec", $segments);
		}
		
		/* Calculate the maximum size (in IPs) of the currently used IP format */
		$max_size = count($segments) * $segment_bits;
		
		/* Determine what segment we are going to modify */
		$applicable_segment = floor($size / $segment_bits) + 1;
		
		/* Ensure that the specified size is possible */
		if($size > $max_size)
		{
			/* The size exceeds the total space for this type of IP */
			return false;
		}
		elseif($size == $max_size)
		{
			/* Only 1 IP. */
			$start_segments = $segments;
		}
		else
		{
			/* Determine the amount of IPs for the given size */
			$class_size = pow(2, ($applicable_segment * $segment_bits) - $size);
			
			/* Round down the applicable segment if necessary to ensure the starting point is valid */
			$segments[$applicable_segment - 1] = floor($segments[$applicable_segment - 1] / $class_size) * $class_size;
			
			$start_segments = $segments;
			
			/* Add the amount of IPs (inclusive) to the applicable segment */
			$segments[$applicable_segment - 1] += $class_size - 1;
			
			/* Set all segments to the right of the applicable segment to the right value. */
			if(count($segments) > $applicable_segment - 1)
			{
				for($i = $applicable_segment + 1; $i <= count($segments); $i++)
				{
					$segments[$i - 1] = $segment_size - 1;
					$start_segments[$i - 1] = 0;
				}
			}
		}
		
		if($hex === true)
		{
			$segments = array_map("dechex", $segments);
			$start_segments = array_map("dechex", $start_segments);
		}
		
		return array(
			'start'	=> implode($delimiter, $start_segments),
			'end'	=> implode($delimiter, $segments),
			'count'	=> $total_ips
		);
	}
}

?>
