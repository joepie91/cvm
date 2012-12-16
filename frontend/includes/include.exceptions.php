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

// SshConnector-related exceptions
class SshException extends Exception {}
class SshConnectException extends SshException {}
class SshAuthException extends SshException {}
class SshCommandException extends SshException {}
class SshExitException extends SshException {}

// VPS-related exceptions
class VpsException extends Exception
{
	private $id = "";
	
	public function __construct($message = "", $code = 0, $id = "", $previous = null)
	{
		$this->id = $id;
		
		parent::__construct($message, $code, $previous);
	}
	
	public function getId()
	{
		return $this->id;
	}
}

class VpsCreateException extends VpsException {}
class VpsConfigureException extends VpsException {}
class VpsStartException extends VpsException {}
class VpsStopException extends VpsException {}
class VpsSuspendException extends VpsException {}
class VpsUnsuspendException extends VpsException {}
class VpsSuspendedException extends VpsException {}
class VpsTerminatedException extends VpsException {}
class VpsDestroyException extends VpsException {}
class VpsReinstallException extends VpsException {}
class VpsDeployException extends VpsException {}
class VpsIpAddException extends VpsException {}
class VpsIpRemoveException extends VpsException {}
class VpsTrafficRetrieveException extends VpsException {}

class UnauthorizedException extends Exception {}
class InsufficientAccessLevelException extends Exception {}
class TemplateUnavailableException extends Exception {}

class ParsingException extends Exception {}
