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

$_CPHP = true;
require("cphp/base.php");
require("include.exceptions.php");
require("include.constants.php");
require("include.parsing.php");
require("classes/class.user.php");
require("classes/class.controller.php");
require("classes/class.container.php");
require("classes/class.node.php");
require("classes/class.template.php");
require("classes/class.sshconnector.php");
?>
