<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_manage_supervisors.php');
    exit();
}

$id = $_GET['id']; // assuming id_penyelia is a string (like "SP001")

// Delete from penyelia
$stmt1 = $conn->prepare("DELETE FROM penyelia WHERE id_penyelia = ?");
$stmt1->bind_param("s", $id);
$ok1 = $stmt1->execute();
$stmt1->close();

if ($ok1) {
    // Delete from users
    $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'supervisor'");
    $stmt2->bind_param("s", $id);
    $ok2 = $stmt2->execute();
    $stmt2->close();

    if ($ok2) {
        $_SESSION['success'] = "Penyelia berjaya dipadam.";
    } else {
        $_SESSION['error'] = "Ralat padam dari users: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "Ralat padam dari penyelia: " . $conn->error;
}

header('Location: admin_manage_supervisors.php');
exit();
