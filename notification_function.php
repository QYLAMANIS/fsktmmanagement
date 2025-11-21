<?php

function sendNotification($conn, $receiver_id, $receiver_role, $message, $link = "")
{
    $status = "baru"; // default status

    $stmt = $conn->prepare("
        INSERT INTO notifikasi (penerima_id, penerima_role, mesej, link, status) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issss", $receiver_id, $receiver_role, $message, $link, $status);
    $stmt->execute();
}
