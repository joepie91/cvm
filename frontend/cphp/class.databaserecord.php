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

abstract class CPHPDatabaseRecordClass extends CPHPBaseClass
{
	public $fill_query = "";
	public $verify_query = "";
	public $table_name = "";
	public $query_cache = 60;
	public $id_field = "Id";
	
	public $prototype = array();
	public $prototype_render = array();	
	public $prototype_export = array();
	public $uData = array();
	
	public $sId = 0;
	
	public function __construct($uDataSource)
	{
		$this->ConstructDataset($uDataSource);
		$this->EventConstructed();
	}
	
	public function ConstructDataset($uDataSource, $uCommunityId = 0)
	{
		$bind_datasets = true;
		
		if(is_numeric($uDataSource))
		{
			if($uDataSource != 0)
			{
				if(!empty($this->fill_query))
				{
					$this->sId = (is_numeric($uDataSource)) ? $uDataSource : 0;
					
					$query = sprintf($this->fill_query, $uDataSource);
					if($result = mysql_query_cached($query, $this->query_cache))
					{
						$uDataSource = $result->data[0];
					}
					else
					{
						$classname = get_class($this);
						throw new NotFoundException("Could not locate {$classname} {$uDataSource} in database.");
					}
				}
				else
				{
					$classname = get_class($this);
					throw new PrototypeException("No fill query defined for {$classname} class.");
				}
			}
			else
			{
				$bind_datasets = false;
			}
		}
		elseif(is_object($uDataSource))
		{
			if(isset($uDataSource->data[0]))
			{
				$uDataSource = $uDataSource->data[0];
			}
			else
			{
				throw new NotFoundException("No result set present in object.");
			}
		}
		elseif(is_array($uDataSource))
		{
			if(isset($uDataSource[0]))
			{
				$uDataSource = $uDataSource[0];
			}
		}
		else
		{
			$classname = get_class($this);
			throw new ConstructorException("Invalid type passed on to constructor for object of type {$classname}.");
		}
		
		if($bind_datasets === true)
		{
			$this->sId = (is_numeric($uDataSource[$this->id_field])) ? $uDataSource[$this->id_field] : 0;
			
			$this->uData = $uDataSource;
			
			foreach($this->prototype as $type => $dataset)
			{
				$this->BindDataset($type, $dataset);
			}
			
			$this->sFound = true;
		}
		else
		{
			$this->sFound = false;
		}
		
		if(!empty($uCommunityId) && !empty($this->sCommunityId))
		{
			$sCommunityId = (is_numeric($uCommunityId)) ? $uCommunityId : 0;
			
			if($sCommunityId != $this->sCommunity->sId)
			{
				$classname = get_class($this);
				throw new OwnershipException("{$classname} {$this->sId} does not belong to Community {$sCommunityId}.");
			}
		}
	}
	
	public function BindDataset($type, $dataset)
	{
		global $cphp_class_map;
		
		if(is_array($dataset))
		{
			foreach($dataset as $variable_name => $column_name) 
			{
				$original_value = $this->uData[$column_name];
				
				switch($type)
				{
					case "string":
						$value = htmlspecialchars(stripslashes($original_value));
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "html":
						$value = filter_html(stripslashes($original_value));
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "simplehtml":
						$value = filter_html_strict(stripslashes($original_value));
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "nl2br":
						$value = nl2br(htmlspecialchars(stripslashes($original_value)), false);
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "numeric":
						$value = (is_numeric($original_value)) ? $original_value : 0;
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "timestamp":
						$value = unix_from_mysql($original_value);
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "boolean":
						$value = (empty($original_value)) ? false : true;
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "user":
						$value = new User($original_value);
						$variable_type = CPHP_VARIABLE_SAFE;
						break;
					case "none":
						$value = $original_value;
						$variable_type = CPHP_VARIABLE_UNSAFE;
						break;
					default:
						$found = false;
						foreach($cphp_class_map as $class_type => $class_name)
						{
							if($type == $class_type)
							{
								$value = new $class_name($original_value);
								$variable_type = CPHP_VARIABLE_SAFE;
								$found = true;
							}
						}
						
						if($found == false)
						{
							$classname = get_class($this);
							throw new Exception("Cannot determine type of dataset ({$type}) passed on to {$classname}.BindDataset."); 
							break;
						}
				}
				
				if($variable_type == CPHP_VARIABLE_SAFE)
				{
					$variable_name_safe = "s" . $variable_name;
					$this->$variable_name_safe = $value;
				}
				
				$variable_name_unsafe = "u" . $variable_name;
				$this->$variable_name_unsafe = $original_value;
			}
		}
		else
		{
			$classname = get_class($this);
			throw new Exception("Invalid dataset passed on to {$classname}.BindDataset."); 
		}
	}
	
	public function DoRenderInternalTemplate()
	{
		if(!empty($this->render_template))
		{
			$strings = array();
			foreach($this->prototype_render as $template_var => $object_var)
			{
				$variable_name = "s" . $object_var;
				$strings[$template_var] = $this->$variable_name;
			}
			return $this->DoRenderTemplate($this->render_template, $strings);
		}
		else
		{
			$classname = get_class($this);
			throw new Exception("Cannot render template: no template defined for {$classname} class.");
		}
	}
	
