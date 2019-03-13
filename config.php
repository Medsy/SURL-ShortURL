<?php
$browser=false;

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'surl');
define('BASE_URL', 'http://localhost/WebURLShortener/');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
  die("ERROR: Could not connect. " . $mysqli->connect_error);
}
