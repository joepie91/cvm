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

$template_cache = array();

class Templater
{
	private $basedir = "templates/";
	private $extension = ".tpl";
	private $tpl = NULL;
	private $tpl_rendered = NULL;
	
	public function Load($template)
	{
		global $template_cache;
		
		if(isset($template_cache[$template]))
		{
			$tpl_contents = $template_cache[$template];
		}
		else
		{
			$tpl_contents = file_get_contents($this->basedir . $template . $this->extension);
			$template_cache[$template] = $tpl_contents;
		}
		
		if($tpl_contents !== false)
		{
			$this->tpl = $tpl_contents;
			$this->tpl_rendered = $tpl_contents;
		}
		else
		{
			Throw new Exception("Failed to load template {$template}.");
		}
	}
	
	public function Reset()
	{
		if(!is_null($this->tpl))
		{
			$this->tpl_rendered = $this->tpl;
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	public function Localize($strings)
	{
		if(!is_null($this->tpl))
		{
			preg_match_all("/<%!([a-zA-Z0-9_-]+)>/", $this->tpl_rendered, $strlist);
			foreach($strlist[1] as $str)
			{
				if(isset($strings[$str]))
				{
					$this->tpl_rendered = str_replace("<%!{$str}>", $strings[$str], $this->tpl_rendered);
				}
			}
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	public function Compile($strings)
	{
		if(!is_null($this->tpl))
		{
			$this->tpl_rendered = preg_replace_callback("/<%foreach ([a-z0-9_-]+) in ([a-z0-9_-]+)>(.*?)<%\/foreach>/si", function($matches) use($strings) {
				$variable_name = $matches[1];
				$array_name = $matches[2];
				$template = $matches[3];
				$returnvalue = "";
				
				if(isset($strings[$array_name]))
				{
					foreach($strings[$array_name] as $item)
					{
						$rendered = $template;
						
						foreach($item as $key => $value)
						{
							$rendered = str_replace("<%?{$variable_name}[{$key}]>", $value, $rendered);
						}
						
						$returnvalue .= $rendered;
					}
					
					return $returnvalue;
				}
				
				return false;
			}, $this->tpl_rendered);
			
			$this->tpl_rendered = preg_replace_callback("/<%if ([a-z0-9_-]+) (=|==|>|<|>=|<=|!=) ([^>]+)>(.*?)<%\/if>/si", function($matches) use($strings) {
				$variable_name = $matches[1];
				$operator = $matches[2];
				$value = $matches[3];
				$template = $matches[4];
				
				if(isset($strings[$variable_name]))
				{
					$variable = $strings[$variable_name];
					
					if($variable == "true") { $variable = true; }
					if($variable == "false") { $variable = false; }
					if(is_numeric($variable)) { $variable = (int)$variable; }
					if($value == "true") { $value = true; }
					if($value == "false") { $value = false; }
					if(is_numeric($value)) { $value = (int)$value; }
					
					var_dump($variable, $operator, $value);
					
					switch($operator)
					{
						case "=":
						case "==":
							$display = ($variable == $value);
							break;
						case ">":
							$display = ($variable > $value);
							break;
						case "<":
							$display = ($variable < $value);
							break;
						case ">=":
							$display = ($variable >= $value);
							break;
						case "<=":
							$display = ($variable <= $value);
							break;
						case "!=":
							$display = ($variable != $value);
							break;
						default:
							return false;
							break;
					}
					
					var_dump($display);
					
					if($display === true)
					{
						return $template;
					}
					else
					{
						return "";
					}
				}
				
				return false;
			}, $this->tpl_rendered);
			
			preg_match_all("/<%\?([a-zA-Z0-9_-]+)>/", $this->tpl_rendered, $strlist);
			foreach($strlist[1] as $str)
			{
				if(isset($strings[$str]))
				{
					$this->tpl_rendered = str_replace("<%?{$str}>", $strings[$str], $this->tpl_rendered);
				}
			}
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	public function Render()
	{
		if(!is_null($this->tpl))
		{
			return $this->tpl_rendered;
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	public function Output()
	{
		if(!is_null($this->tpl))
		{
			echo($this->tpl_rendered);
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	public static function InlineRender($templatename, $localize = array(), $compile = array())
	{
		$template = new Templater();
		$template->Load($templatename);
		$template->Localize($localize);
		$template->Compile($compile);
		return $template->Render();
	}
}
