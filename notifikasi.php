<?php
session_start();
require_once 'config.php';

// Pastikan login pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header("Location: student_login.php");
    exit();
}

$id_pelajar = $_SESSION['user_id'];

// Klik → tanda dibaca & redirect
if (isset($_GET['read'])) {
    $id_notif = intval($_GET['read']);

    $stmt = $conn->prepare("
        SELECT link FROM notifikasi 
        WHERE id_notifikasi = ? AND penerima_id = ? AND penerima_role='pelajar'
    ");
    $stmt->bind_param("ii", $id_notif, $id_pelajar);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        $u = $conn->prepare("UPDATE notifikasi SET status='dibaca' WHERE id_notifikasi=?");
        $u->bind_param("i", $id_notif);
        $u->execute();

        if (!empty($row['link'])) {
            header("Location: " . $row['link']);
            exit();
        }
    }
}

// Ambil semua notifikasi
$stmt = $conn->prepare("
    SELECT id_notifikasi, mesej, status, created_at 
    FROM notifikasi
    WHERE penerima_id=? AND penerima_role='pelajar'
    ORDER BY id_notifikasi DESC
");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pelajar | Notifikasi</title>

<style>
body { font-family: Arial; background:#fff; padding:25px; }
.container { max-width:700px; margin:auto; }
.notif-card { background:#f8f9fc; border-radius:10px; padding:18px;
               border:1px solid #ddd; margin-bottom:12px; transition:.2s; }
.notif-card.unread { background:#e8f0ff; border-left:6px solid #1a73e8; }
.notif-card.read { background:#eee; border-left:6px solid #999; }
.notif-card:hover { transform:scale(1.01); }
</style>

</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Notifikasi</h2>
    <br>

    <?php if ($result->num_rows === 0): ?>
        <p style="text-align:center;color:#777;">Tiada notifikasi.</p>
    <?php endif; ?>

    <?php while ($n = $result->fetch_assoc()): ?>
        <a href="notifikasi.php?read=<?= $n['id_notifikasi'] ?>" style="text-decoration:none;color:inherit;">
            <div class="notif-card <?= $n['status']=='baru' ? 'unread':'read' ?>">
                <div style="font-size:15px;"><?= htmlspecialchars($n['mesej']) ?></div>
                <div style="font-size:12px;color:#666;margin-top:6px;">
                    <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>
                </div>
            </div>
        </a>
    <?php endwhile; ?>
</div>

</body>
</html>
