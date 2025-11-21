<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';
$imported = [];
$skipped = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $row = 0;
        $header = fgetcsv($handle, 1000, ",");
        $header = array_map(function($h) { return strtolower(trim($h)); }, $header);

        $idx_name = array_search('name', $header);
        $idx_email = array_search('email', $header);
        $idx_password = array_search('password', $header);
        $idx_course = array_search('course', $header);
        $idx_phone = array_search('phone', $header);

        if ($idx_name === false || $idx_email === false || $idx_password === false || $idx_course === false || $idx_phone === false) {
            $error = "CSV mesti ada header: name, email, password, course, phone";
        } else {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                $name = isset($data[$idx_name]) ? mysqli_real_escape_string($conn, $data[$idx_name]) : '';
                $email = isset($data[$idx_email]) ? mysqli_real_escape_string($conn, $data[$idx_email]) : '';
                $password = isset($data[$idx_password]) ? password_hash($data[$idx_password], PASSWORD_DEFAULT) : '';
                $course = isset($data[$idx_course]) ? mysqli_real_escape_string($conn, $data[$idx_course]) : '';
                $phone = isset($data[$idx_phone]) ? mysqli_real_escape_string($conn, $data[$idx_phone]) : '';

                if ($name && $email && $password && $course && $phone) {
                    // Semak email sudah wujud
                    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
                    if (mysqli_num_rows($check) > 0) {
                        $skipped[] = "Baris $row: $name ($email) sudah wujud dalam sistem.";
                        continue;
                    }
                    $sql = "INSERT INTO users (name, email, password, role, course, phone) VALUES ('$name', '$email', '$password', 'student', '$course', '$phone')";
                    if (mysqli_query($conn, $sql)) {
                        $imported[] = $name;
                    } else {
                        $skipped[] = "Baris $row: $name ($email) gagal diimport.";
                    }
                } else {
                    $skipped[] = "Baris $row: Data tidak lengkap.";
                }
            }
            fclose($handle);
            $success = "CSV file uploaded.";
        }
    } else {
        $error = "Failed to open the uploaded file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Upload Pelajar (CSV)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
            color: #333;
            font-size: 12px; /* Reduced font size */
        }

        h2 {
            margin-bottom: 10px;
            color: #444;
            font-size: 16px; /* Reduced font size */
        }

        p {
            margin: 5px 0; /* Reduced margin */
        }

        form {
            margin-top: 15px; /* Reduced margin */
        }

        input[type="file"] {
            margin-bottom: 10px; /* Reduced margin */
        }

        button {
            padding: 5px 10px; /* Reduced padding */
            font-size: 12px; /* Reduced font size */
            background: #888;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #666;
        }

        a {
            color: #666;
            text-decoration: none;
            font-weight: normal;
        }

        a:hover {
            text-decoration: underline;
        }

        ul {
            padding-left: 20px; /* Added padding for list */
        }
    </style>
</head>
<body>
    <h2>Upload Pelajar (CSV)</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <?php if (!empty($imported)) { ?>
        <b>Pelajar berjaya diimport:</b>
        <ul>
            <?php foreach ($imported as $nama) echo "<li>".htmlspecialchars($nama)."</li>"; ?>
        </ul>
    <?php } ?>

    <?php if (!empty($skipped)) { ?>
        <b>Baris yang gagal diimport:</b>
        <ul>
            <?php foreach ($skipped as $msg) echo "<li>".htmlspecialchars($msg)."</li>"; ?>
        </ul>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Pilih fail CSV:</label>
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
    <br>
    <a href="admin_manage_student.php">← Kembali ke Senarai Pelajar</a>
    <br><br>
    <b>Format CSV (header):</b><br>
    name,email,password,course,phone
</body>
</html>
