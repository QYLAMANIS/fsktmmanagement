<?php
session_start();
require_once 'config.php';

// ✅ Pastikan login & role betul
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$_SESSION['name'] = 'Admin';
$_SESSION['email'] = 'admin@uthm.edu.my';
$_SESSION['images'] = 'images/logo-fsktm.png';

// Fallback gambar
$image = (file_exists($_SESSION['images'])) ? $_SESSION['images'] : 'images/logo-fsktm.png';
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif;}
    body {display: flex; height: 100vh; background-color: #f4f7fb; overflow: hidden;}

    /* 🟦 SIDEBAR */
    .sidebar {
      width: 200px;
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
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      padding: 8px 20px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.08);
      position: sticky; top: 0; z-index: 90;
    }

    .sidebar-toggle {
      color: #0d3b66; font-size: 26px;
      cursor: pointer; border: none; background: none;
      transition: 0.3s;
    }
    .sidebar-toggle:hover {color: #1b4f72;}

    /* 🌟 PROFILE DROPDOWN TOP RIGHT */
    .topbar .profile-area {
      position: relative;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .topbar .profile-area img {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      border: 2px solid #0d3b66;
      object-fit: cover;
      transition: 0.3s;
    }

    .topbar .profile-area span {
      font-weight: 600;
      font-size: 0.9rem;
      color: #0d3b66;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 48px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      min-width: 160px;
      z-index: 100;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px 14px;
      color: #0d3b66;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background 0.3s;
    }

    .dropdown-menu a:hover {
      background: #f0f4ff;
    }

    .profile-area:hover .dropdown-menu {
      display: block;
    }

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
  </style>
</head>

<body>
<!-- 🟦 SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="profile">
    <img src="<?= htmlspecialchars($image) ?>" alt="Profile Image">
    <h3><?= htmlspecialchars($_SESSION['name']) ?></h3>
    <p><?= htmlspecialchars($_SESSION['email']) ?></p>
  </div>

  <ul class="sidebar-menu">
    <li><a href="admin_info.php" target="contentFrame" data-title="Dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
    <li><a href="admin_manage_student.php" target="contentFrame" data-title="Senarai Pelajar"><i class="fas fa-user-graduate"></i><span>Senarai Pelajar</span></a></li>
    <li><a href="admin_manage_supervisors.php" target="contentFrame" data-title="Senarai Penyelia"><i class="fas fa-chalkboard-teacher"></i><span>Senarai Penyelia</span></a></li>
    <li><a href="admin_manage_titles.php" target="contentFrame" data-title="Senarai Tajuk PSM"><i class="fas fa-file-alt"></i><span>Senarai Tajuk PSM</span></a></li>
  </ul>
</div>

<!-- 🔵 MAIN AREA -->
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>

    <div class="profile-area">
      <span><?= htmlspecialchars($_SESSION['name']) ?></span>
      <img src="<?= htmlspecialchars($image) ?>" alt="Profile">
      <div class="dropdown-menu">
        <a href="admin_profile.php" target="contentFrame"><i class="fa fa-user"></i> Profil</a>
        <a href="admin_edit_profile.php" target="contentFrame"><i class="fa fa-pen"></i> Kemaskini</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Log Keluar</a>
      </div>
    </div>
  </div>

  <section class="content-header" id="page-header">
    <h1>Admin <small id="page-subtitle">| Dashboard</small></h1>
  </section>

  <iframe name="contentFrame" src="admin_info.php"></iframe>
</div>

<script>
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
