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

define("CVM_VIRTUALIZATION_OPENVZ",			1	);

define("CVM_STATUS_BLANK",				1	);
define("CVM_STATUS_CREATED",				2	);
define("CVM_STATUS_CONFIGURED",				3	);
define("CVM_STATUS_STARTED",				4	);
define("CVM_STATUS_STOPPED",				5	);
define("CVM_STATUS_SUSPENDED",				6	);
define("CVM_STATUS_TERMINATED",				7	);

define("API_CLIENT",					1	);
define("API_BILLING",					2	);
define("API_ADMIN",					3	);

define("REGEX_HOSTNAME",	"/(([a-zA-Z0-9-]+\.)+)([a-zA-Z0-9-]+)/");
?>
