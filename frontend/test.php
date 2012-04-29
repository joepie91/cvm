<?php
$_CVM = true;
require("includes/include.base.php");

$ssh = new SshConnector();
$ssh->host = "cvm-vz.cryto.net";
$ssh->key = "/etc/cvm/key";
$ssh->pubkey = "/etc/cvm/key.pub";

var_dump($ssh->RunCommand("df -h"));
?>
