<?php
session_start();
require_once 'config.php';

// Pastikan pelajar login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header("Location: student_login.php");
    exit();
}

$id_pelajar = $_SESSION['user_id'];
$success = $error = "";

// Bila submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Kata laluan baru tidak sepadan.";
    } else {

        $stmt = $conn->prepare("SELECT kata_laluan FROM pelajar WHERE id_pelajar = ?");
        $stmt->bind_param("i", $id_pelajar);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            $error = "Ralat: Pelajar tidak dijumpai.";
        } else {

            $hashed_password = $row['kata_laluan'];

            if (!password_verify($current_password, $hashed_password)) {
                $error = "Kata laluan lama tidak betul.";
            } else {

                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                $update = $conn->prepare("UPDATE pelajar SET kata_laluan = ? WHERE id_pelajar = ?");
                $update->bind_param("si", $new_hashed, $id_pelajar);

                if ($update->execute()) {
                    $success = "Kata laluan berjaya ditukar!";
                } else {
                    $error = "Ralat semasa mengemaskini kata laluan.";
                }
            }
        }
    }
}

// ✅ Bina mesej alert
$message = "";

if (!empty($error)) {
    $message = "<div class='alert alert-danger'>$error</div>";
}

if (!empty($success)) {
    $message = "<div class='alert alert-success'>$success</div>";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Tukar Katalaluan</title>
<style>
body {font-family: Arial; background:#fff; padding:20px;}

.form-container {
    max-width: 450px;
    margin: auto;
    background: #f8f9fc;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

h2 {color: #0d3b66; text-align:center; margin-bottom:20px;}

input[type=password] {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 5px;
    border: 1px solid #bbb;
}

button {
    width: 100%;
    padding: 12px;
    background: #0d3b66;
    border: none;
    color: #fff;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #154c8f;
}

.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    text-align: center;
}
.alert-danger {background: #ffdddd; color:#a30000; border:1px solid #ffb3b3;}
.alert-success {background: #ddffdd; color:#006600; border:1px solid #b3ffb3;}

</style>
</head>
<body>

<div class="form-container">

    <h2>TUKAR KATA LALUAN</h2>

    <?= $message ?>

    <form method="POST">
        <label>KATA LALUAN SEMASA</label>
        <input type="password" name="current_password" required>

        <label>KATA LALUAN BARU</label>
        <input type="password" name="new_password" required>

        <label>SAHKAN KATA LALUAN BARU</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">KEMASKINI KATA LALUAN</button>
    </form>

</div>

</body>
</html>
