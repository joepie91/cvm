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
	try
	{
		if(!empty($_POST['password']) && !empty($_POST['confirm']))
		{
			if($_POST['password'] == $_POST['confirm'])
			{
				$sVps->SetRootPassword($_POST['password']);
				
				$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/success", $locale->strings, array(
					'title'		=> $locale->strings['error-password-success-title'],
					'message'	=> $locale->strings['error-password-success-text']
				));
			}
			else
			{
				$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
					'title'		=> $locale->strings['error-password-nomatch-title'],
					'message'	=> $locale->strings['error-password-nomatch-text']
				));
			}
		}
		else
		{
			$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
				'title'		=> $locale->strings['error-password-missing-title'],
				'message'	=> $locale->strings['error-password-missing-text']
			));
		}
	}
	catch (VpsSuspendedException $e)
	{
		$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-password-suspended-title'],
			'message'	=> $locale->strings['error-password-suspended-text']
		));
	}
	catch (VpsTerminatedException $e)
	{
		$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-password-terminated-title'],
			'message'	=> $locale->strings['error-password-terminated-text']
		));
	}
	catch (SshExitException $e)
	{
		$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/error", $locale->strings, array(
			'title'		=> $locale->strings['error-password-invalid-title'],
			'message'	=> $locale->strings['error-password-invalid-text']
		));
	}
}

if($display_form === true)
{
	$sPageContents .= NewTemplater::Render("{$sTheme}/shared/error/warning", $locale->strings, array(
		'title'		=> $locale->strings['warning-password-title'],
		'message'	=> $locale->strings['warning-password-text']
	));
	
	$sPageContents .= Templater::AdvancedParse("{$sTheme}/client/vps/password", $locale->strings, array(
		'id'	=> $sVps->sId
	));
}
