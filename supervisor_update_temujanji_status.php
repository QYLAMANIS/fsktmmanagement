<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id = intval($_POST['id']);
$status = $_POST['status'];
$komen = trim($_POST['komen'] ?? '');

if (!$id || !$status || !$komen) {
    echo "<script>alert('Sila isi semua maklumat sebelum hantar!'); window.history.back();</script>";
    exit();
}

$stmt = $conn->prepare("UPDATE temujanji SET status=?, komen=? WHERE id=?");
$stmt->bind_param("ssi", $status, $komen, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Temujanji berjaya dikemaskini!'); window.location='supervisor_temujanji.php';</script>";
} else {
    echo "<script>alert('Tiada perubahan dibuat.'); window.location='supervisor_temujanji.php';</script>";
}
$stmt->close();
$conn->close();
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">