<?php
$browser=false;

define('DB_SERVER', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');
define('BASE_URL', '');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
  die("ERROR: Could not connect. " . $mysqli->connect_error);
}
