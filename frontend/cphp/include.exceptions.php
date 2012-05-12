<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if($_CPHP !== true) { die(); }

class OwnershipException extends Exception {}
class UserAccessException extends Exception {}
class NotFoundException extends Exception {}
class PrototypeException extends Exception {}
class ConstructorException extends Exception {}
class MissingDataException extends Exception {}
class DatabaseException extends Exception {}
class TypeException extends Exception {}

class TemplateException extends Exception
{
	public $message = "";
	public $file = "";
	public $startpos = 0;
	public $endpos = 0;
	public $code = 0;
	
	public function __construct($message, $file, $startpos, $endpos, $code)
	{
		$this->message = $message;
		$this->file = $file;
		$this->startpos = $startpos;
		$this->endpos = $endpos;
	}
}

class TemplateSyntaxException extends TemplateException {}
class TemplateParsingException extends TemplateException {}
