<?php
// supervisor_topbar.php
if (!isset($id_penyelia)) {
    $id_penyelia = $_SESSION['user_id'] ?? 0;
}

// Dapatkan jumlah notifikasi belum dibaca
$unread_count = 0;
if (isset($conn)) {
    $stmt_notif = $conn->prepare("
        SELECT COUNT(*) AS cnt 
        FROM notifikasi 
        WHERE penerima_id = ? 
          AND penerima_role = 'penyelia' 
          AND status = 'baru'
    ");
    $stmt_notif->bind_param("i", $id_penyelia);
    $stmt_notif->execute();
    $result_notif = $stmt_notif->get_result();
    if ($row = $result_notif->fetch_assoc()) {
        $unread_count = $row['cnt'];
    }
}

// Pastikan profile image wujud
if (!isset($profile_img)) {
    $profile_img = 'default_profile.png';
}
?>


<!-- Topbar HTML -->
<div class="topbar">
    <a href="#" class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fa fa-bars"></i>
    </a>

    <div class="right-icons">
        <a href="supervisor_notifikasi.php" class="icon" target="contentFrame">
            <i class="fas fa-bell fa-lg"></i>
            <?php if ($unread_count > 0): ?>
                <span class="badge"><?= $unread_count ?></span>
            <?php endif; ?>
        </a>

        <!-- Profile dropdown -->
        <div class="profile-dropdown">
            <a href="#" class="profile-toggle">
                <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile">
            </a>
            <div class="dropdown-menu">
                <a href="supervisor_profile.php" target="contentFrame">
                    <i class="fas fa-user"></i> Profil Penyelia
                </a>
                <a href="supervisor_edit_profil.php" target="contentFrame">
                    <i class="fas fa-edit"></i> Edit Profil
                </a>
            </div>
        </div>

        <a href="logout.php" class="logout">
            <i class="fas fa-right-from-bracket fa-lg"></i>
        </a>
    </div>
</div>

<!-- Topbar CSS -->
<style>
/* ===== TOPBAR ===== */
.topbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #2c3e50;
    padding: 0 20px;
    color: #fff;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.right-icons a {
    color: #fff;
    margin-left: 15px;
    text-decoration: none;
    font-size: 18px;
    position: relative;
}

.right-icons .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #e74c3c;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    padding: 2px 5px;
}

/* Profile dropdown */
.profile-dropdown {
    position: relative;
    display: inline-block;
}

.profile-dropdown .profile-toggle img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
}

.profile-dropdown .dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background-color: #fff;
    min-width: 150px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    border-radius: 5px;
    overflow: hidden;
    z-index: 1001;
}

.profile-dropdown .dropdown-menu a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
}

.profile-dropdown .dropdown-menu a i {
    margin-right: 10px;
}

.profile-dropdown .dropdown-menu a:hover {
    background-color: #f1f1f1;
}

.profile-dropdown.active .dropdown-menu {
    display: block;
}

/* Sidebar toggle hover */
.sidebar-toggle i:hover {
    color: #f1c40f;
}

/* Logout hover */
.right-icons .logout:hover,
.right-icons .icon:hover {
    color: #f1c40f;
}

/* ===== BODY ADJUSTMENT ===== */
body {
    padding-top: 70px; /* adjust top padding untuk topbar */
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .topbar {
        flex-direction: row;
        justify-content: space-between;
        padding: 0 10px;
    }

    .right-icons {
        display: flex;
        align-items: center;
    }

    .right-icons a, .right-icons .profile-dropdown {
        margin-left: 10px;
    }

    .profile-dropdown .dropdown-menu {
        right: -20px;
    }
}

@media (max-width: 480px) {
    .topbar {
        height: 50px;
    }

    .profile-dropdown .profile-toggle img {
        width: 30px;
        height: 30px;
    }

    .right-icons a i {
        font-size: 16px;
    }

    body {
        padding-top: 60px;
    }
}
</style>

<!-- Topbar JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileToggle = document.querySelector('.profile-toggle');
    const profileDropdown = document.querySelector('.profile-dropdown');

    profileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        profileDropdown.classList.toggle('active');
    });

    window.addEventListener('click', function(e) {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
    });
});
</script>
