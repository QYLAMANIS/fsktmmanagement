<?php
// Elak error jika session belum dimulakan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Dapatkan maklumat pengguna semasa
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Pastikan pengguna sudah login
if (!$user_id || !$role) {
    header("Location: login.php");
    exit();
}

// Ambil maklumat pengguna ikut role
if ($role === 'pelajar') {
    $stmt = $conn->prepare("SELECT nama_pelajar AS nama, profile_image FROM pelajar WHERE id_pelajar = ?");
} elseif ($role === 'penyelia') {
    $stmt = $conn->prepare("SELECT nama_penyelia AS nama, profile_image FROM penyelia WHERE id_penyelia = ?");
} else {
    $stmt = null;
}

$nama = '';
$profile_image = '';

// Jalankan query
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($nama, $profile_image);
    $stmt->fetch();
    $stmt->close();
}

// Tetapkan gambar default jika tiada
$image_path = (!empty($profile_image) && file_exists(__DIR__ . '/uploads/' . $profile_image))
    ? 'uploads/' . $profile_image
    : 'images/default.png';

// Kira jumlah notifikasi belum dibaca
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifikasi WHERE penerima_id = ? AND penerima_role = ? AND status = 'baru'");
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$stmt->bind_result($unread_count);
$stmt->fetch();
$stmt->close();
?>

<!-- 🌟 HEADER HTML -->
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- 🌸 Favicon untuk semua page -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/uthm_logo.png">
    <link rel="shortcut icon" href="images/uthm_logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="images/uthm_logo.png">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<style>
.topbar {
  width: 100%;
  background: #dc2626; /* merah UTHM style */
  color: white;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 6px 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
  position: sticky;
  top: 0;
  z-index: 80;
}

.topbar .icon {
  margin: 0 15px;
  position: relative;
  cursor: pointer;
}

.topbar .icon .badge {
  position: absolute;
  top: -6px;
  right: -10px;
  background: #22c55e;
  color: #fff;
  font-size: 11px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 50%;
}

.topbar .profile {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  text-decoration: none;
}

.topbar .profile img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid white;
  object-fit: cover;
}

.topbar .profile span {
  font-size: 0.9rem;
  font-weight: 600;
}

.topbar a.logout {
  color: white;
  margin-left: 20px;
  text-decoration: none;
}

.topbar a.logout:hover {
  color: #ffe4e4;
}
</style>
<!-- 🌟 Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="images/logo-fsktm.png">
<link rel="apple-touch-icon" href="images/logo-fsktm.png">

<div class="topbar">
  <!-- Notifikasi -->
  <a href="notifikasi.php" class="icon" title="Notifikasi">
    <i class="fas fa-bell fa-lg"></i>
    <?php if ($unread_count > 0): ?>
      <span class="badge"><?php echo $unread_count; ?></span>
    <?php endif; ?>
  </a>

  <!-- Profil -->
  <a href="student_profile.php" class="profile">
    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile">
    <span><?php echo htmlspecialchars($user_id); ?></span>
  </a>

  <!-- Logout -->
  <a href="logout.php" class="logout" title="Log Keluar">
    <i class="fas fa-right-from-bracket fa-lg"></i>
  </a>
</div>