	public function InsertIntoDatabase()
	{
		if(!empty($this->verify_query))
		{
			if($this->sId == 0)
			{
				$insert_mode = CPHP_INSERTMODE_INSERT;
			}
			else
			{
				$query = sprintf($this->verify_query, $this->sId);
				if($result = mysql_query_cached($query))
				{
					$insert_mode = CPHP_INSERTMODE_UPDATE;
				}
				else
				{
					$insert_mode = CPHP_INSERTMODE_INSERT;
				}
			}
			
			$element_list = array();
			
			foreach($this->prototype as $type_key => $type_value)
			{
				foreach($type_value as $element_key => $element_value)
				{
					switch($type_key)
					{
						case "none":
						case "numeric":
						case "boolean":
						case "timestamp":
						case "string":
							$element_list[$element_value] = array(
								'key'	=> $element_key,
								'type'	=> $type_key
							);
							break;
						default:
							break;
					}
				}
			}
			
			$sKeyList = array();
			$sValueList = array();
			
			foreach($element_list as $sKey => $value)
			{				
				$variable_name_safe = "s" . $value['key'];
				$variable_name_unsafe = "u" . $value['key'];
				
				if(isset($this->$variable_name_safe) || isset($this->$variable_name_unsafe))
				{
					switch($value['type'])
					{
						case "none":
							$sFinalValue = mysql_real_escape_string($this->$variable_name_unsafe);
							break;
						case "numeric":
							$number = (isset($this->$variable_name_unsafe)) ? $this->$variable_name_unsafe : $this->$variable_name_safe;
							$sFinalValue = (is_numeric($number)) ? $number : 0;
							break;
						case "boolean":
							$bool = (isset($this->$variable_name_unsafe)) ? $this->$variable_name_unsafe : $this->$variable_name_safe;
							$sFinalValue = ($bool) ? "1" : "0";
							break;
						case "timestamp":
							$sFinalValue = (isset($this->$variable_name_safe)) ? mysql_from_unix($this->$variable_name_safe) : mysql_from_unix(unix_from_local($this->$variable_name_unsafe));
							break;
						case "string":
							$sFinalValue = (isset($this->$variable_name_unsafe)) ? mysql_real_escape_string($this->$variable_name_unsafe) : mysql_real_escape_string($this->$variable_name_safe);
							break;
						case "default":
							$sFinalValue = mysql_real_escape_string($this->$variable_name_unsafe);
							break;
					}
					
					$sFinalValue = "'{$sFinalValue}'";
					$sKey = "`{$sKey}`";
					
					$sKeyList[] = $sKey;
					$sValueList[] = $sFinalValue;
				}
				else
				{
					$classname = get_class($this);
					throw new Exception("Database insertion failed: prototype property {$value['key']} not found in object of type {$classname}.");
				}
			}
			
			
			if($insert_mode == CPHP_INSERTMODE_INSERT)
			{
				$sQueryKeys = implode(", ", $sKeyList);
				$sQueryValues = implode(", ", $sValueList);
				$query = "INSERT INTO {$this->table_name} ({$sQueryKeys}) VALUES ({$sQueryValues})";
			}
			elseif($insert_mode == CPHP_INSERTMODE_UPDATE)
			{
				$sKeyValueList = array();
				
				for($i = 0; $i < count($sKeyList); $i++)
				{
					$sKey = $sKeyList[$i];
					$sValue = $sValueList[$i];
					$sKeyValueList[] = "{$sKey} = {$sValue}";
				}
				
				$sQueryKeysValues = implode(", ", $sKeyValueList);
				$query = "UPDATE {$this->table_name} SET {$sQueryKeysValues} WHERE `{$this->id_field}` = '{$this->sId}'";
			}
			
			if($result = mysql_query($query))
			{
				if($insert_mode == CPHP_INSERTMODE_INSERT)
				{
					$this->sId = mysql_insert_id();
				}
				
				$this->PurgeCache();
				
				return $result;
			}
			else
			{
				$classname = get_class($this);
				throw new DatabaseException("Database insertion query failed in object of type {$classname}. Error message: " . mysql_error());
			}
		}
		else
		{
			$classname = get_class($this);
			throw new Exception("No verification query defined for {$classname} class.");
		}
	}
	
	public function RetrieveChildren($type, $field)
	{
		// Not done yet!
		
		if(!isset($cphp_class_map[$type]))
		{
			$classname = get_class($this);
			throw new NotFoundException("Non-existent 'type' argument passed on to {$classname}.RetrieveChildren function.");
		}
		
		$parent_type = get_parent_class($cphp_class_map[$type]);
		if($parent_type !== "CPHPDatabaseRecordClass")
		{
			$parent_type = ($parent_type === false) ? "NONE" : $parent_type;
			$classname = get_class($this);
			throw new TypeException("{$classname}.RetrieveChildren expected 'type' argument of parent-type CPHPDatabaseRecordClass, but got {$parent_type} instead.");
		}
		
		$query = "";
	}
	
	public function PurgeCache()
	{
		$query = sprintf($this->fill_query, $this->sId);
		$key = md5($query) . md5($query . "x");
		mc_delete($key);
	}
	
	public function RenderTemplate($template = "")
	{
		if(!empty($template))
		{
			$this->render_template = $template;
		}
		
		return $this->DoRenderInternalTemplate();
	}
	
	public function Export()
	{
		// Exports the object as a nested array. Observes the export prototype.
		$export_array = array();
		
		foreach($this->prototype_export as $field)
		{
			$variable_name = "s{$field}";
			if(is_object($this->$variable_name))
			{
				if(!empty($this->$variable_name->sId))
				{
					$export_array[$field] = $this->$variable_name->Export();
				}
				else
				{
					$export_array[$field] = null;
				}
			}
			else
			{
				$export_array[$field] = $this->$variable_name;
			}
		}
		
		return $export_array;
	}
	
	// Define events
	
	protected function EventConstructed() { }
}
