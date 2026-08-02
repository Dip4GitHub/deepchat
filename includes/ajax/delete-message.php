<?php
session_start();
include('../dbconfigure.php');

$tableName    = "web_message";
$id           = isset($_POST['id']) ? $_POST['id'] : 0;
$user_id      = $_SESSION['user_id'];

if ($id > 0) {
    echo $qry   = "DELETE FROM $tableName WHERE `id` = '$id' AND `sender_id` = '$user_id'";
    $suc   = mysqli_query($Dbconnect, $qry);
    if ($suc) {
        echo "success";
    } else {
        echo "failed";
    }
}