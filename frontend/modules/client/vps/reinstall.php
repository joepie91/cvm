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

$display_form = true;

if(isset($_POST['submit']))
{
	if(!empty($_POST['template']))
	{
		try
		{
			$sVps->CheckAllowed();
			$sTemplate = new Template($_POST['template']);
			$sTemplate->CheckAvailable();
			
			if(isset($_POST['confirm']))
			{
				$sVps->uTemplateId = $sTemplate->sId;
				$sVps->InsertIntoDatabase();
				$sVps->Reinstall();
				$sVps->Start();
				
				$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
					'title'		=> $locale->strings['error-reinstall-success-title'],
					'message'	=> $locale->strings['error-reinstall-success-text']
				));
			}
			else
			{
				$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
					'title'		=> $locale->strings['error-reinstall-confirm-title'],
					'message'	=> $locale->strings['error-reinstall-confirm-text']
				));
			}
		}
		catch (NotFoundException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-notfound-title'],
				'message'	=> $locale->strings['error-reinstall-notfound-text']
			));
		}
		catch (TemplateUnavailableException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-unavailable-title'],
				'message'	=> $locale->strings['error-reinstall-unavailable-text']
			));
		}
		catch (VpsReinstallException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-failed-title'],
				'message'	=> $locale->strings['error-reinstall-failed-text']
			));
		}
		catch (VpsStartException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-start-title'],
				'message'	=> $locale->strings['error-reinstall-start-text']
			));
		}
		catch (VpsSuspendedException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-suspended-title'],
				'message'	=> $locale->strings['error-reinstall-suspended-text']
			));
		}
		catch (VpsTerminatedException $e)
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-reinstall-terminated-title'],
				'message'	=> $locale->strings['error-reinstall-terminated-text']
			));
		}
	}
	else
	{
		$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-reinstall-notselected-title'],
			'message'	=> $locale->strings['error-reinstall-notselected-text']
		));
	}
}

if($display_form === true)
{
	$result = $database->CachedQuery("SELECT * FROM templates WHERE `Available` = '1'");

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

	$sPageContents .= NewTemplater::Render("{$sTheme}/client/vps/reinstall", $locale->strings, array(
		'templates'	=> $sTemplateList
	));
}
