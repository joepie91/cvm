<?php
$_CVM = true;
require("includes/include.base.php");

/*$ssh = new SshConnector();
$ssh->host = "cvm-vz.cryto.net";
$ssh->key = "/etc/cvm/key";
$ssh->pubkey = "/etc/cvm/key.pub";

var_dump($ssh->RunCommand("df -h", true));*/

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";

$sNode = new Node(1);
var_dump($sNode->sDiskFree, $sNode->sDiskUsed);

?>
