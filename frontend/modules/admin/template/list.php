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

$sTemplates = array();

try
{
	foreach(Template::CreateFromQuery("SELECT * FROM templates") as $sTemplate)
	{
		$sTemplates[] = array(
			"id" => $sTemplate->sId,
			"name" => $sTemplate->sName,
			"filename" => $sTemplate->sTemplateName,
			"description" => $sTemplate->sDescription,
			"supported" => $sTemplate->sIsSupported,
			"outdated" => $sTemplate->sIsOutdated,
			"available" => $sTemplate->sIsAvailable
		);
	}
}
catch (NotFoundException $e)
{
	/* pass */
}

$sPageContents = NewTemplater::Render("{$sTheme}/admin/template/list", $locale->strings, array(
	"templates"	=> $sTemplates
));
