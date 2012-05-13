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
				$sContainer->SetRootPassword($_POST['password']);
				
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['error-password-success-title'], $locale->strings['error-password-success-text']);
				$sPageContents .= $err->Render();
			}
			else
			{
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-password-nomatch-title'], $locale->strings['error-password-nomatch-text']);
				$sPageContents .= $err->Render();
			}
		}
		else
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-password-missing-title'], $locale->strings['error-password-missing-text']);
			$sPageContents .= $err->Render();
		}
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-password-suspended-title'], $locale->strings['error-password-suspended-text']);
		$sPageContents .= $err->Render();
	}
	catch (ContainerTerminatedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-password-terminated-title'], $locale->strings['error-password-terminated-text']);
		$sPageContents .= $err->Render();
	}
	catch (SshExitException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['error-password-invalid-title'], $locale->strings['error-password-invalid-text']);
		$sPageContents .= $err->Render();
	}
}

if($display_form === true)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, $locale->strings['warning-password-title'], $locale->strings['warning-password-text']);
	$sPageContents .= $err->Render();
	
	$sPageContents .= Templater::AdvancedParse("vps.password", $locale->strings, array(
		'id'	=> $sContainer->sId
	));
}
