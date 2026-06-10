<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'luxor');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', 'http://localhost/luxor-system/');
date_default_timezone_set('America/Bogota');
?>