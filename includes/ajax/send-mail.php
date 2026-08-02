<?php
include('../dbconfigure.php');

$tableName    = "web_users";
$sendemail  = isset($_POST['email']) ? $_POST['email'] : '';

//echo $sendemail ;
$rand       = rand(1000, 9999);
$msg        = "Hi $sendemail, We are sending this msg to Confirm otp from you...\n 
OTP is $rand";

if ($sendemail != '') {
    $qry      = "SELECT id FROM $tableName WHERE `email` = '$sendemail'";
    $suc   = mysqli_query($Dbconnect, $qry);
    if (mysqli_num_rows($suc) > 0) {
        $sql      = "UPDATE $tableName SET `otp` = '$rand' WHERE `email` = '$sendemail'";
        $success   = mysqli_query($Dbconnect, $sql);
    } else {
        $sql      = "INSERT INTO $tableName SET `otp` = '$rand', `email` = '$sendemail'";
        $success   = mysqli_query($Dbconnect, $sql);
        $id = mysqli_insert_id($Dbconnect);
    }
    if ($success) {
    } else {
        mysqli_error($Dbconnect);
    }
}

//echo $sendemail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../assets/vendor/autoload.php';

echo smtp_mailer($sendemail, 'Confirm OTP', $msg);
function smtp_mailer($to, $subject, $msg)
{
    $email = new PHPMailer();

    //$email-> From  = "dipakbhardwaj358@gmail.com";
    $email->IsSMTP();
    $email->Host = "smtp.gmail.com";
    $email->SMTPAuth = TRUE;
    $email->Username  = "dipakbhardwaj358@gmail.com";
    $email->Password  = "ufrn ofrv tsxn ymyh";
    $email->SMTPSecure = "tls";
    $email->Port = "587";
    $email->IsHTML(TRUE);
    $email->CharSet = "UTF-8";

    //$email-> FromName  = "Dipak Hemprakash Bhardwaj";
    $email->addReplyTo("dipakbhardwaj358@gmail.com", "Reply Address");
    $email->Subject  = $subject;
    $email->Body  = $msg;
    $email->addAddress($to);
    $email->SetFrom("dipakbhardwaj358@gmail.com");

    $email->SMTPOptions = array("ssl" => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));


    if (!$email->send()) {
        echo $email->ErrorInfo;
    } else {
        echo "sent";
    }
}
