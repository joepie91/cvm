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

// Container-related exceptions
class ContainerException extends Exception
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

class ContainerCreateException extends ContainerException {}
class ContainerConfigureException extends ContainerException {}
class ContainerStartException extends ContainerException {}
class ContainerStopException extends ContainerException {}
class ContainerSuspendException extends ContainerException {}
class ContainerUnsuspendException extends ContainerException {}
class ContainerSuspendedException extends ContainerException {}
class ContainerTerminatedException extends ContainerException {}
class ContainerDestroyException extends ContainerException {}
class ContainerReinstallException extends ContainerException {}
class ContainerDeployException extends ContainerException {}
class ContainerIpAddException extends ContainerException {}
class ContainerIpRemoveException extends ContainerException {}
class ContainerTrafficRetrieveException extends ContainerException {}

class UnauthorizedException extends Exception {}
class InsufficientAccessLevelException extends Exception {}
class TemplateUnavailableException extends Exception {}

class ParsingException extends Exception {}
