<?php

// Call WordPress DB Interface for use as API

require_once ROOT_LOC . "/outdoors/wp-includes/version.php";
require_once ROOT_LOC . "/includes/wp-join.php";

$abspath=ROOT_LOC . "/outdoors/";
define("ABSPATH",$abspath);
define("WP_CONTENT_DIR",$abspath);
define("WPINC","wp-includes");
define("WP_DEBUG",FALSE);

require_once ROOT_LOC . '/outdoors/wp-load.php';
//require_once ROOT_LOC . "/outdoors/wp-includes/load.php";
//require_once ROOT_LOC . "/outdoors/wp-includes/plugin.php";
//require_once ROOT_LOC . "/outdoors/wp-includes/wp-db.php";

require_wp_db();

// Call Meekro DB Interface for use as API

require_once ROOT_LOC . "/meekro/db.class.php";
DB::$user = DB_USER;
DB::$password = DB_PASSWORD;

?>
