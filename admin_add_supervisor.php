<?php
session_start();
require_once 'config.php';

// Semak jika admin log masuk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

$error = '';

// Tambah penyelia secara manual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $nama = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $kuota = isset($_POST['kuota']) ? (int)$_POST['kuota'] : 5;  // Default kuota is 5

    $default_password = 'uthm';
    $kata_laluan = password_hash($default_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO penyelia (nama_penyelia, email, course, kata_laluan, kuota) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nama, $email, $course, $kata_laluan, $kuota);

    if ($stmt->execute()) {
        header('Location: admin_manage_supervisors.php');
        exit();
    } else {
        $error = "Gagal tambah penyelia: " . $conn->error;
    }

    $stmt->close();
}

// Proses Upload CSV
if (isset($_POST['upload_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file_tmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file_tmp, 'r');

        if ($handle !== FALSE) {
            // Abaikan header
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) < 4) continue; // Skip jika data tidak lengkap

                $nama = mysqli_real_escape_string($conn, $data[0]);
                $email = mysqli_real_escape_string($conn, $data[1]);
                $course = mysqli_real_escape_string($conn, $data[2]);
                $kuota = (int)$data[3];  // CSV format: Name, Email, Course, Kuota

                $default_password = 'uthm';
                $kata_laluan = password_hash($default_password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO penyelia (nama_penyelia, email, course, kata_laluan, kuota)
                        VALUES ('$nama', '$email', '$course', '$kata_laluan', $kuota)";

                mysqli_query($conn, $sql);
            }

            fclose($handle);
            header('Location: admin_manage_supervisors.php');
            exit();
        } else {
            $error = "Gagal membaca fail CSV.";
        }
    } else {
        $error = "Sila pilih fail CSV yang sah.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Tambah Penyelia</title>
   <style>
/* 🌿 Gaya utama */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f8;
    padding: 30px;
    font-size: 12px;
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
    margin-bottom: 20px;
}

/* Input */
label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}
input[type="text"],
input[type="email"],
input[type="file"] {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
    font-size: 13px;
}

/* Butang */
button {
    margin-top: 15px;
    width: 100%;
    background: #0d3b66;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px;
    font-size: 14px;
    cursor: pointer;
}
button:hover {
    background: #09325a;
}

/* Nota dan ralat */
.note {
    font-size: 11px;
    margin-top: 5px;
    color: #555;
}
.error {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}
</style>
</head>
<body><body>
    <div class="card">
    <h2>TAMBAH PENYELIA BAHARU (MANUAL) </h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <!-- Form Tambah Penyelia Manual -->
    <form method="POST" action="admin_add_supervisor.php">
        <label>NAMA PENYELIA:</label>
        <input type="text" name="name" required>

        <label>EMAIL:</label>
        <input type="email" name="email" required>

        <label>Kursus:</label>
        <input type="text" name="course" required>

        <label>KUOTA PENYELIA:</label>
        <input type="number" name="kuota" min="1" value="5" required>

        <p class="note">* Kata laluan penyelia ditetapkan secara automatik kepada <strong>uthm</strong></p>

        <button type="submit">TAMBAH PENYELIA</button>
    </form>

    <h2>UPLOAD SENARAI PENYELIA (CSV)</h2>

    <!-- Form Upload CSV -->
    <form method="POST" action="admin_add_supervisor.php" enctype="multipart/form-data">
        <label>UPLOAD FAIL CSV :</label>
        <input type="file" name="csv_file" accept=".csv" required>

        <p class="note">* Susunan CSV: Nama Penyelia, Email, Kursus, Kuota</p>

        <button type="submit" name="upload_csv">UPLOAD CSV</button>
    </form>
</body>
</html>

</div
