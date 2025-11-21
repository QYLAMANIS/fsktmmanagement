<?php
session_start();
require_once 'config.php';

// ✅ Pastikan hanya admin boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// ✅ Semak ID pelajar
if (!isset($_GET['id'])) {
    header("Location: admin_manage_student.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM pelajar WHERE id_pelajar = '$id'");
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header("Location: admin_manage_student.php");
    exit();
}

$error = '';

// ✅ Proses kemaskini
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $program = mysqli_real_escape_string($conn, $_POST['course']);
    $psm = mysqli_real_escape_string($conn, $_POST['psm']);

    $update = "
        UPDATE pelajar 
        SET nama_pelajar = '$nama', emel = '$email', program = '$program', psm = '$psm' 
        WHERE id_pelajar = '$id'
    ";

    if (mysqli_query($conn, $update)) {
        header('Location: admin_manage_student.php?update=success');
        exit();
    } else {
        $error = "❌ Gagal kemaskini: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8" />
    <title>Edit Pelajar</title>
    <style>
/* 🌿 Gaya utama */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f8;
    padding: 30px;
    font-size: 13px;
    color: #333;
}

/* Kad */
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
    margin: 20px auto;
    max-width: 600px;
}

/* Tajuk */
h2 {
    text-align: center;
    color: #0d3b66;
    margin-bottom: 25px;
    font-size: 18px;
}

/* Input */
label {
    font-weight: 600;
    display: block;
    margin-top: 12px;
}
input[type="text"],
input[type="email"],
select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
    font-size: 13px;
}

/* Butang */
button {
    margin-top: 20px;
    width: 100%;
    background: #0d3b66;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}
button:hover {
    background: #09325a;
}

/* Ralat */
.error-message {
    color: red;
    text-align: center;
    font-weight: 600;
    margin-bottom: 15px;
}
    </style>
</head>

<body>
<div class="card">
    <h2>✏KEMASKINI MAKLUMAT PELAJAR</h2>

    <?php if ($error): ?>
        <p class="error-message"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>NAMA PELAJAR:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($student['nama_pelajar']) ?>" required>

        <label>EMAIL:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['emel']) ?>" required>

        <label>PROGRAM:</label>
        <input type="text" name="course" value="<?= htmlspecialchars($student['program']) ?>" required>

        <label>PSM:</label>
        <select name="psm" required>
            <option value="PSM1" <?= $student['psm'] == 'PSM1' ? 'selected' : '' ?>>PSM I</option>
            <option value="PSM2" <?= $student['psm'] == 'PSM2' ? 'selected' : '' ?>>PSM II</option>
        </select>

        <button type="submit">SIMPAN PERUBAHAN</button>
    </form
