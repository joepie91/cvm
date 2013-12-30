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

if($router->uMethod == "post")
{
	$handler = new CPHPFormHandler();
	
	try
	{
		$handler
			->RequireKey("filename")
			->RequireKey("name")
			->RequireKey("description")
			->RequireNonEmpty("filename")
			->RequireNonEmpty("name")
			->ValidateCustom("filename", "The specified template file does not exist.", function($key, $value, $args, $handler){
				return file_exists("/etc/cvm/templates/{$value}");
			})
			->Done();
			
		foreach($handler->GetGroupedValues("filename", "name", "description") as $uTemplateData)
		{
			$sTemplate = new Template();
			$sTemplate->uName = $uTemplateData["name"];
			$sTemplate->uTemplateName = $uTemplateData["filename"];
			$sTemplate->uDescription = $uTemplateData["description"];
			$sTemplate->uIsSupported = true;
			$sTemplate->uIsOutdated = false;
			$sTemplate->uIsAvailable = true;
			$sTemplate->InsertIntoDatabase();
		}
		
		redirect("/admin/templates/");
	}
	catch (FormValidationException $e)
	{
		var_dump($e->GetOffendingKeys());
		var_dump($e->GetErrors());
	}
}
else
{
	$sUnknownTemplates = array();

	$handle = opendir("/etc/cvm/templates");
	while(($filename = readdir($handle)) !== false)
	{
		if($filename != "." && $filename != "..")
		{
			try
			{
				Template::CreateFromQuery("SELECT * FROM templates WHERE `TemplateName` = :Filename", array("Filename" => $filename), 0);
			}
			catch (NotFoundException $e)
			{
				$sUnknownTemplates[] = $filename;
			}
		}
	}
	closedir($handle);

	$sPageContents = NewTemplater::Render("{$sTheme}/admin/template/add", $locale->strings, array(
		"templates"	=> $sUnknownTemplates
	));
}
