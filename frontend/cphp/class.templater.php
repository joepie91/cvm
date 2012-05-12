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
$template_global_vars = array();

define("CPHP_TEMPLATER_SWITCH_NONE",		1);
define("CPHP_TEMPLATER_SWITCH_TAG_OPEN",	2);
define("CPHP_TEMPLATER_SWITCH_TAG_SYNTAX",	3);
define("CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER",	4);
define("CPHP_TEMPLATER_SWITCH_TAG_STATEMENT",	5);
define("CPHP_TEMPLATER_SWITCH_TAG_VARNAME",	6);
define("CPHP_TEMPLATER_TYPE_TAG_NONE",		10);
define("CPHP_TEMPLATER_TYPE_TAG_OPEN",		11);
define("CPHP_TEMPLATER_TYPE_TAG_CLOSE",		12);

class Templater
{
	public $basedir = "templates/";
	public $extension = ".tpl";
	private $tpl = NULL;
	private $tpl_rendered = NULL;
	public $templatename = "";
	public $root = null;
	public $debug_tree = array();
	
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
			foreach($strings as $key => $str)
			{
				$this->tpl_rendered = str_replace("<%!{$key}>", $str, $this->tpl_rendered);
				$this->tpl_rendered = str_replace("{%!{$key}}", $str, $this->tpl_rendered);
			}
		}
		else
		{
			Throw new Exception("No template loaded.");
		}
	}
	
	/* Legacy parser code */
	
	public function Compile($strings)
	{
		global $template_global_vars;
		
		if(!is_null($this->tpl))
		{
			$strings = array_merge($strings, $template_global_vars);
			
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
	
	/* New parser code */
	
	public static function AdvancedParse($templatename, $localize = array(), $compile = array())
	{		
		$template = new Templater();
		$template->templatename = $template->basedir . $templatename . $template->extension;;
		$template->Load($templatename);
		$template->Localize($localize);
		return $template->Parse($compile);
	}
	
	public function Parse($data)
	{
		global $template_global_vars;
		
		$tree = $this->BuildSyntaxTree();
		$data = array_merge($data, $template_global_vars);
		return $this->root->Evaluate($data);
	}
	
	public function BuildSyntaxTree()
	{
		$content = $this->tpl_rendered;
		$length = strlen($content);
		$offset = 0;
		$depth = 1;
		$current_tag = array();
		$current_element = array();
		$current_text_element = null;
		$debug_tree = array();
		$root = array();
		$tag_start = 0;
		$tag_end = 0;
		$text_block = "";
		
		$switch = CPHP_TEMPLATER_SWITCH_NONE;
		$type = CPHP_TEMPLATER_TYPE_TAG_NONE;
		
		$current_element[0] = new TemplateRootElement();
		
		while($offset < $length)
		{
			$char = $content[$offset];
			
			if($char == "{" && $switch == CPHP_TEMPLATER_SWITCH_NONE)
			{
				$switch = CPHP_TEMPLATER_SWITCH_TAG_OPEN;
				$tag_start = $offset;
			}
			elseif($char == "%" && $switch == CPHP_TEMPLATER_SWITCH_TAG_OPEN)
			{
				if($text_block != "")
				{
					$current_text_element->text = $text_block;
					$text_block = "";
				
					$current_element[$depth - 1]->children[] = $current_text_element;
				}
				
				// Look ahead to see whether this is going to be a variable or a logic element
				if($content[$offset + 1] == "?")
				{
					$switch = CPHP_TEMPLATER_SWITCH_TAG_VARNAME;
					$name = "";
				}
				else
				{
					$switch = CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER;
					$identifier = "";
				}
			}
			elseif($char != "%" && $switch == CPHP_TEMPLATER_SWITCH_TAG_OPEN)
			{
				// Not a templater tag, abort.
				$switch = CPHP_TEMPLATER_SWITCH_NONE;
				$type = CPHP_TEMPLATER_TYPE_TAG_NONE;
				$text_block .= "{";
			}
			elseif($switch == CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER && $type == CPHP_TEMPLATER_TYPE_TAG_NONE)
			{
				if($char == "/")
				{
					$type = CPHP_TEMPLATER_TYPE_TAG_CLOSE;
				}
				else
				{
					$type = CPHP_TEMPLATER_TYPE_TAG_OPEN;
					continue;
				}
			}
			else
			{
				if(($char != " " && $switch == CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER && $type == CPHP_TEMPLATER_TYPE_TAG_OPEN) ||
				($char != "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER && $type == CPHP_TEMPLATER_TYPE_TAG_CLOSE))
				{
					$identifier .= $char;
				}
				elseif($char != "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_VARNAME)
				{
					if($char != "?" || $name != "")
					$name .= $char;
				}
				elseif($char == " " && $switch == CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER && $type == CPHP_TEMPLATER_TYPE_TAG_OPEN)
				{
					$switch = CPHP_TEMPLATER_SWITCH_TAG_STATEMENT;
					$statement = "";
				} 
				elseif($char != "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_STATEMENT)
				{
					$statement .= $char;
				}
				elseif(($char == "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_STATEMENT && $type == CPHP_TEMPLATER_TYPE_TAG_OPEN) ||
				($char == "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_IDENTIFIER && $type == CPHP_TEMPLATER_TYPE_TAG_CLOSE))
				{
					$tag_end = $offset + 1;
					
					if($type == CPHP_TEMPLATER_TYPE_TAG_OPEN)
					{
						// This was an opening tag.
						$debug_tree[] = "[{$depth}]" . str_repeat("&nbsp;&nbsp;&nbsp;", $depth) . "{$identifier} {$statement}";
						$child = $this->CreateSyntaxElement($identifier, $statement, $offset + 1);
						$current_element[$depth] = $child;
						
						if(isset($current_element[$depth - 1]) && !is_null($current_element[$depth - 1]))
						{
							$child->parent = $current_element[$depth - 1];
							$child->parent->children[] = $current_element[$depth];
						}
						
						$current_tag[$depth] = $identifier;
						
						$depth += 1;
					}
					elseif($type == CPHP_TEMPLATER_TYPE_TAG_CLOSE)
					{
						// This was a closing tag.
						$depth -= 1;
						
						if($identifier == $current_tag[$depth])
						{
							$debug_tree[] = "[{$depth}]" . str_repeat("&nbsp;&nbsp;&nbsp;", $depth) . "/{$identifier}";
							
						}
						else
						{
							throw new TemplateSyntaxException("Closing tag does not match opening tag (".$current_tag[$depth]." vs. {$identifier}) at position {$tag_start}.", $this->templatename, $tag_start, $tag_end);
						}
					}
					else
					{
						throw new TemplateParsingException("The type of tag could not be determined.", $this->templatename, $tag_start, $tag_end);
					}
					
					$switch = CPHP_TEMPLATER_SWITCH_NONE;
					$type = CPHP_TEMPLATER_TYPE_TAG_NONE;
					$identifier = "";
					$statement = "";
				}
				elseif($char == "}" && $switch == CPHP_TEMPLATER_SWITCH_TAG_VARNAME)
				{
					$debug_tree[] = "[{$depth}]" . str_repeat("&nbsp;&nbsp;&nbsp;", $depth) . "var {$name}";
					
					$child = $this->CreateSyntaxElement("variable", $name);
					
					if(isset($current_element[$depth - 1]) && !is_null($current_element[$depth - 1]))
					{
						$child->parent = $current_element[$depth - 1];
						$child->parent->children[] = $child;
					}
					
					$switch = CPHP_TEMPLATER_SWITCH_NONE;
					$type = CPHP_TEMPLATER_TYPE_TAG_NONE;
					$name = "";
				}
				else
				{
					if($text_block == "")
					{
						$current_text_element = $this->CreateSyntaxElement("text", "", $offset);
					}
					
					$text_block .= $char;
				}
			}
			
			$offset += 1;
		}
		
		if($text_block != "")
		{
			$current_text_element->text = $text_block;
			$text_block = "";
		
			$current_element[0]->children[] = $current_text_element;
		}
		
		$this->root = $current_element[0];
		$this->debug_tree = $debug_tree;
	}
	
	function CreateSyntaxElement($identifier, $statement)
	{
		if($identifier == "if")
		{
			$element = new TemplateIfElement;
		}
		elseif($identifier == "foreach")
		{
			$element = new TemplateForEachElement;
		}
		elseif($identifier == "text")
		{
			$element = new TemplateTextElement;
		}
		elseif($identifier == "variable")
		{
			$element = new TemplateVariableElement;
		}
		else
		{
			$element = new TemplateRootElement;
		}
		
		if($identifier == "if" || $identifier == "foreach")
		{
			$element->statement = $statement;
		}
		
		if($identifier == "if")
		{
			$statement_parts = explode(" ", $statement, 3);
			$element->left = $statement_parts[0];
			$element->operator = $statement_parts[1];
			$element->right = $statement_parts[2];
		}
		
		if($identifier == "foreach")
		{
			$statement_parts = explode(" ", $statement, 3);
			$element->varname = $statement_parts[0];
			$element->source = $statement_parts[2];
		}
		
		if($identifier == "variable")
		{
			$element->variable = $statement;
		}
		
		return $element;
	}
}

/* Syntax element definitions */

class TemplateSyntaxElement
{
	public $parent = null;
	public $children = array();
	public $data = array();
	
	public function Evaluate($data)
	{
		$result = "";
		
		foreach($this->children as $child)
		{
			$result .= $child->Evaluate($data);
		}
		
		return $result;
	}
	
	public function FetchVariable($name, $data)
	{
		if(strpos($name, "[") === false)
		{
			return $data[$name];
		}
		else
		{
			// Variable refers to a subset of the data, traverse up the tree to find the matching dataset
			$open_brace = strpos($name, "[");
			$closing_brace = strpos($name, "]");
			
			$source = substr($name, 0, $open_brace);
			$item = substr($name, $open_brace + 1, ($closing_brace - $open_brace - 1));
			
			$current_element = $this;
			
			while(!is_null($current_element))
			{
				$current_element = $current_element->parent;
				
				if(isset($current_element->varname))
				{
					if($current_element->varname == $source)
					{
						if(isset($current_element->data[$item]))
						{
							return $current_element->data[$item];
						}
						else
						{
							return false;
						}
					}
				}
			}
		}
	}
}

class TemplateRootElement extends TemplateSyntaxElement {}

class TemplateVariableElement extends TemplateSyntaxElement
{
	public $variable = "";
	
	public function Evaluate($data)
	{
		return $this->FetchVariable($this->variable, $data);
	}
}

class TemplateTextElement extends TemplateSyntaxElement
{
	public $text = "";
	
	public function Evaluate($data)
	{
		return $this->text;
	}
}

class TemplateIfElement extends TemplateSyntaxElement
{
	public $statement = "";
	public $left = "";
	public $right = "";
	public $operator = "";
	
	public function Evaluate($data)
	{
		$a = $this->FetchVariable($this->left, $data);
		$b = $this->right;
		
		switch($this->operator)
		{
			case "=":
			case "==":
				$result = ($a == $b);
				break;
			case ">":
				$result = ($a > $b);
				break;
			case "<":
				$result = ($a < $b);
				break;
			case ">=":
				$result = ($a >= $b);
				break;
			case "<=":
				$result = ($a <= $b);
				break;
			case "!=":
				$result = ($a != $b);
				break;
			default:
				$result = false;
		}
		
		if($result == true)
		{
			return parent::Evaluate($data);
		}
	}
}

class TemplateForEachElement extends TemplateSyntaxElement
{
	public $statement = "";
	public $source = "";
	public $varname = "";
	public $block = "";
	public $data = array();
	
	public function Evaluate($data)
	{
		$target = $this->FetchVariable($this->source, $data);
		
		$result = "";
		
		foreach($target as $iteration)
		{
			$this->data = $iteration;
			
			foreach($this->children as $child)
			{
				$result .= $child->Evaluate($data);
			}
		}
		
		return $result;
	}
}
