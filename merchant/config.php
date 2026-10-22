<?php
error_reporting(E_ALL);
ini_set("display_errors",true);
//error_reporting(0);
date_default_timezone_set("Asia/Kolkata");

// Define the base directory constant
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', realpath(dirname(__FILE__) . '/../') . '/');
}


// Securely include files using the ROOT_DIR constant
include_once ROOT_DIR . 'pages/dbInfo.php';
include_once ROOT_DIR . 'pages/dbFunctions.php';

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);



$server = $_SERVER["HTTP_HOST"];
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$site_url = $protocol . $server;

// Fetch website settings globally
$website_settings = getWebsiteSettings();
?>