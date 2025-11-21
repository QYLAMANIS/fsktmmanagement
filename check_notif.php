<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["count" => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM notifikasi 
    WHERE penerima_id=? 
      AND penerima_role=? 
      AND status='baru'
");
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

echo json_encode(["count" => $res['total']]);
