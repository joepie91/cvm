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

class Template extends CPHPDatabaseRecordClass
{
	public $table_name = "templates";
	public $fill_query = "SELECT * FROM templates WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM templates WHERE `Id` = '%d'";
	
	public $prototype = array(
		'string' => array(
			'Name'			=> "Name",
			'TemplateName'		=> "TemplateName",
			'Description'		=> "Description"
		),
		'boolean' => array(
			'IsSupported'		=> "Supported",
			'IsAvailable'		=> "Available"
			'IsOutdated'		=> "Outdated"
		)
	);
}

?>
