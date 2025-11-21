<?php
session_start();
require_once "config.php";

// Pastikan penyelia login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];
$message = "";

// Bila form dihantar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_pass = $_POST["current_password"];
    $new_pass = $_POST["new_password"];
    $confirm_pass = $_POST["confirm_password"];

    // 1. Ambil password lama dari DB (kolum sebenar = kata_laluan)
    $stmt = $conn->prepare("SELECT kata_laluan FROM penyelia WHERE id_penyelia = ?");
    $stmt->bind_param("i", $id_penyelia);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($db_password);
    $stmt->fetch();

    // Jika tiada result
    if ($stmt->num_rows === 0) {
        $message = "<p style='color:red;'>Ralat: Rekod penyelia tidak dijumpai.</p>";
    }
    else if (!password_verify($current_pass, $db_password)) {
        $message = "<p style='color:red;'>Kata laluan lama tidak betul.</p>";
    } 
    else if ($new_pass !== $confirm_pass) {
        $message = "<p style='color:red;'>Kata laluan baru tidak sama.</p>";
    } 
    else {

        // 3. Hash password baru
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

        // 4. Update (guna kolum betul: kata_laluan)
        $update = $conn->prepare("UPDATE penyelia SET kata_laluan = ? WHERE id_penyelia = ?");
        $update->bind_param("si", $hashed_password, $id_penyelia);

        if ($update->execute()) {
            $message = "<p style='color:green;'>Kata laluan berjaya dikemaskini.</p>";
        } else {
            $message = "<p style='color:red;'>Ralat semasa mengemaskini kata laluan.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tukar Kata Laluan</title>
</head>
<body>

<h2>Tukar Kata Laluan</h2>

<?= $message ?>

<form method="POST">
    <label>Kata Laluan Semasa</label><br>
    <input type="password" name="current_password" required><br><br>

    <label>Kata Laluan Baru</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>Sahkan Kata Laluan Baru</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Kemaskini Kata Laluan</button>
</form>

</body>
</html>
