<?php
// Pastikan $page_title ada nilai
if (!isset($page_title) || empty($page_title)) {
    // AUTO DETECT nama file → dijadikan tajuk
    $file = basename($_SERVER['PHP_SELF'], ".php");
    $page_title = ucwords(str_replace("_", " ", $file));
}
?>

<style>
.topbar {
    width: 100%;
    background: #ffffff;
    padding: 15px 25px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 22px;
    font-weight: 600;
    color: #1a3d7c;
}

.profile-area {
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-area img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-area a {
    font-weight: 600;
    text-decoration: none;
    color: #1a3d7c;
}
</style>

<div class="topbar">
    <div class="page-title">
        <?= htmlspecialchars($page_title) ?>
    </div>

    <div class="profile-area">
        <img src="images/default.png">
        <a href="#"><?= $_SESSION['no_matrik'] ?? '' ?></a>
    </div>
</div>
