<?php
$_CVM = true;
require("includes/include.base.php");

$settings['master_privkey'] = "/etc/cvm/key";
$settings['master_pubkey'] = "/etc/cvm/key.pub";

$sContainer = new Container(1);

$sContainer->Start();

echo("Done!");
?>
