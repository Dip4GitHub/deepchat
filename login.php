<?php
session_start();
include('includes/dbconfigure.php');
$email        = isset($_POST['email']) ? $_POST['email'] : '';
$otp          = isset($_POST['otp']) ? $_POST['otp'] : '';
$tableName    = "web_users";
if ($email != '' && $otp != '') {
  $sql = "SELECT `id`, `otp` FROM $tableName WHERE `email` = ? LIMIT 1";
  $stmt = mysqli_prepare($Dbconnect, $sql);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId, $otpReal);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
  }

  if (!empty($otpReal) && $otp == $otpReal) {
    $_SESSION['name'] = $email;
    $_SESSION['user_id'] = (int)$userId;
    $g_user_id = $_SESSION['user_id'];
    header("location: index.php");
    exit;
  }

  header("location: index.php?msg=Please Enter Valid OTP");
}
