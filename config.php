<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
$servername = "localhost";
$username = "unkuodtm3putf";
$password = "htk2glkxl4n4";
$dbname = "dbze8vhj5lbc2z";
 
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
session_start();
?>
