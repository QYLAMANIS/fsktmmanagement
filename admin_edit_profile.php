<?php
session_start();
require_once 'config.php';

// ✅ Semak login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_admin.php');
    exit();
}

$username = $_SESSION['user_id'];

// ✅ Dapatkan maklumat admin
$query = "SELECT * FROM admin WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    echo "Profil tidak dijumpai.";
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $new_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $admin['password'];

    // ✅ Upload gambar profil (jika ada)
    $profile_image = $admin['profile_image'] ?? '';
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/admin_images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $file_name;
            } else {
                $error = "Ralat memuat naik gambar.";
            }
        } else {
            $error = "Hanya fail JPG, JPEG, PNG, atau GIF dibenarkan.";
        }
    }

    // ✅ Kemas kini data admin
    if (empty($error)) {
        $update = $conn->prepare("UPDATE admin SET email = ?, password = ?, profile_image = ? WHERE username = ?");
        $update->bind_param("ssss", $email, $new_password, $profile_image, $username);

        if ($update->execute()) {
            $success = "Profil berjaya dikemaskini!";
            // Refresh data
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Ralat semasa mengemaskini profil.";
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
<title>Kemaskini Profil Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f7fb;
    margin: 0;
    padding: 40px 20px;
    color: #1e293b;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.form-card {
    max-width: 700px;
    width: 100%;
    background: #fff;
    border-radius: 15px;
    padding: 35px 25px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

h2 {
    text-align: center;
    color: #002b5c;
    font-weight: 700;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

label {
    font-weight: 600;
    color: #334155;
}

input[type="email"],
input[type="password"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    margin-top: 5px;
}

.btn-save {
    background: #002b5c;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #0a3470;
    transform: translateY(-1px);
}

.alert {
    text-align: center;
    font-size: 14px;
    padding: 10px;
    border-radius: 6px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.alert-danger {
    background: #fee2e2;
    color: #b91c1c;
}

.profile-image-preview {
    display: block;
    margin: 0 auto 15px auto;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
}
</style>
</head>
<body>

<div class="form-card">
    <h2>Kemaskini Profil Admin</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="text-center">
            <?php if (!empty($admin['profile_image'])): ?>
                <img src="uploads/admin_images/<?= htmlspecialchars($admin['profile_image']) ?>" class="profile-image-preview">
            <?php else: ?>
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="profile-image-preview">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Alamat Emel</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Kata Laluan Baharu (pilihan)</label>
            <input type="password" name="password" id="password" placeholder="Biarkan kosong jika tidak ingin menukar">
        </div>

        <div class="form-group">
            <label for="profile_image">Gambar Profil (pilihan)</label>
            <input type="file" name="profile_image" id="profile_image" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <div class="text-center">
            <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>
