<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $id_penyelia = $_POST['id_penyelia'];
    $tarikh = $_POST['tarikh'];
    $masa = $_POST['masa'];
    $tujuan = $_POST['tujuan'];

    $nama = $_SESSION['name'];
    $email = $_SESSION['email'];

    // Semak semua input diisi
    if (empty($id_penyelia) || empty($tarikh) || empty($masa) || empty($tujuan)) {
        echo "<script>alert('Sila lengkapkan semua maklumat.'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO temujanji (user_id, id_penyelia, nama, email, tarikh, masa, tujuan, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Dalam Proses', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss", $user_id, $id_penyelia, $nama, $email, $tarikh, $masa, $tujuan);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Permohonan temujanji telah dihantar kepada penyelia.";
        header("Location: senarai_temujanji.php");
        exit();
    } else {
        echo "Ralat: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">