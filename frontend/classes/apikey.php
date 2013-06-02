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

class ApiKey extends CPHPDatabaseRecordClass
{
	public $table_name = "api_keys";
	public $fill_query = "SELECT * FROM api_keys WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM api_keys WHERE `Id` = :Id";
	
	public $prototype = array(
		'string' => array(
			'PublicToken'		=> "PublicToken",
			'PrivateToken'		=> "PrivateToken",
			'Salt'			=> "Salt"
		),
		'numeric' => array(
			'UserId'		=> "UserId",
			'KeyType'		=> "KeyType"
		),
		'user' => array(
			'User'			=> "UserId"
		)
	);
	
	public function GenerateSalt()
	{
		$this->uSalt = random_string(10);
	}
	
	public function GenerateHash()
	{
		if(!empty($this->uSalt))
		{
			if(!empty($this->uToken))
			{
				$this->uPrivateToken = $this->CreateHash($this->uToken);
			}
			else
			{
				throw new MissingDataException("ApiKey object is missing a token.");
			}
		}
		else
		{
			throw new MissingDataException("ApiKey object is missing a salt.");
		}
	}
	
	public function CreateHash($input)
	{
		global $settings;
		$hash = crypt($input, "$5\$rounds=50000\${$this->uSalt}{$settings['salt']}$");
		$parts = explode("$", $hash);
		return $parts[4];
	}
	
	public function VerifyToken($token)
	{
		if($this->CreateHash($token) == $this->sPrivateToken)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function SetPrivateToken($token)
	{
		$this->uToken = $token;
		$this->GenerateHash();
	}
}
