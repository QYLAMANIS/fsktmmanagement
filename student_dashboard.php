<?php
session_start();
require_once 'config.php';

// ✅ Papar mesej berjaya (jika ada)
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" style="max-width:800px; margin:20px auto; text-align:center;">' 
         . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// ✅ Semak login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Dapatkan maklumat pelajar
$stmt = $conn->prepare("SELECT nama_pelajar, no_matrik, emel, profile_image FROM pelajar WHERE id_pelajar = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $matrik, $email, $profile_image);
$stmt->fetch();
$stmt->close();

$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['matrik'] = $matrik;

// ✅ Gambar profil fallback
$image = ($profile_image && file_exists(__DIR__ . '/uploads/' . $profile_image))
    ? 'uploads/' . $profile_image
    : 'uploads/profile_images/default_profile.png';

// ✅ Kira notifikasi belum dibaca
$role = $_SESSION['role'];
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifikasi WHERE penerima_id = ? AND penerima_role = ? AND status = 'baru'");
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$stmt->bind_result($unread_count);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Dashboard Pelajar</title>
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
  overflow-y: auto;
  transition: width 0.3s ease;
}
.sidebar.collapsed {width: 80px;}

.sidebar .profile {text-align: center; margin-bottom: 20px;}
.sidebar .profile img {
  width: 85px; height: 85px; border-radius: 50%;
  border: 3px solid #fff; object-fit: cover;
}
.sidebar .profile h3 {font-size: 1.1rem;}
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
.sidebar a:hover, .sidebar a.active {background-color: #1b4f72; transform: translateX(5px);}

/* Dropdown Sidebar */
.treeview > a::after {
  content: "\f107"; font-family: "Font Awesome 6 Free"; font-weight: 900;
  margin-left: auto; transition: transform 0.3s;
}
.treeview.open > a::after {transform: rotate(180deg);}
.treeview-menu {display: none; padding-left: 25px;}
.treeview.open .treeview-menu {display: block;}

/* 🟩 TOPBAR */
.topbar {
  width: 100%; display: flex; align-items: center;
  justify-content: space-between; background: #fff;
  padding: 8px 16px; box-shadow: 0 3px 6px rgba(0,0,0,0.08);
  position: sticky; top: 0; z-index: 80;
}
.sidebar-toggle {
  color: #0d3b66; font-size: 22px; text-decoration: none;
  padding: 10px 15px; border-radius: 5px;
}
.sidebar-toggle:hover {background-color: #0d3b66; color: #fff;}

.topbar .right-icons {display: flex; align-items: center; gap: 20px;}
.topbar .icon {position: relative; cursor: pointer; color: #0d3b66;}
.topbar .icon:hover {color: #3b82f6;}
.topbar .badge {
  position: absolute; top: -6px; right: -10px;
  background: #3b82f6; color: #fff; font-size: 11px;
  padding: 2px 6px; border-radius: 50%;
}
.topbar .profile {
  display: flex; align-items: center; gap: 10px;
  cursor: pointer; color: #0d3b66; text-decoration: none;
}
.topbar .profile img {
  width: 38px; height: 38px; border-radius: 50%; object-fit: cover;
  border: 2px solid #0d3b66;
}
.topbar .profile span {font-size: 0.9rem; font-weight: 600;}

/* 🟦 TOPBAR DROPDOWN */
.profile-menu {
  display: none;
  position: absolute;
  top: 48px;
  right: 0;
  background: #fff;
  border-radius: 6px;
  min-width: 180px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  padding: 8px 0;
  z-index: 999;
}
.profile-menu li {list-style: none;}
.profile-menu a {
  display: block; padding: 10px 15px;
  font-size: 0.9rem; color: #0d3b66;
  text-decoration: none;
}
.profile-menu a:hover {background: #f4f7fb;}
.profile-menu hr {
  margin: 6px 0; border: none; border-top: 1px solid #ddd;
}
.profile-menu i {width: 18px; text-align: center; margin-right: 6px;}

/* 🟧 HEADER */
.content-header {
  padding: 15px 25px; background: #fff; border-bottom: 1px solid #ddd;
}
.content-header h1 {color: #0d3b66; font-size: 20px; font-weight: 600;}
.content-header small {color: #666;}

/* 🟨 MAIN */
.main-content {flex-grow: 1;}
iframe {
    width: 100%;
    height: calc(100vh - 160px); /* kecilkan supaya footer tak overlap */
    border: none;
    background: #fff;
}


/* FOOTER PUTIH */
.footer {
    width: 100%;
    background: #ffffff;
    color: #0d3b66;
    text-align: center;
    padding: 12px 0;
    font-size: 0.9rem;
    border-top: 1px solid #ddd;
}



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
    <li>
        <a href="info_list.php" target="contentFrame" onclick="setTitle('Paparan Info')">
            <i class="fas fa-home"></i><span>Paparan Info</span>
        </a>
    </li>

    <li class="treeview">
        <a href="javascript:void(0)" onclick="toggleTreeview(this)">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Penyelia</span>
        </a>
        <ul class="treeview-menu">
            <li><a href="student_list_supervisor.php" target="contentFrame" onclick="setTitle('Senarai Penyelia')"><i class="fa fa-user"></i> Senarai Penyelia</a></li>
            <li><a href="student_view_submission.php" target="contentFrame" onclick="setTitle('Status Permohonan')"><i class="fa fa-folder-open"></i> Status Permohonan</a></li>
            <li><a href="student_permohonan_temujanji.php" target="contentFrame" onclick="setTitle('Permohonan Temujanji')"><i class="fa fa-calendar"></i> Permohonan Temujanji</a></li>
            <li><a href="student_senarai_temujanji.php" target="contentFrame" onclick="setTitle('Senarai Temujanji')"><i class="fa fa-list"></i> Senarai Temujanji</a></li>
            <li><a href="student_view_titles.php" target="contentFrame" onclick="setTitle('Senarai Tajuk')"><i class="fa fa-book"></i> Senarai Tajuk</a></li>
        </ul>
    </li>

    <li><a href="student_upload_report.php" target="contentFrame" onclick="setTitle('Laporan')"><i class="fas fa-file-upload"></i><span>Laporan</span></a></li>
    <li><a href="student_logbook.php" target="contentFrame" onclick="setTitle('Buku Log')"><i class="fas fa-book"></i><span>Buku Log</span></a></li>
    <li><a href="ai_search.php" target="contentFrame" onclick="setTitle('Undergraduate Project Report')"><i class="fas fa-file-alt"></i><span>Undergraduate Project Report</span></a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Log Keluar</span></a></li>
</ul>

</div>

<!-- 🔵 MAIN -->
<div class="main-content">
  
  <!-- TOPBAR -->
  <div class="topbar">
    <a href="#" class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa fa-bars"></i></a>

    <div class="right-icons">

      <!-- NOTIFIKASI -->
      <a href="notifikasi.php" class="icon" target="contentFrame">
        <i class="fas fa-bell fa-lg"></i>
        <?php if ($unread_count > 0): ?>
          <span class="badge"><?= $unread_count ?></span>
        <?php endif; ?>
      </a>

      <!-- DROPDOWN PROFILE -->
      <div class="dropdown" style="position: relative;">
        <a href="#" class="profile" onclick="toggleProfileMenu(event)">
          <img src="<?= htmlspecialchars($image) ?>" alt="Profile">
          <span><?= htmlspecialchars($_SESSION['matrik']) ?></span>
          <i class="fa fa-caret-down" style="font-size:14px;"></i>
        </a>

<ul id="profileMenu" class="profile-menu">
    <li><a href="student_profile.php" target="contentFrame" onclick="setTitle('Profil')"><i class="fa fa-user"></i> Profil</a></li>
    <li><a href="student_edit_profile.php" target="contentFrame" onclick="setTitle('Kemaskini Profil')"><i class="fa fa-pen"></i> Kemaskini Profil</a></li>
    <li><a href="student_change_password.php" target="contentFrame" onclick="setTitle('Tukar Katalaluan')"><i class="fa fa-key"></i> Tukar Katalaluan</a></li>
    <hr>
    <li><a href="logout.php"><i class="fa fa-right-from-bracket"></i> Log Keluar</a></li>
</ul>

      </div>

    </div>
  </div>

  <!-- HEADER -->
  <section class="content-header" id="page-header">
    <h1>Pelajar <small id="page-subtitle">| Paparan Info</small></h1>
  </section>

  <iframe name="contentFrame" src="info_list.php"></iframe>

  <!-- FOOTER -->
<footer class="footer">
    <p>© <?= date('Y') ?> Sistem PSM UTHM</p>
</footer>


</div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}

// Toggle profile dropdown
function toggleProfileMenu(e) {
  e.preventDefault();
  const menu = document.getElementById("profileMenu");
  const isOpen = menu.style.display === "block";
  menu.style.display = isOpen ? "none" : "block";
}

// Close dropdown when clicking outside
document.addEventListener("click", function(e) {
  const menu = document.getElementById("profileMenu");
  if (!e.target.closest(".dropdown")) {
    menu.style.display = "none";
  }
});

function toggleTreeview(element) {
    const parent = element.parentElement;
    parent.classList.toggle("open");
}

function setTitle(text) {
    // Tukar topbar
    document.getElementById("page-subtitle").textContent = "| " + text;

    // Tukar TAB browser
    document.title = "Pelajar | " + text;
}


// Bila notifikasi.php dibuka dalam iframe,
// update tajuk dekat dashboard.
if (window.parent && window.parent.setTitle) {
    window.parent.setTitl
    
    function loadNotif() {
    fetch("check_notif.php")
        .then(r => r.json())
        .then(data => {
            let badge = document.getElementById("notifBadge");
            badge.textContent = data.count > 0 ? data.count : "";
        });
}
}
setInterval(loadNotif, 5000);
loadNotif();

// Bila notifikasi.php dibuka dalam iframe,
// update tajuk dekat dashboard.
if (window.parent && window.parent.setTitle) {
    window.parent.setTitle('Notifikasi');
}
</script>

</body>
</html>
