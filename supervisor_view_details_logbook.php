<?php
session_start();
require_once 'config.php';

// 🔐 Semak login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];

// 📌 Semak permohonan ID
if (!isset($_GET['id'])) {
    echo "<p style='text-align:center;color:red;'>Tiada permohonan dipilih. <a href='supervisor_projects.php'>Kembali</a></p>";
    exit();
}
$permohonan_id = (int)$_GET['id'];

// 📌 Get all logs for this permohonan
$stmt = $conn->prepare("SELECT * FROM logbook WHERE permohonan_id=? ORDER BY week_no ASC");
$stmt->bind_param("i", $permohonan_id);
$stmt->execute();
$result = $stmt->get_result();

// 📌 Get selected log
$selected_id = $_GET['selected_id'] ?? null;
$selected_log = null;
if ($selected_id) {
    $stmt2 = $conn->prepare("SELECT * FROM logbook WHERE id=? AND permohonan_id=?");
    $stmt2->bind_param("ii", $selected_id, $permohonan_id);
    $stmt2->execute();
    $selected_log = $stmt2->get_result()->fetch_assoc();
}

// 📌 Save supervisor comment/status
if (isset($_POST['save_supervisor'])) {
    $log_id = $_POST['log_id'];
    $supervisor_comment = $_POST['supervisor_comment'];
    $supervisor_status = $_POST['supervisor_status'];

    $stmt3 = $conn->prepare("
        UPDATE logbook 
        SET supervisor_comment=?, 
            supervisor_status=?, 
            status=?, 
            updated_at=NOW() 
        WHERE id=?
    ");
    $stmt3->bind_param("sssi", $supervisor_comment, $supervisor_status, $supervisor_status, $log_id);
    $stmt3->execute();
    $stmt3->close();

    header("Location: supervisor_view_details_logbook.php?id=$permohonan_id&selected_id=$log_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logbook Penyelia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== RESET & BASE ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: #f4f5f7;
  color: #222;
  margin: 0;
  padding: 25px;
}

/* ===== LAYOUT (2 COLUMN) ===== */
.container-logbook {
  display: flex;
  gap: 30px;
  padding: 25px;
  max-width: 1200px;
  margin: auto;
}

/* ===== LEFT & RIGHT PANELS ===== */
.left-panel, .right-panel {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #ddd;
  box-shadow: 0 3px 10px rgba(0,0,0,0.06);
  padding: 25px;
  transition: all 0.3s ease;
}

.left-panel { width: 32%; }
.right-panel { flex-grow: 1; }

.left-panel:hover,
.right-panel:hover {
  box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

/* ===== HEADINGS ===== */
.left-panel h5, .right-panel h4 {
  color: #000;
  font-weight: 600;
  letter-spacing: 0.3px;
  margin-bottom: 20px;
  border-bottom: 2px solid #000;
  padding-bottom: 6px;
}

/* ===== LIST ITEM (Panel Kiri) ===== */
.log-item {
  border-bottom: 1px solid #eee;
  padding: 12px 0;
}
.log-item a {
  text-decoration: none;
  color: #000;
  font-weight: 500;
  transition: 0.2s ease;
}
.log-item a:hover {
  color: #111;
  text-decoration: underline;
}

/* ===== BADGE STATUS ===== */
.badge {
  background: #000 !important;
  color: #fff !important;
  font-size: 0.75rem;
  padding: 6px 12px;
  border-radius: 25px;
  font-weight: 500;
}

/* ===== BUTTONS ===== */
button, .btn {
  background: #fff;
  color: #000;
  border: 1px solid #000;
  border-radius: 6px;
  padding: 7px 14px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.25s ease;
}
button:hover, .btn:hover {
  background: #000;
  color: #fff;
}

.btn-save {
  background: #000;
  color: #fff;
  border-radius: 25px;
  font-size: 13px;
  font-weight: 500;
  padding: 8px 22px;
  border: none;
}
.btn-save:hover {
  background: #222;
}

/* ===== INPUTS, TEXTAREA ===== */
textarea.form-control,
select.form-select,
input[type="text"],
input[type="file"] {
  background: #f9f9f9;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 13px;
  padding: 6px 10px;
  width: 100%;
  transition: 0.2s ease;
}
textarea:focus,
select:focus,
input:focus {
  outline: none;
  border-color: #000;
  background: #fff;
}

/* ===== LINKS ===== */
a {
  color: #000;
  text-decoration: none;
  transition: 0.2s ease;
}
a:hover {
  text-decoration: underline;
}

/* ===== TABLE ===== */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  background: #fff;
  font-size: 13px;
}
th, td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center;
  vertical-align: middle;
}
thead {
  background: #000;
  color: #fff;
}
tbody tr:hover {
  background: #f2f2f2;
}

/* ===== PAGINATION ===== */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
  margin-top: 20px;
}
.pagination a, .pagination span {
  padding: 6px 12px;
  border: 1px solid #000;
  border-radius: 5px;
  text-decoration: none;
  color: #000;
  background: #fff;
  font-size: 13px;
}
.pagination a:hover {
  background: #000;
  color: #fff;
}
.pagination a.active {
  background: #000;
  color: #fff;
  font-weight: bold;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
  .container-logbook {
    flex-direction: column;
  }
  .left-panel, .right-panel {
    width: 100%;
  }
}
</style>


