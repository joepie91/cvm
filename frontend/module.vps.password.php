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
				
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Password configuration succeeded!", "Your new root password was successfully configured. Please ensure to change your root password again from your container after logging in.");
				$sPageContents .= $err->Render();
			}
			else
			{
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Passwords do not match", "Both entries should be identical. Please try again.");
				$sPageContents .= $err->Render();
			}
		}
		else
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Fields missing", "Both fields are required. Please try again.");
			$sPageContents .= $err->Render();
		}
	}
	catch (ContainerSuspendedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Failed to configure root password", "You can not configure the root password for this VPS, because it is suspended. If you believe this is in error, please contact support.");
		$sPageContents .= $err->Render();
	}
	catch (ContainerTerminatedException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Failed to configure root password", "You can not configure the root password for this VPS, because it is suspended. If you believe this is in error, please contact support.");
		$sPageContents .= $err->Render();
	}
	catch (SshExitException $e)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Failed to configure root password", "Your password may be in an invalid format. Try again with a different password.");
		$sPageContents .= $err->Render();
	}
}

if($display_form === true)
{
	$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_WARNING, "Security warning", "Configuring your root password through this panel may expose it to the VPS provider. Only use this feature in an emergency situation, and always change your password again afterwards, from within your container.");
	$sPageContents .= $err->Render();
	
	$sPageContents .= Templater::InlineRender("vps.password", $locale->strings, array(
		'id'	=> $sContainer->sId
	));
}
?>
