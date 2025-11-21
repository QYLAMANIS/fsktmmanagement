<?php
session_start();
include 'config.php'; // Sambungan ke DB

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo "Pengguna belum log masuk.";
    exit();
}

$error = "";
$success = "";
$name = "";
$matric_no = "";
$email = "";
$course = "";
$phone = "";

// Dapatkan data pelajar
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $conn->prepare("SELECT nama_pelajar, no_matrik, emel, program, telefon FROM pelajar WHERE id_pelajar = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $matric_no, $email, $course, $phone);
    $stmt->fetch();
    $stmt->close();
}

// Proses kemaskini profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $matric_no = trim($_POST['matric_no']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $phone = trim($_POST['phone']);

    if (empty($name) || empty($matric_no) || empty($email)) {
        $error = "Nama, ID Pelajar dan Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak sah.";
    } else {
        $profile_image_name = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $tmpName = $_FILES['profile_image']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];

            if (!in_array($ext, $allowed)) {
                $error = "Format gambar tidak sah. Sila muat naik JPG, PNG atau GIF.";
            } else {
                $profile_image_name = 'profile_' . $user_id . '_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $profile_image_name;

                if (!move_uploaded_file($tmpName, $targetFile)) {
                    $error = "Gagal memuat naik gambar.";
                }
            }
        }

        if (!$error) {
            if ($profile_image_name) {
                $stmt = $conn->prepare("UPDATE pelajar SET nama_pelajar=?, no_matrik=?, emel=?, program=?, telefon=?, profile_image=? WHERE id_pelajar=?");
                $stmt->bind_param("ssssssi", $name, $matric_no, $email, $course, $phone, $profile_image_name, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE pelajar SET nama_pelajar=?, no_matrik=?, emel=?, program=?, telefon=? WHERE id_pelajar=?");
                $stmt->bind_param("sssssi", $name, $matric_no, $email, $course, $phone, $user_id);
            }

     if ($stmt->execute()) {
    header("Location: student_profile.php?updated=1");
    exit();
}
 else {
                $error = "Ralat semasa mengemaskini: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta charset="UTF-8">
    <title>Edit Profil Pelajar</title>
    <style>
        body {
            font-family: "Poppins", Arial, sans-serif;
            background: linear-gradient(135deg, #dfe9f3, #ffffff);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 480px;
            margin: 60px auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            padding: 35px 40px;
            animation: fadeIn 0.7s ease;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 30px;
        }
        label {
            font-weight: 500;
            color: #34495e;
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,0.2);
        }
        input[type="submit"] {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }
        input[type="submit"]:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .error { color: #e74c3c; }
        .success { color: #27ae60; }
        .profile-preview {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Kemaskini Profil Pelajar</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="profile-preview">
            <?php
            $stmt = $conn->prepare("SELECT profile_image FROM pelajar WHERE id_pelajar = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($profile_image);
            $stmt->fetch();
            $stmt->close();

            if (!empty($profile_image) && file_exists("uploads/$profile_image")) {
                echo '<img src="uploads/' . htmlspecialchars($profile_image) . '" alt="Gambar Profil">';
            } else {
                echo '<img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Default Profile">';
            }
            ?>
        </div>

        <label for="name">Nama</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="matric_no">ID Pelajar</label>
        <input type="text" id="matric_no" name="matric_no" value="<?= htmlspecialchars($matric_no) ?>" required>

        <label for="email">Emel</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="course">Kursus</label>
        <input type="text" id="course" name="course" value="<?= htmlspecialchars($course) ?>">

        <label for="phone">No. Telefon</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">

        <label for="profile_image">Gambar Profil (pilihan)</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/*">

        <input type="submit" value="Simpan Perubahan">
    </form>
</div>

</body>
</html>
