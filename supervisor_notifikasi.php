<?php
session_start();
require_once 'config.php';

// -----------------------------
// 1. Semak login penyelia
// -----------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];

// -----------------------------
// 2. Jika klik satu notifikasi → tandakan sebagai dibaca
// -----------------------------
if (isset($_GET['read'])) {
    $id_notifikasi = intval($_GET['read']);

    // Ambil link
    $stmt = $conn->prepare("
        SELECT link FROM notifikasi 
        WHERE id_notifikasi = ? 
          AND penerima_id = ? 
          AND penerima_role = 'penyelia'
    ");
    $stmt->bind_param("ii", $id_notifikasi, $id_penyelia);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        // Mark as read
        $update = $conn->prepare("UPDATE notifikasi SET status='dibaca' WHERE id_notifikasi=?");
        $update->bind_param("i", $id_notifikasi);
        $update->execute();

        // Redirect jika ada link
        if (!empty($row['link'])) {
            header("Location: " . $row['link']);
            exit();
        }
    }
}

// -----------------------------
// 3. Aksi: Tandakan Semua Dibaca / Hapus Semua
// -----------------------------
if (isset($_POST['mark_all_read'])) {

    $stmt = $conn->prepare("
        UPDATE notifikasi 
        SET status='dibaca' 
        WHERE penerima_id = ? 
          AND penerima_role = 'penyelia'
    ");
    $stmt->bind_param("i", $id_penyelia);
    $stmt->execute();

} elseif (isset($_POST['delete_all'])) {

    $stmt = $conn->prepare("
        DELETE FROM notifikasi 
        WHERE penerima_id = ? 
          AND penerima_role = 'penyelia'
    ");
    $stmt->bind_param("i", $id_penyelia);
    $stmt->execute();
}

// -----------------------------
// 4. Ambil semua notifikasi
// -----------------------------
$stmt = $conn->prepare("
    SELECT id_notifikasi, mesej, link, status, created_at 
    FROM notifikasi
    WHERE penerima_id = ? 
      AND penerima_role = 'penyelia'
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $id_penyelia);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Penyelia | Notifikasi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fb; font-family: 'Segoe UI', sans-serif; padding: 25px; }
.card { border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.card-header { background-color: #064e3b; color: #fff; text-align: center; font-weight: 600; }
.notification-item { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
.notification-item:hover { background: #e9f8f0; }
.badge-new { background-color: #10b981; padding: 5px 8px; }
.actions { display: flex; justify-content: space-between; margin-bottom: 15px; }
a { text-decoration: none; color: inherit; }
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="card-header">📢 Notifikasi Penyelia</div>
    <div class="card-body">

      <!-- Butang -->
      <form method="post" class="actions">
        <button type="submit" name="mark_all_read" class="btn btn-outline-success btn-sm">
            Tandakan Semua Dibaca
        </button>

        <button type="submit" name="delete_all" class="btn btn-outline-danger btn-sm"
                onclick="return confirm('Padam semua notifikasi?')">
            Padam Semua
        </button>
      </form>

      <!-- Papar notifikasi -->
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          
          <a href="supervisor_notifikasi.php?read=<?= $row['id_notifikasi'] ?>">
              <div class="notification-item">
                <div>
                  <p class="mb-1"><?= htmlspecialchars($row['mesej']) ?></p>
                  <small class="text-muted">📅 <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small><br>

                  <?php if (!empty($row['link'])): ?>
                    <span class="badge bg-success mt-2">Klik untuk lihat</span>
                  <?php endif; ?>
                </div>

                <?php if ($row['status'] === 'baru' || $row['status'] === 'belum_baca'): ?>
                  <span class="badge-new">Baru</span>
                <?php endif; ?>
              </div>
          </a>

        <?php endwhile; ?>
      <?php else: ?>
        <div class="alert alert-info text-center mb-0">Tiada notifikasi buat masa ini 😊</div>
      <?php endif; ?>

    </div>
  </div>
</div>

</body>
</html>
