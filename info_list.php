<?php
session_start();
require_once 'config.php';

// ✅ Semak login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Benarkan hanya Pelajar & Penyelia
if (!in_array($_SESSION['role'], ['pelajar', 'penyelia'])) {
    echo "<script>alert('Akses ditolak. Halaman ini hanya untuk pelajar atau penyelia.'); window.location.href='dashboard.php';</script>";
    exit();
}

$uploadDir = 'uploads/info/';
$result = $conn->query("SELECT * FROM info ORDER BY created_at DESC");

// helper escape
function h($s){ return htmlspecialchars($s, ENT_QUOTES,'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ms">
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Maklumat Terkini</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;

  margin: 0;
  padding: 30px;
  font-size: 13px;

}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  color: #1a1a1a;
  font-size: 20px;
  text-align: center;
  margin-bottom: 25px;
  border-bottom: 2px solid #ccc;
  padding-bottom: 8px;
  letter-spacing: 0.3px;
}

/* Setiap card (notis) */
.card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 20px;
  overflow: hidden;
}

.card-img img {
  width: 100%;
  height: auto;
  display: block;
  border-bottom: 1px solid #eee;
}

.card-body {
  padding: 15px 20px;
}

/* Tajuk setiap notis */
.card-body h3 {
  font-size: 15px;
  color: #0d3b66;
  margin: 0 0 6px;
  font-weight: 600;
}

/* Tarikh & jenis info */
.meta {
  font-size: 12px;
  color: #666;
  margin-bottom: 8px;
}

/* Kandungan utama */
.content {
  font-size: 13px;
  color: #333;
  line-height: 1.6;
  margin-bottom: 10px;
}

/* Pautan dan dokumen */
.links a {
  display: inline-block;
  font-size: 12px;
  color: #0d3b66;
  border: 1px solid #0d3b66;
  padding: 4px 8px;
  border-radius: 4px;
  text-decoration: none;
  margin-right: 6px;
  transition: 0.2s;
}

.links a:hover {
  background: #0d3b66;
  color: #fff;
}

/* Jika tiada data */
.no-data {
  text-align: center;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 15px;
  font-size: 13px;
  color: #777;
}

/* Responsif */
@media (max-width: 768px) {
  body { padding: 15px; font-size: 12px; }
  h1 { font-size: 18px; }
  .card-body h3 { font-size: 14px; }
}

.form-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

.form-table th, .form-table td {
    border: 1px solid #ccc;
    padding: 10px 12px;
    text-align: left;
}

.form-table th {
    background-color: #0d3b66;
    color: #fff;
}

.table-btn {
    padding: 5px 10px;
    border-radius: 5px;
    background-color: #0d3b66;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
}

.table-btn:hover {
    background-color: #0a2d50;
}


</style>


</head>
<body>

<div class="container">
    <h1>Maklumat</h1>

    <?php if($result->num_rows == 0): ?>
        <div class="no-data">Tiada maklumat buat masa ini.</div>
    <?php endif; ?>

    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <div class="card-img">
                <?php if(!empty($row['image']) && file_exists($uploadDir . $row['image'])): ?>
                    <img src="<?= h($uploadDir . $row['image']); ?>" alt="">
                <?php else: ?>
                    <div style="color:#aaa; padding:18px;">Tiada gambar</div>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <h3><?= h($row['title']); ?></h3>

                <div class="meta">
                    <?= date("d M Y, H:i", strtotime($row['created_at'])); ?> 
                    · <?= $row['info_type'] ? h($row['info_type']) : 'Umum' ?>
                </div>

                <div class="content"><?= $row['content']; ?></div>

                <div class="links">
                    <?php if(!empty($row['link'])): ?>
                        <a href="<?= h($row['link']); ?>" target="_blank">🔗 Buka Pautan</a>
                    <?php endif; ?>

                    <?php if(!empty($row['attachment']) && file_exists($uploadDir . $row['attachment'])): ?>
                        <a href="<?= h($uploadDir . $row['attachment']); ?>" download>📎 Muat Turun</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
