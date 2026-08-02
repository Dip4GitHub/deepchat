<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../includes/dbconfigure.php';

$limit = 100;

$result = mysqli_query(
    $Dbconnect,
    "SELECT
        m.id,
        m.sender_id,
        m.receiver_id,
        m.message_type,
        m.message,
        m.file_name,
        m.is_seen,
        m.seen_at,
        m.created_date,
        u.email AS sender_name
     FROM web_message m
     LEFT JOIN web_users u
     ON u.id = m.sender_id
     ORDER BY m.id DESC
     LIMIT $limit"
);

$messages = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

echo json_encode(
    array_reverse($messages),
    JSON_UNESCAPED_UNICODE
);
