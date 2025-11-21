<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';

// Tambah pelajar secara manual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['name']);
    $no_matrik = mysqli_real_escape_string($conn, $_POST['matric']);
    $program = mysqli_real_escape_string($conn, $_POST['course']);
    $emel = mysqli_real_escape_string($conn, $_POST['email']);

    $default_password = 'uthm';
    $kata_laluan = password_hash($default_password, PASSWORD_DEFAULT);

$psm = isset($_POST['psm']) ? mysqli_real_escape_string($conn, $_POST['psm']) : 'PSM1';


$sql = "INSERT INTO pelajar (nama_pelajar, no_matrik, program, emel, kata_laluan, psm)
        VALUES ('$nama', '$no_matrik', '$program', '$emel', '$kata_laluan', '$psm')";


    if (mysqli_query($conn, $sql)) {
        header('Location: admin_manage_student.php');
        exit();
    } else {
        $error = "Gagal tambah pelajar: " . mysqli_error($conn);
    }
}

// Proses Upload CSV
if (isset($_POST['upload_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file_tmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file_tmp, 'r');

        if ($handle !== FALSE) {
            fgetcsv($handle); // Skip header
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) < 4) continue;

                $nama = mysqli_real_escape_string($conn, $data[0]);
                $no_matrik = mysqli_real_escape_string($conn, $data[1]);
                $program = mysqli_real_escape_string($conn, $data[2]);
                $emel = mysqli_real_escape_string($conn, $data[3]);

                $default_password = 'uthm';
                $kata_laluan = password_hash($default_password, PASSWORD_DEFAULT);

             $psm = isset($_GET['psm']) ? mysqli_real_escape_string($conn, $_GET['psm']) : 'PSM1';

$sql = "INSERT INTO pelajar (nama_pelajar, no_matrik, program, emel, kata_laluan, psm)
        VALUES ('$nama', '$no_matrik', '$program', '$emel', '$kata_laluan', '$psm')";

                mysqli_query($conn, $sql);
            }
            fclose($handle);
            header('Location: admin_manage_student.php');
            exit();
        } else {
            $error = "Gagal membaca fail CSV.";
        }
    } else {
        $error = "Sila pilih fail CSV yang sah.";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

      <?php include 'favicon.php'; ?>
<meta charset="UTF-8">
<title>Tambah Pelajar</title>
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

select {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
    font-size: 13px;
    background-color: white;
}
select:focus {
    outline: none;
    border-color: #0d3b66;
    box-shadow: 0 0 3px rgba(13, 59, 102, 0.3);
}
.psm-menu {
    text-align: center;
    margin-bottom: 25px;
}

.psm-menu a {
    display: inline-block;
    padding: 10px 20px;
    margin: 0 5px;
    background: #e0e7ff;
    color: #0d3b66;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 13px;
    transition: 0.3s;
}

.psm-menu a:hover {
    background: #0d3b66;
    color: white;
}

.psm-menu a.active {
    background: #0d3b66;
    color: white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

</style>
</head>


<body>
<div class="card">
    <h2>TAMBAH PELAJAR (MANUAL)</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <label>NAMA:</label>
        <input type="text" name="name" required>

        <label>NO MATRIK:</label>
        <input type="text" name="matric" required>

        <label>PROGRAM:</label>
        <select name="course" required>
            <option value="">-- PILIH PROGRAM --</option>
            <option value="BIT">BIT</option>
            <option value="BIS">BIS</option>
            <option value="BIM">BIM</option>
            <option value="BIP">BIP</option>
            <option value="BIW">BIW</option>
        </select>
<label>PSM:</label>
<select name="psm" required>
    <option value="">-- PILIH PSM --</option>
    <option value="PSM1" <?= (isset($_GET['psm']) && $_GET['psm'] == 'PSM1') ? 'selected' : '' ?>>PSM I</option>
    <option value="PSM2" <?= (isset($_GET['psm']) && $_GET['psm'] == 'PSM2') ? 'selected' : '' ?>>PSM II</option>
</select>


        <label>EMAIL:</label>
        <input type="email" name="email" required>

        <p class="note">* Kata laluan pelajar ditetapkan secara automatik kepada <strong>uthm</strong></p>

        <button type="submit">TAMBAH PELAJAR</button>
    </form>
</div>


    <div class="card">
        <h2>UPLOAD SENARAI PELAJAR (CSV)</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label>UPLOAD FAIL (CSV)</label>
            <input type="file" name="csv_file" accept=".csv" required>

            <p class="note">* Susunan CSV: Nama, No Matriks, Program, Email</p>

            <button type="submit" name="upload_csv">UPLOAD CSV</button>
        </form>
    </div>
</body>
</html>
