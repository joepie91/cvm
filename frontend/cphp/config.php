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

$cphp_class_map = array(
	'user'		=> "User",
	'node'		=> "Node",
	'container'	=> "Container",
);

$cphp_locale_name = "english";
$cphp_locale_path = "locales";
$cphp_locale_ext  = "lng";

$cphp_usersettings[CPHP_SETTING_TIMEZONE] = "Europe/Amsterdam";

/* These are the memcache settings. You will need to have memcache set
 * up on your server to use these. Compression requires zlib. */
$cphp_memcache_enabled 		= true;			// Whether to user memcache.
$cphp_memcache_server		= "localhost";	// The hostname of the memcached
$cphp_memcache_port		= 11211;		// The port number of memcached
$cphp_memcache_compressed	= true;			// Whether to compress memcache objects

$cphp_mysql_enabled = true;

require("config.mysql.php");

/* Please create a new file in this directory named config.mysql.php
 * that holds the following contents (modified to the correct settings):

$cphp_mysql_host = "localhost";
$cphp_mysql_user = "username";
$cphp_mysql_pass = "password";
$cphp_mysql_db 	= "database";

*/

$cphp_components = array(
	"router",
	"errorhandler"
);
