<?php
session_start();
require_once 'config.php';

// ✅ Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: student_login.php');
    exit();
}

$id_pelajar = $_SESSION['user_id'];

/* Ensure permohonan exists (same as your original logic) */
$stmt_check = $conn->prepare("SELECT id FROM permohonan WHERE id_pelajar = ? LIMIT 1");
if ($stmt_check === false) {
    die("Prepare failed (check permohonan): " . $conn->error);
}
$stmt_check->bind_param("i", $id_pelajar);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check && $res_check->num_rows > 0) {
    $row = $res_check->fetch_assoc();
    $permohonan_id = $row['id'];
    $stmt_check->close();
} else {
    $stmt_insert = $conn->prepare("
        INSERT INTO permohonan (id_pelajar, tajuk, status, tarikh_hantar)
        VALUES (?, ?, ?, NOW())
    ");
    if ($stmt_insert === false) {
        die("Prepare failed (insert permohonan): " . $conn->error);
    }
    $dummy_title = "Dummy Project";
    $dummy_status = "Pending";
    $stmt_insert->bind_param("iss", $id_pelajar, $dummy_title, $dummy_status);
    $stmt_insert->execute();
    $permohonan_id = $conn->insert_id;
    $stmt_insert->close();
    $stmt_check->close();
}

/* ========== ADD WEEKLY LOG (include progress default 0) ========== */
if (isset($_POST['add_week'])) {
    $week_no = intval($_POST['week_no']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("
        INSERT INTO logbook (permohonan_id, week_no, start_date, end_date, supervisor_status, progress, created_at)
        VALUES (?, ?, ?, ?, 'Pending', 0, NOW())
    ");
    if ($stmt === false) {
        die("Prepare failed (insert logbook): " . $conn->error);
    }
    $stmt->bind_param("iiss", $permohonan_id, $week_no, $start_date, $end_date);
    $stmt->execute();
    $stmt->close();

    header("Location: student_logbook.php");
    exit();
}

/* ========== DELETE LOG ========== */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM logbook WHERE id = ? AND permohonan_id = ?");
    if ($stmt === false) {
        die("Prepare failed (delete logbook): " . $conn->error);
    }
    $stmt->bind_param("ii", $id, $permohonan_id);
    $stmt->execute();
    $stmt->close();

    header("Location: student_logbook.php");
    exit();
}

/* ========== SAVE/UPDATE LOG DETAILS (include progress) ========== */
if (isset($_POST['save_detail'])) {
    $id = intval($_POST['log_id']);
    $log_content = $_POST['log_content'] ?? '';
    $action_taken = $_POST['action_taken'] ?? '';
    $progress = isset($_POST['progress']) ? intval($_POST['progress']) : 0;
    if ($progress < 0) $progress = 0;
    if ($progress > 100) $progress = 100;

    $attachments = [];

    if (!empty($_FILES['attachments']['name'][0])) {
        $target_dir = __DIR__ . "/uploads/logbook/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        foreach ($_FILES['attachments']['name'] as $key => $name) {
            if (empty($name)) continue;
            $file_tmp = $_FILES['attachments']['tmp_name'][$key];
            $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', basename($name));
            $target_path = $target_dir . $file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $attachments[] = $file_name;
            }
        }
    }

    if (!empty($attachments)) {
        // append or replace? We'll store the new set (could be changed to merge)
        $attachments_json = json_encode($attachments);
        $stmt = $conn->prepare("UPDATE logbook SET log_content=?, action_taken=?, progress=?, attachment=?, updated_at=NOW() WHERE id=? AND permohonan_id=?");
        if ($stmt === false) {
            die("Prepare failed (update logbook with attach): " . $conn->error);
        }
        $stmt->bind_param("ssisii", $log_content, $action_taken, $progress, $attachments_json, $id, $permohonan_id);
    } else {
        $stmt = $conn->prepare("UPDATE logbook SET log_content=?, action_taken=?, progress=?, updated_at=NOW() WHERE id=? AND permohonan_id=?");
        if ($stmt === false) {
            die("Prepare failed (update logbook no attach): " . $conn->error);
        }
        $stmt->bind_param("ssiii", $log_content, $action_taken, $progress, $id, $permohonan_id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: student_logbook.php?selected_id=$id");
    exit();
}

/* ========== GET ALL LOGS ========== */
$stmt = $conn->prepare("SELECT * FROM logbook WHERE permohonan_id=? ORDER BY week_no ASC");
if ($stmt === false) {
    die("Prepare failed (select logs): " . $conn->error);
}
$stmt->bind_param("i", $permohonan_id);
$stmt->execute();
$result = $stmt->get_result();

/* ========== GET SELECTED LOG ========== */
$selected_id = isset($_GET['selected_id']) ? intval($_GET['selected_id']) : null;
$selected_log = null;
if ($selected_id) {
    $stmt2 = $conn->prepare("SELECT * FROM logbook WHERE id=? AND permohonan_id=? LIMIT 1");
    if ($stmt2 === false) {
        die("Prepare failed (select selected log): " . $conn->error);
    }
    $stmt2->bind_param("ii", $selected_id, $permohonan_id);
    $stmt2->execute();
    $selected_log = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Logbook Mingguan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ==== THEME SWITCH (4 themes included) ==== */
/* Base resets & shared styles */
:root {
  --bg: #f4f4f4;
  --card-bg: #ffffff;
  --accent: #000000;
  --muted: #666666;
  --shadow: rgba(0,0,0,0.06);
  --border: #e0e0e0;
  --progress-bg: #eef2f7;
  --progress-fill-start: #000;
  --progress-fill-end: #333;
  --text: #000;
}



/* Theme B: Soft Grey Card UI */
body.theme-b {
  --bg: #f1f3f6;
  --card-bg: #ffffff;
  --accent: #222;
  --muted: #666;
  --shadow: rgba(30,41,90,0.06);
  --border: #dfe7f2;
  --progress-bg: #e9eef9;
  --progress-fill-start: #1f4c9a;
  --progress-fill-end: #6c88d0;
  --text: #0b1220;
}

/* Global base */
body {
  margin: 0;
  padding: 24px;
  background: var(--bg);
  font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: var(--text);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  zoom: 0.80;
  
}

/* Layout */
.container-logbook {
  display: flex;
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

/* Left panel (list) */
.left-panel {
  width: 34%;
  background: var(--card-bg);
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 6px 18px var(--shadow);
  border-left: 6px solid var(--accent);
  min-height: 400px;
}
.left-panel .top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.left-panel h5 { margin: 0; font-weight: 600; color: var(--accent); }

/* theme selector */
.theme-select { font-size: 0.9rem; }

/* add button */
.btn-add {
  background: var(--accent);
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
}
.btn-add:hover { opacity: 0.95; transform: translateY(-1px); }

/* log item */
.log-list { max-height: 580px; overflow:auto; padding-right: 6px; }
.log-item {
  display:flex; justify-content:space-between; align-items:flex-start;
  padding: 12px 6px;
  border-bottom: 1px solid var(--border);
}
.log-item .meta { max-width: 78%; }
.log-item a.week-link { color: var(--text); font-weight:600; text-decoration:none; display:inline-block; margin-bottom:4px; }
.log-item small { color: var(--muted); display:block; margin-bottom:6px; }
.log-item .badge {
  background: var(--accent);
  color: #fff;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 0.75rem;
  font-weight:600;
}
.log-item .delete-icon { color: #c23; text-decoration:none; font-size:18px; }

/* small progress bar in list */
.mini-progress {
  width: 100%;
  height: 8px;
  background: var(--progress-bg);
  border-radius: 8px;
  overflow: hidden;
  margin-top:8px;
}
.mini-progress .fill {
  height:100%;
  background: linear-gradient(90deg, var(--progress-fill-start), var(--progress-fill-end));
  width:0%;
  transition: width .6s ease;
}

/* Right panel (detail) */
.right-panel {
  flex:1;
  background: var(--card-bg);
  border-radius: 12px;
  padding: 26px;
  box-shadow: 0 6px 18px var(--shadow);
  border-left: 6px solid rgba(0,0,0,0.04);
  min-height: 400px;
}

.right-panel h4 { margin:0 0 12px 0; color: var(--accent); font-weight:700; border-bottom: 2px solid var(--border); padding-bottom:10px; }

/* Labels & inputs */
.form-label { font-weight: 700; color: var(--accent); font-size:0.95rem; display:block; margin-bottom:6px; }
textarea.form-control, input[type="date"], input[type="number"], input[type="file"] {
  width:100%; padding: 12px; border-radius:10px; border:1px solid var(--border); background: #fff; color:var(--text);
  box-sizing: border-box; font-size:0.95rem;
}
textarea.form-control:focus, input[type="number"]:focus, input[type="date"]:focus {
  outline: none; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  border-color: var(--accent);
}

/* big progress bar */
.progress-wrapper { margin:12px 0 18px 0; }
.progress-label { font-weight:700; color:var(--accent); margin-bottom:6px; }
.progress-bar-custom { width:100%; height:14px; background:var(--progress-bg); border-radius:999px; overflow:hidden; }
.progress-fill { height:100%; width:0%; background: linear-gradient(90deg, var(--progress-fill-start), var(--progress-fill-end)); transition: width .6s ease; }

/* buttons */
.btn-save { background:var(--accent); color:#fff; border:none; padding:10px 18px; border-radius:999px; font-weight:700; }
.btn-save:hover { transform: translateY(-2px); }

/* attachments */
.attach-link { display:block; margin-top:8px; color:var(--accent); text-decoration:none; font-size:0.92rem; }

/* small helpers */
.text-muted-italic { color: var(--muted); font-style: italic; }

/* Modal tweaks */
.modal-content { border-radius:12px; }
.modal-header { border-bottom: none; }

/* responsive */
@media (max-width: 900px) {
  .container-logbook { flex-direction: column; padding: 16px; }
  .left-panel { width: 100%; }
  .right-panel { width: 100%; }
}

.badge {
  background: var(--accent) !important;
  color: #fff !important;
  border-radius: 999px;
  padding: 6px 12px;
  font-weight: 600;
  font-size: 0.75rem;
}

.badge-pending {
  background: #f4b400 !important; /* kuning */
  color: #fff !important;
}

.badge-approved {
  background: #34a853 !important; /* hijau */
  color: #fff !important;
}

.badge-rejected {
  background: #ea4335 !important; /* merah */
  color: #fff !important;
}

</style>
</head>
<body class="theme-b">

</div>

<div class="container-logbook">
  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="top-row">
      <h5>AKTIVITI MINGGUAN</h5>
      <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">+ TAMBAH</button>
    </div>

    <div class="log-list">
      <?php while ($row = $result->fetch_assoc()): 
        $progress = intval($row['progress'] ?? 0);
      ?>
        <div class="log-item">
          <div class="meta">
            <a class="week-link" href="?selected_id=<?= $row['id'] ?>"> MINGGU <?= htmlspecialchars($row['week_no']) ?></a>
            <small><?= htmlspecialchars($row['start_date']) ?> → <?= htmlspecialchars($row['end_date']) ?></small>
            <div class="mini-progress" aria-hidden="true">
              <div class="fill" style="width: <?= $progress ?>%;"></div>
            </div>
          </div>
          <div style="text-align:right;">
            <div style="margin-bottom:6px;">
              <span class="badge badge-<?= strtolower($row['supervisor_status']) ?>">
    <?= htmlspecialchars($row['supervisor_status']) ?>
</span>

            </div>
            <a class="delete-icon" href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this week?')">🗑️</a>
          </div>
        </div>
      <?php endwhile; ?>
      <?php if ($result->num_rows == 0): ?>
        <p class="text-muted-italic">Tiada rekod. Tekan “+ Add” untuk tambah minggu.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <?php if ($selected_log): 
      $files = !empty($selected_log['attachment']) ? json_decode($selected_log['attachment'], true) : [];
      $selected_progress = intval($selected_log['progress'] ?? 0);
    ?>
      <h4>AKTIVITI MINGGUAN — MINGGU <?= htmlspecialchars(str_pad($selected_log['week_no'], 2, '0', STR_PAD_LEFT)) ?></h4>

      <div style="display:flex; gap:18px; margin-bottom:12px; align-items:center;">
        <div>
          <div class="form-label">TARIKH</div>
          <div><?= htmlspecialchars($selected_log['start_date']) ?> — <?= htmlspecialchars($selected_log['end_date']) ?></div>
        </div>
        <div>
          <div class="form-label">STATUS</div>
          <div><span class="badge badge-<?= strtolower($selected_log['supervisor_status']) ?>">
    <?= htmlspecialchars($selected_log['supervisor_status']) ?>
</span>
</div>
        </div>
      </div>

      <!-- Progress big -->
      <div class="progress-wrapper">
        <div class="progress-label">KEMAJUAN <?= $selected_progress ?>%</div>
        <div class="progress-bar-custom" role="progressbar" aria-valuenow="<?= $selected_progress ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-fill" style="width: <?= $selected_progress ?>%;"></div>
        </div>
      </div>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="log_id" value="<?= $selected_log['id'] ?>">
        
        <div class="mb-3">
          <label class="form-label">ISU YANG DIBINCANGKAN</label>
          <textarea name="log_content" class="form-control" rows="5"><?= htmlspecialchars($selected_log['log_content']) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">TINDAKAN YANG PERLU DIAMBIL</label>
          <textarea name="action_taken" class="form-control" rows="3"><?= htmlspecialchars($selected_log['action_taken'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">KEMAJUAN (%)</label>
          <input type="number" name="progress" class="form-control" min="0" max="100" value="<?= $selected_progress ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">LAMPIRAN (PDF)</label>
          <input type="file" name="attachments[]" accept="application/pdf" class="form-control" multiple>
          <?php if (!empty($files) && is_array($files)): foreach ($files as $f): ?>
            <a class="attach-link" href="uploads/logbook/<?= htmlspecialchars($f) ?>" target="_blank">📎 <?= htmlspecialchars($f) ?></a>
          <?php endforeach; endif; ?>
        </div>

        <button type="submit" name="save_detail" class="btn-save">SIMPAN</button>
      </form>

      <hr style="margin:18px 0; border-color:var(--border);">

      <h6 style="color:var(--accent); margin-bottom:8px;">KOMEN PENYELIA</h6>
      <?php if (!empty($selected_log['supervisor_comment'])): ?>
        <div style="background:var(--progress-bg); padding:12px; border-radius:8px;"><?= nl2br(htmlspecialchars($selected_log['supervisor_comment'])) ?></div>
      <?php else: ?>
        <p class="text-muted-italic">No comments yet.</p>
      <?php endif; ?>

      <div style="margin-top:12px;">
        <div class="form-label">STATUS PENYELIA</div>
<div><span class="badge badge-<?= strtolower($selected_log['supervisor_status']) ?>">
    <?= htmlspecialchars($selected_log['supervisor_status']) ?>
</span></div>


      </div>

    <?php else: ?>
      <p class="text-muted-italic">Pilih minggu di kiri untuk lihat atau edit butiran.</p>
    <?php endif; ?>
  </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">TAMBAH BUKU LOG</h5>
      </div>
      <div class="modal-body" style="padding:18px;">
        <div class="mb-2">
          <label class="form-label">MINGGU KE</label>
          <input type="number" name="week_no" class="form-control" required min="1">
        </div>
        <div class="mb-2">
          <label class="form-label">MULA TARIKH</label>
          <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">AKHIR TARIKH</label>
          <input type="date" name="end_date" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_week" class="btn-save">TAMBAH</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

/* smooth show of progress fills after load for animation */
document.addEventListener('DOMContentLoaded', () => {
  // animate all mini fills (already inline style width set by PHP)
  document.querySelectorAll('.mini-progress .fill, .progress-fill').forEach(el => {
    // trigger reflow so transition animates
    const w = el.style.width;
    el.style.width = '0%';
    setTimeout(()=> el.style.width = w, 50);
  });
});
</script>
</body>
</html>
