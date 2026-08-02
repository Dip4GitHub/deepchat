<?php
$host             = 'localhost';
$username         = 'root';
$password         = '';
$database         = 'projects_websocket';
date_default_timezone_set('Asia/Kolkata');
define('CHAT_ENCRYPTION_KEY', 'deepchat-secret-key-2026');

$smtp_Host         = 'gains.arrowdnscloud.com';
$smtp_Port         = '465'; // or 587
$smtp_Username     = 'noreply@msebeccs.com';
$smtp_Password     = 'noreply@2019mission';
//C:\xampp\htdocs\phpmailer
$siteUrl        = 'http://127.0.0.1/websocket-ratchet/';

$siteName        = 'websocket';
$adminEmail        = 'dipakbhardwaj358@gmail.com';
$siteEmail        = 'dipakbhardwaj358@gmail.com';
$siteMobile        = '7298515125';
$limitperpage     = 30;
$glb_msg        = '';
$currentPage      = '';


$Dbconnect = new mysqli($host, $username, $password, $database);
mysqli_set_charset($Dbconnect, 'utf8');
// Check connection
if ($Dbconnect->connect_error) {
    die("Connection failed: " . $Dbconnect->connect_error);
}