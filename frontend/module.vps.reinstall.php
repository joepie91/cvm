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

$display_form = true;

if(isset($_POST['submit']))
{
	if(!empty($_POST['template']))
	{
		try
		{
			$sContainer->CheckAllowed();
			$sTemplate = new Template($_POST['template']);
			$sTemplate->CheckAvailable();
			
			if(isset($_POST['confirm']))
			{
				$sContainer->uTemplateId = $sTemplate->sId;
				$sContainer->InsertIntoDatabase();
				$sContainer->Reinstall();
				$sContainer->Start();
				
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-reinstall-success-title'], $locale->strings['error-reinstall-success-text']);
				$sPageContents .= $err->Render();
			}
			else
			{
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-confirm-title'], $locale->strings['error-reinstall-confirm-text']);
				$sPageContents .= $err->Render();
			}
		}
		catch (NotFoundException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-notfound-title'], $locale->strings['error-reinstall-notfound-text']);
			$sPageContents .= $err->Render();
		}
		catch (TemplateUnavailableException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-unavailable-title'], $locale->strings['error-reinstall-unavailable-text']);
			$sPageContents .= $err->Render();
		}
		catch (ContainerReinstallException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-failed-title'], $locale->strings['error-reinstall-failed-text']);
			$sPageContents .= $err->Render();
		}
		catch (ContainerStartException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, $locale->strings['error-reinstall-start-title'], $locale->strings['error-reinstall-start-text']);
			$sPageContents .= $err->Render();
		}
		catch (ContainerSuspendedException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-suspended-title'], $locale->strings['error-reinstall-suspended-text']);
			$sPageContents .= $err->Render();
		}
		catch (ContainerTerminatedException $e)
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-reinstall-terminated-title'], $locale->strings['error-reinstall-terminated-text']);
			$sPageContents .= $err->Render();
		}
	}
	else
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "No template selected", "You did not select a template from the list. Please select a template and try again.");
		$sPageContents .= $err->Render();
	}
}

if($display_form === true)
{
	$result = mysql_query_cached("SELECT * FROM templates WHERE `Available` = '1'");

	$sTemplateList = array();

	foreach($result->data as $row)
	{
		$sTemplate = new Template($row);
		$sTemplateList[] = array(
			'id'		=> $sTemplate->sId,
			'name'		=> $sTemplate->sName,
			'description'	=> $sTemplate->sDescription
		);
	}

	$sPageContents .= Templater::AdvancedParse("vps.reinstall", $locale->strings, array(
		'templates'	=> $sTemplateList
	));
}
