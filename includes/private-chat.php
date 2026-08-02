<?php

session_start();

include('dbconfigure.php');

$user1 = $_SESSION['user_id'];
$user2 = $_GET['user_id'];

$sql = "SELECT m.*, u.email AS sender_name FROM web_message m LEFT JOIN web_users u ON u.id = m.sender_id WHERE (sender_id='$user1' AND receiver_id='$user2') OR (sender_id='$user2' AND receiver_id='$user1') ORDER BY id ASC";

$result = mysqli_query($Dbconnect, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
