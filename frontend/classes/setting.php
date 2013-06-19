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

class Setting extends CPHPDatabaseRecordClass
{
	public $table_name = "settings";
	public $fill_query = "SELECT * FROM settings WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM settings WHERE `Id` = :Id";
	
	public $prototype = array(
		'string' => array(
			"Key"		=> "Key",
			"Value"		=> "Value"
		),
		'timestamp' => array(
			"LastChanged"	=> "LastChanged"
		)
	);
	
	public static function ByKey($key, $cache_duration = 60)
	{
		return Setting::CreateFromQuery("SELECT * FROM settings WHERE `Key` = :Key", array(":Key" => $key), $cache_duration, true);
	}
	
	public function ChangeValue($value)
	{
		$this->uValue = $value;
		$this->uLastChanged = time();
		$this->InsertIntoDatabase();
	}
}
