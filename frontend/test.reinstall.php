<?php
$_CVM = true;
require("includes/include.base.php");

$sContainer = new Container(1);
pretty_dump($sContainer->sTemplate->sName);

$sContainer->uTemplateId = 4;
$sContainer->InsertIntoDatabase();
pretty_dump($sContainer->sTemplate->sName);
