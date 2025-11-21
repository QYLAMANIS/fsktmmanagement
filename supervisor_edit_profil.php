<?php
session_start();
require_once 'config.php';

// Pastikan penyelia logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penyelia') {
    header('Location: login.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);
$error = '';
$success = '';

// Dapatkan data penyelia semasa
$query = "SELECT * FROM penyelia WHERE id_penyelia = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Profil tidak dijumpai.";
    exit();
}

$supervisor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dapatkan input dari form
    $full_name = trim($_POST['full_name'] ?? '');
    $academic_position = trim($_POST['academic_position'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $cv_link = trim($_POST['cv_link'] ?? '');
    $max_quota = intval($_POST['max_quota']);

    // Validasi pautan CV (boleh kosong, tapi kalau isi mesti URL valid)
    if (!empty($cv_link) && !filter_var($cv_link, FILTER_VALIDATE_URL)) {
        $error = "Sila masukkan pautan CV yang sah (contoh: https://...).";
    }

    if (!$error) {
        // Semak email unik
        $check = $conn->prepare("SELECT id_penyelia FROM penyelia WHERE email = ? AND id_penyelia != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email sudah digunakan oleh penyelia lain.";
        }
    }

    // Jika tiada error, teruskan kemaskini
    if (!$error) {
        // Handle upload gambar profile
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['profile_image']['type'];

            if (in_array($file_type, $allowed_types)) {
                $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'profile_' . $user_id . '.' . $ext;
                $upload_dir = 'uploads/profile_images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $target_file = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image = $new_filename;
                } else {
                    $error = "Gagal memuat naik gambar.";
                }
            } else {
                $error = "Jenis fail tidak dibenarkan. Sila muat naik JPG, PNG atau GIF.";
            }
        }
    }

    // Update data jika tiada error
    if (!$error) {
        if (isset($profile_image)) {
            $sql = "UPDATE penyelia SET 
                nama_penyelia=?, 
                jawatan=?, 
                course=?, 
                email=?, 
                telefon=?, 
                jabatan=?, 
                pautan_cv=?, 
                kuota=?, 
                profile_image=? 
                WHERE id_penyelia=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssisi", $full_name, $academic_position, $department, $email, $phone, $institution, $cv_link, $max_quota, $profile_image, $user_id);
        } else {
            $sql = "UPDATE penyelia SET 
                nama_penyelia=?, 
                jawatan=?, 
                course=?, 
                email=?, 
                telefon=?, 
                jabatan=?, 
                pautan_cv=?, 
                kuota=? 
                WHERE id_penyelia=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssisi", $full_name, $academic_position, $department, $email, $phone, $institution, $cv_link, $max_quota, $user_id);
        }

        if ($stmt->execute()) {
            $success = "Profil berjaya dikemaskini.";
            // Reload data terbaru
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $supervisor = $result->fetch_assoc();
        } else {
            $error = "Ralat semasa kemaskini: " . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cv_link = trim($_POST['cv_link'] ?? '');

    // Validasi
    if (!empty($cv_link) && !filter_var($cv_link, FILTER_VALIDATE_URL)) {
        $error = "Pautan CV tidak sah.";
    } else {
        $stmt = $conn->prepare("UPDATE penyelia SET pautan_cv = ? WHERE id_penyelia = ?");
        $stmt->bind_param("si", $cv_link, $user_id);
        if ($stmt->execute()) {
            $success = "Pautan CV berjaya dikemaskini.";
        } else {
            $error = "Gagal kemaskini pautan CV.";
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
<title>Kemaskini Profil Penyelia</title>
<style>
/* =========================
   Minimal / MI Style
   ========================= */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 40px 15px;
    display: flex;
    justify-content: center;
}

.profile-form {
    background: #ffffff;
    max-width: 700px;
    width: 100%;
    border-radius: 15px;
    padding: 35px 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease-in-out;
}

.profile-form:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.profile-form h2 {
    text-align: center;
    color: #002b5c;
    margin-bottom: 25px;
    font-weight: 700;
}

.profile-image-preview {
    display: block;
    margin: 0 auto 20px auto;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #334155;
}

input[type="text"],
input[type="email"],
input[type="number"],
input[type="file"] {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: border 0.2s ease;
}

input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 5px rgba(14,165,233,0.3);
}

button {
    width: 100%;
    padding: 12px 0;
    background-color: #16a34a;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease-in-out;
}

button:hover {
    background-color: #15803d;
    transform: translateY(-1px);
}

.message {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 500;
    text-align: center;
}

.error { background: #fee2e2; color: #b91c1c; }
.success { background: #dcfce7; color: #166534; }

@media(max-width: 600px){
    .profile-form { padding: 25px 20px; }
    .profile-image-preview { width: 100px; height: 100px; }
}
</style>
</head>
<body>

<form method="POST" enctype="multipart/form-data" class="profile-form">
    <h2>KEMASKINI PROFIL</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($supervisor['profile_image'])): ?>
        <img src="uploads/profile_images/<?= htmlspecialchars($supervisor['profile_image']) ?>" class="profile-image-preview" alt="Profile Image">
    <?php endif; ?>

    <label>NAMA:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($supervisor['nama_penyelia'] ?? '') ?>" required>

    <label>JAWATAN:</label>
    <input type="text" name="academic_position" value="<?= htmlspecialchars($supervisor['jawatan'] ?? '') ?>" required>

    <label>PROGRAM:</label>
    <input type="text" name="department" value="<?= htmlspecialchars($supervisor['course'] ?? '') ?>" required>

    <label>EMAIL:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($supervisor['email'] ?? '') ?>" required>

    <label>NOM.TELEFON:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($supervisor['telefon'] ?? '') ?>" required>

    <label>JABATAN:</label>
    <input type="text" name="institution" value="<?= htmlspecialchars($supervisor['jabatan'] ?? '') ?>" required>

    <label>PAUTAN CV:</label>
    <input type="text" name="cv_link" value="<?= htmlspecialchars($supervisor['pautan_cv'] ?? '') ?>">

    <label>KUOTA MAKSIMUM PELAJAR:</label>
    <input type="number" name="max_quota" value="<?= htmlspecialchars($supervisor['kuota'] ?? '') ?>" min="0" required>

    <label>MUAT NAIK GAMBAR PROFILE (JPG, PNG, GIF):</label>
    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif">

    <button type="submit">SIMPAN</button>
</form>

</body>
</html>
