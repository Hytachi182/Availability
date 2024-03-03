<?php
// START SESSION
session_name('AvailabilityWeb');
session_start();

//include  functions
require  ($_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/global.functions.php');

//GLOBAL VAR
define ("ROOT_URL",  "https://" . $_SERVER['HTTP_HOST']);
define ("SUBFOLDER", "AvailabilityWeb");
define ("APP_URL",   ROOT_URL . "/" . SUBFOLDER);
define ("APPLICATION_NAME", "AvailabilityWeb");
define ("SCHEMA", "AvailabilityWeb");
define ("PROXY", "1.1.1.1:1111");
define ("PWD_PROXY", "passwordProxy");
//Set timezone
date_default_timezone_set('Europe/Brussels');

//DATASOURCE
require  ($_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/datasource.private');

$MYSQLI_connection = MYSQLI___get_connector(SCHEMA);
// INIT MYSQLI CONNECTOR ONCE, AT THE BEGINNING OF THE SCRIPT, WILL BE USED IN ALL REQUEST
if (is_null($MYSQLI_connection)) exit(99);

    

// FILTERING VALUES FROM USER, PREVENTING XSS INJECTIONS
if(!empty($_POST)) {
    function filter_xss(&$value) {
        if(is_array($value)) {
            array_walk_recursive($value, "filter_xss");
        } else {
            $value = htmlspecialchars(strip_tags($value));
        }
    }
    array_walk_recursive($_POST, "filter_xss");
}

// Options: development,  production
define("ENVIRONMENT", "development"); 

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
    ini_set("log_errors", 1);
    ini_set("error_log", "./logs/AvailabilityWeb".date('Y-m-d') . ".log");
}
