<?php
session_start();
require_once 'config.php';

// ✅ Pastikan login & role betul
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// ✅ Dapatkan maklumat penyelia
$stmt = $conn->prepare("SELECT nama_penyelia, email, profile_image FROM penyelia WHERE id_penyelia = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nama_penyelia, $email, $profile_image);
$stmt->fetch();
$stmt->close();

$_SESSION['name'] = $nama_penyelia;
$_SESSION['email'] = $email;

// ✅ Gambar profil fallback
$profile_img = (!empty($profile_image) && file_exists("uploads/profile_images/" . $profile_image))
    ? "uploads/profile_images/" . $profile_image
    : "uploads/profile_images/default_profile.png";

// ✅ Kira notifikasi belum dibaca
$stmt2 = $conn->prepare("SELECT COUNT(*) FROM notifikasi WHERE penerima_id = ? AND penerima_role = 'penyelia' AND status = 'baru'");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$stmt2->bind_result($unread_count);
$stmt2->fetch();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Penyelia</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif;}
    body {display: flex; height: 100vh; background-color: #f4f7fb; overflow: hidden;}

    /* 🟦 SIDEBAR */
    .sidebar {
      width: 250px;
      background-color: #0d3b66;
      color: #fff;
      padding: 20px 10px;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      transition: width 0.3s ease;
      overflow-y: auto;
    }
    .sidebar.collapsed {width: 80px;}

    .sidebar .profile {
      text-align: center;
      margin-bottom: 20px;
    }
    .sidebar .profile img {
      width: 85px; height: 85px; border-radius: 50%;
      border: 3px solid #fff; object-fit: cover;
    }
    .sidebar .profile h3 {font-size: 1.1rem; color: #fff;}
    .sidebar .profile p {font-size: 0.85rem; color: #cce0ff;}
    .sidebar.collapsed .profile h3,
    .sidebar.collapsed .profile p {display: none;}
    .sidebar.collapsed .profile img {width: 55px; height: 55px;}

    .sidebar ul {list-style: none;}
    .sidebar ul li {margin-bottom: 8px;}
    .sidebar a {
      display: flex; align-items: center;
      color: #fff; padding: 12px 15px; text-decoration: none;
      font-size: 0.95rem; border-radius: 4px;
      transition: all 0.25s ease;
    }
    .sidebar a i {width: 25px; text-align: center; font-size: 18px;}
    .sidebar.collapsed a span {display: none;}
    .sidebar a:hover, .sidebar a.active {
      background-color: #1b4f72; transform: translateX(5px);
    }

    /* 🟩 TOPBAR */
    .topbar {
      width: 100%; display: flex; align-items: center;
      justify-content: space-between; background: #fff;
      padding: 8px 16px; box-shadow: 0 3px 6px rgba(0,0,0,0.08);
      position: sticky; top: 0; z-index: 80;
    }
    .sidebar-toggle {
      display: flex; align-items: center; justify-content: center;
      color: #0d3b66; font-size: 22px; text-decoration: none;
      padding: 10px 15px; transition: background 0.3s, color 0.3s;
      margin-right: auto;
    }
    .sidebar-toggle:hover {background-color: #0d3b66; color: #fff; border-radius: 5px;}

    .topbar .right-icons {display: flex; align-items: center; gap: 20px;}
    .topbar .icon {position: relative; cursor: pointer; color: #0d3b66;}
    .topbar .icon:hover {color: #3b82f6;}
    .topbar .icon .badge {
      position: absolute; top: -6px; right: -10px;
      background: #3b82f6; color: #fff; font-size: 11px;
      padding: 2px 6px; border-radius: 50%;
    }
    .topbar .profile {display: flex; align-items: center; gap: 10px; color: #0d3b66;}
    .topbar .profile img {
      width: 38px; height: 38px; border-radius: 50%; object-fit: cover;
      border: 2px solid #0d3b66;
    }
    .topbar .profile span {font-size: 0.9rem; font-weight: 600;}
    .topbar a.logout {color: #0d3b66; text-decoration: none;}
    .topbar a.logout:hover {color: #3b82f6;}

    /* 🟧 HEADER SECTION */
    .content-header {
      padding: 15px 25px;
      background: #fff;
      border-bottom: 1px solid #ddd;
    }
    .content-header h1 {
      color: #0d3b66;
      font-size: 20px;
      font-weight: 600;
    }
    .content-header h1 small {
      color: #666;
      font-weight: 400;
    }

    /* 🟨 MAIN CONTENT */
    .main-content {flex-grow: 1; background: #f9fbff;}
    iframe {width: 100%; height: calc(100vh - 110px); border: none; background-color: #fff;}

    /* Dropdown Profile */
.profile-dropdown {
    position: relative;
}

.profile-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 50px;
    background: white;
    width: 180px;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    z-index: 200;
    padding: 8px 0;
}

.profile-menu li {
    list-style: none;
}

.profile-menu li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    font-size: 14px;
    color: #333;
    text-decoration: none;
}

.profile-menu li a:hover {
    background: #f0f4ff;
}

  </style>
</head>

<body>
<!-- 🟦 SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="profile">
    <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile Image">
    <h3><?= htmlspecialchars($nama_penyelia) ?></h3>
    <p><?= htmlspecialchars($email) ?></p>
  </div>

<ul class="sidebar-menu">
    <li><a href="info_list.php" target="contentFrame" onclick="setTitle('Paparan Info')"><i class="fas fa-home"></i><span>Paparan Info</span></a></li>

    <li><a href="supervisor_titles.php" target="contentFrame" onclick="setTitle('Tajuk Penyelia')"><i class="fas fa-clipboard-list"></i><span>Tajuk Penyelia</span></a></li>

    <li><a href="supervisor_manage_request_permohonan.php" target="contentFrame" onclick="setTitle('Permohonan Pelajar')"><i class="fas fa-file-alt"></i><span>Permohonan Pelajar</span></a></li>

    <li><a href="supervisor_projects.php" target="contentFrame" onclick="setTitle('Pelajar Selian')"><i class="fas fa-folder-open"></i><span>Pelajar Selian</span></a></li>

    <li><a href="supervisor_temujanji.php" target="contentFrame" onclick="setTitle('Temujanji')"><i class="fas fa-calendar-check"></i><span>Temujanji</span></a></li>

    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Log Keluar</span></a></li>
</ul>

</div>

<!-- 🔵 MAIN AREA -->
<div class="main-content">
  <div class="topbar">
    <a href="#" class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></a>

    <div class="right-icons">
      <a href="supervisor_notifikasi.php" class="icon" title="Notifikasi" target="contentFrame">
        <i class="fas fa-bell fa-lg"></i>
        <?php if ($unread_count > 0): ?>
          <span class="badge"><?= $unread_count ?></span>
        <?php endif; ?>
      </a>

<div class="profile-dropdown">
  <a href="#" class="profile" onclick="toggleProfileMenu(event)">
    <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile">
    <i class="fa fa-caret-down" style="color:#0d3b66;"></i>
  </a>

  <ul id="profileMenu" class="profile-menu">
    <li><a href="supervisor_profile.php" target="contentFrame" onclick="setTitle('Profil')"><i class="fa fa-user"></i> Profil</a></li>

    <li><a href="supervisor_edit_profil.php" target="contentFrame" onclick="setTitle('Kemaskini Profil')"><i class="fa fa-pen"></i> Kemaskini Profil</a></li>

    <li><a href="supervisor_change_password.php" target="contentFrame" onclick="setTitle('Tukar Katalaluan')"><i class="fa fa-key"></i> Tukar Katalaluan</a></li>

    <hr style="border:0; border-top:1px solid #ddd; margin:5px 0;">

    <li><a href="logout.php"><i class="fa fa-right-from-bracket"></i> Log Keluar</a></li>
  </ul>
</div>



      <a href="logout.php" class="logout"><i class="fas fa-right-from-bracket fa-lg"></i></a>
    </div>
  </div>

  <!-- 🟧 PAGE HEADER -->
  <section class="content-header" id="page-header">
    <h1>Penyelia <small id="page-subtitle">| Paparan Info</small></h1>
  </section>

  <iframe name="contentFrame" src="info_list.php"></iframe>
</div>

<script>
function toggleProfileMenu(e) {
    e.preventDefault();
    document.getElementById("profileMenu").style.display =
        document.getElementById("profileMenu").style.display === "block"
        ? "none"
        : "block";
}

// Hide menu bila klik luar
window.addEventListener("click", function(e) {
    if (!e.target.closest(".profile-dropdown")) {
        document.getElementById("profileMenu").style.display = "none";
    }
});

  function setTitle(text) {
    // Tukar header
    document.getElementById("page-subtitle").textContent = "| " + text;

    // Tukar tajuk tab browser
    document.title = "Penyelia | " + text;
}

function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}

// Tukar tajuk header ikut page
const headerSubtitle = document.getElementById("page-subtitle");
document.querySelectorAll('.sidebar a[target="contentFrame"]').forEach(link => {
  link.addEventListener('click', () => {
    const title = link.getAttribute("data-title");
    if (title) headerSubtitle.textContent = "| " + title;
  });
});
</script>
</body>
</html>
