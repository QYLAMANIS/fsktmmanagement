<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelajar = $_POST['user_id'];
    $id_penyelia = $_POST['id_penyelia'];
    $tarikh = $_POST['tarikh'];
    $masa = $_POST['masa'];
    $tujuan = $_POST['tujuan'];

    $stmt = $conn->prepare("INSERT INTO temujanji (id_pelajar, id_penyelia, tarikh, masa, tujuan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id_pelajar, $id_penyelia, $tarikh, $masa, $tujuan);

    if ($stmt->execute()) {
        // 🔹 Guna header redirect terus ke senarai temujanji
        header("Location: student_senarai_temujanji.php?success=1");
        exit();
    } else {
        echo "<script>alert('Ralat: Gagal menghantar permohonan.'); window.history.back();</script>";
    }
}
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">