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

cphp_dependency_provides("cphp_router", "1.0");

class CPHPRouter extends CPHPBaseClass
{
	public $routes = array();
	public $parameters = array();
	public $custom_query = "";
	
	public function RouteRequest()
	{
		eval(extract_globals()); // hack hackity hack hack
		
		if(!empty($this->custom_query))
		{
			$requestpath = $this->custom_query;
		}
		else
		{
			if(!empty($_SERVER['REQUEST_URI']))
			{
				$requestpath = trim($_SERVER['REQUEST_URI']);
			}
			else
			{
				$requestpath = "/";
			}
		}
		
		$found = false;  // Workaround because a break after an include apparently doesn't work in PHP.
		
		foreach($this->routes as $priority)
		{
			foreach($priority as $route_regex => $route_destination)
			{
				if($found === false)
				{
					$regex = str_replace("/", "\/", $route_regex);
					if(preg_match("/{$regex}/i", $requestpath, $matches))
					{
						$this->uParameters = $matches;
						include($route_destination);
						$found = true;
					}
				}
			}
		}
	}
}
?>
