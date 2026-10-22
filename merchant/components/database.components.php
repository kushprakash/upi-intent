<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
error_reporting(0);
date_default_timezone_set("Asia/Kolkata");   //India time (GMT+5:30)

function db(){
global $dbhost,$dbname,$dbuser,$dbpass;
try {   
include_once '../../pages/dbInfo.php';

    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->exec('SET NAMES "utf8"');
return $conn;
} catch (PDOException $e ) {
//require_once("error/503.php");  
exit($e->getMessage());
}
}