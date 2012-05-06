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
			$this->tpl_rendered = $this->ParseForEach($this->tpl_rendered, $strings);
			$this->tpl_rendered = $this->ParseIf($this->tpl_rendered, $strings);
			
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
	
	public function ParseForEach($source, $data)
	{
		$templater = $this;
		
		return preg_replace_callback("/<%foreach ([a-z0-9_-]+) in ([a-z0-9_-]+)>(.*?)<%\/foreach>/si", function($matches) use($data, $templater) {
			$variable_name = $matches[1];
			$array_name = $matches[2];
			$template = $matches[3];
			$returnvalue = "";
			
			if(isset($data[$array_name]))
			{
				foreach($data[$array_name] as $item)
				{
					$rendered = $template;
					
					$rendered = $templater->ParseIf($rendered, $data, $item, $variable_name);
					
					foreach($item as $key => $value)
					{
						$rendered = str_replace("<%?{$variable_name}[{$key}]>", $value, $rendered);
					}
					
					$returnvalue .= $rendered;
				}
				
				return $returnvalue;
			}
			
			return false;
		}, $source);
	}
	
	public function ParseIf($source, $data, $context = null, $identifier = "")
	{
		return preg_replace_callback("/<%if ([][a-z0-9_-]+) (=|==|>|<|>=|<=|!=) ([^>]+)>(.*?)<%\/if>/si", function($matches) use($data, $context, $identifier) {
			$variable_name = $matches[1];
			$operator = $matches[2];
			$value = $matches[3];
			$template = $matches[4];
			
			if(!empty($identifier))
			{
				if(preg_match("/{$identifier}\[([a-z0-9_-]+)\]/i", $variable_name, $submatches))
				{
					// Local variable.
					$name = $submatches[1];
					
					if(isset($context[$name]))
					{
						$variable = $context[$name];
					}
					else
					{
						return false;
					}
				}
				elseif(preg_match("/[a-z0-9_-]+\[[a-z0-9_-]+\]/i", $variable_name))
				{
					// Not the right scope.
					return false;
				}
				else
				{
					// Global variable.
					if(isset($data[$variable_name]))
					{
						$variable = $data[$variable_name];
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				if(isset($data[$variable_name]))
				{
					$variable = $data[$variable_name];
				}
				else
				{
					return false;
				}
			}
			
				
			if($variable === "true") { $variable = true; }
			if($variable === "false") { $variable = false; }
			if(is_numeric($variable)) { $variable = (int)$variable; }
			if($value === "true") { $value = true; }
			if($value === "false") { $value = false; }
			if(is_numeric($value)) { $value = (int)$value; }
			
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
			
			if($display === true)
			{
				return $template;
			}
			else
			{
				return "";
			}
			
			return false;
		}, $source);
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
