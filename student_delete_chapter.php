<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar'){
    header("Location: student_login.php");
    exit();
}

$id_pelajar = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

// Pastikan chapter milik pelajar sendiri
$stmt = $conn->prepare("SELECT chapter_file, file_corrected FROM chapter WHERE id=? AND id_pelajar=?");
$stmt->bind_param("ii", $id, $id_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if($data){
    // Padam fail jika ada
    if(file_exists($data['chapter_file'])) unlink($data['chapter_file']);
    if($data['file_corrected'] && file_exists($data['file_corrected'])) unlink($data['file_corrected']);

    // Padam rekod dari DB
    $del = $conn->prepare("DELETE FROM chapter WHERE id=? AND id_pelajar=?");
    $del->bind_param("ii", $id, $id_pelajar);
    $del->execute();
    $del->close();
}

header("Location: student_upload_report.php?deleted=1");

exit();
?>