</head>
<body>
<div class="container-logbook">
  <!-- LEFT PANEL -->
  <div class="left-panel">
 <h5 class="mb-3">📘 Aktiviti Mingguan Pelajar</h5>

    <?php while ($row = $result->fetch_assoc()): 
        $badge_class = 'secondary';
        if ($row['supervisor_status'] == 'Approved') $badge_class = 'success';
        elseif ($row['supervisor_status'] == 'Revision') $badge_class = 'warning';
        elseif ($row['supervisor_status'] == 'Pending') $badge_class = 'secondary';
    ?>
      <div class="log-item d-flex justify-content-between align-items-center">
        <div>
          <a href="?id=<?= $permohonan_id ?>&selected_id=<?= $row['id'] ?>">Minggu <?= htmlspecialchars($row['week_no']) ?></a><br>
          <small><?= htmlspecialchars($row['start_date']) ?> → <?= htmlspecialchars($row['end_date']) ?></small><br>
          <span class="badge bg-<?= $badge_class ?>"><?= htmlspecialchars($row['supervisor_status']) ?></span>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <?php if ($selected_log): ?>
      <h4 style="color:#2e5aac; border-bottom:3px solid #cbd5f0; padding-bottom:5px; margin-bottom:20px;">
        Minggu <?= htmlspecialchars($selected_log['week_no']) ?>
      </h4>

      <p><strong>Tarikh:</strong> <?= htmlspecialchars($selected_log['start_date']) ?> - <?= htmlspecialchars($selected_log['end_date']) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars($selected_log['supervisor_status']) ?></p>

      <div class="mb-3">
        <label class="form-label" style="color:#2e5aac;">Nota / Isu Dibincangkan:</label>
        <div class="p-2 border rounded bg-light"><?= nl2br(htmlspecialchars($selected_log['log_content'])) ?: '<i>Tiada catatan</i>' ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label" style="color:#2e5aac;">Tindakan yang Perlu Diambil:</label>
        <div class="p-2 border rounded bg-light"><?= nl2br(htmlspecialchars($selected_log['action_taken'] ?? '')) ?: '<i>Tiada tindakan direkod</i>' ?></div>
      </div>

   <?php if (!empty($selected_log['attachment'])): 
  $files = json_decode($selected_log['attachment'], true);
  if (is_array($files) && count($files) > 0): ?>
    <div class="mb-3">
      <label class="form-label" style="color:#2e5aac;">Lampiran:</label>
      <?php foreach ($files as $file): ?>
        <a href="uploads/logbook/<?= htmlspecialchars($file) ?>" target="_blank" class="d-block text-primary">
          📎 <?= htmlspecialchars($file) ?>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif;
endif; ?>


      <hr>
      <form method="POST">
        <input type="hidden" name="log_id" value="<?= $selected_log['id'] ?>">
        <div class="mb-2">
          <label class="form-label">Komen Penyelia</label>
          <textarea class="form-control" name="supervisor_comment" rows="4"><?= htmlspecialchars($selected_log['supervisor_comment']) ?></textarea>
        </div>
        <div class="mb-2">
          <label class="form-label">Status Penyelia</label>
          <select class="form-select" name="supervisor_status">
            <option value="Pending" <?= $selected_log['supervisor_status']=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Approved" <?= $selected_log['supervisor_status']=='Approved'?'selected':'' ?>>Approved</option>
            <option value="Revision" <?= $selected_log['supervisor_status']=='Revision'?'selected':'' ?>>Revision</option>
          </select>
        </div>
        <button type="submit" name="save_supervisor" class="btn btn-save">💾 Simpan</button>
      </form>
    <?php else: ?>
      <p class="text-muted">Pilih minggu dari panel kiri untuk lihat maklumat logbook pelajar.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
