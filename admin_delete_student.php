<?php
session_start();
require_once 'config.php';

// Only admin allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if id is given
if (!isset($_GET['id'])) {
    header("Location: admin_manage_student.php");
    exit();
}

$student_id = intval($_GET['id']);

// Check if student exists
$stmt = $conn->prepare("SELECT * FROM pelajar WHERE id_pelajar = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Pelajar tidak dijumpai.";
    exit();
}

// Delete student
$delete = $conn->prepare("DELETE FROM pelajar WHERE id_pelajar = ?");
$delete->bind_param("i", $student_id);
$delete->execute();

// Redirect to student list
header("Location: admin_manage_student.php");
exit();
?>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
