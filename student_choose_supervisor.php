<?php
session_start();
require_once 'config.php';

// ✅ Semak login pelajar
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='pelajar'){
    header('Location: student_login.php');
    exit();
}

$id_pelajar = $_SESSION['user_id'];

// ✅ Terima id_penyelia dari POST/GET
$id_penyelia = $_POST['id_penyelia'] ?? $_GET['id_penyelia'] ?? null;
$id_penyelia = $id_penyelia ? intval($id_penyelia) : null;

if(!$id_penyelia){
    echo "<div style='text-align:center; padding:50px; font-family:Segoe UI;'>
            <h3 style='color:#c0392b;'>⚠️ Tiada penyelia dipilih!</h3>
            <p>Sila kembali ke senarai penyelia dan pilih semula.</p>
            <a href='student_list_supervisors.php' class='btn btn-primary mt-3'>Kembali ke senarai penyelia</a>
          </div>";
    exit();
}

// ✅ Dapatkan maklumat pelajar
$stmt = $conn->prepare("SELECT nama_pelajar, no_matrik FROM pelajar WHERE id_pelajar=?");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$pelajar = $stmt->get_result()->fetch_assoc();

// ✅ Dapatkan maklumat penyelia
$stmt2 = $conn->prepare("SELECT nama_penyelia, course FROM penyelia WHERE id_penyelia=?");
$stmt2->bind_param("i", $id_penyelia);
$stmt2->execute();
$penyelia = $stmt2->get_result()->fetch_assoc();

// ✅ Ambil tajuk sedia ada
$stmt3 = $conn->prepare("
    SELECT t.tajuk, t.abstrak, p.nama_pelajar
    FROM tajuk_penyelia t
    LEFT JOIN permohonan m 
        ON m.tajuk_dipilih = t.tajuk 
        AND m.id_penyelia = t.id_penyelia 
        AND (m.status = 'diluluskan' OR m.status = 'diterima')
    LEFT JOIN pelajar p ON m.id_pelajar = p.id_pelajar
    WHERE t.id_penyelia = ?
");
$stmt3->bind_param("i", $id_penyelia);
$stmt3->execute();
$tajuks = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

// ✅ Proses permohonan
if(isset($_POST['mohon'])){
    $pilihan = $_POST['pilihan'] ?? '';
    $tajuk_dipilih = '';
    $tajuk1 = $abstrak1 = $tajuk2 = $abstrak2 = $tajuk3 = $abstrak3 = '';

    if($pilihan === 'cadangan'){
        $tajuk_array = $_POST['tajuk_cadangan'] ?? [];
        $abstrak_array = $_POST['abstrak_cadangan'] ?? [];
        $tajuk1 = trim($tajuk_array[0] ?? '');
        $abstrak1 = trim($abstrak_array[0] ?? '');
        $tajuk2 = trim($tajuk_array[1] ?? '');
        $abstrak2 = trim($abstrak_array[1] ?? '');
        $tajuk3 = trim($tajuk_array[2] ?? '');
        $abstrak3 = trim($abstrak_array[2] ?? '');
        $tajuk_dipilih = $tajuk1 ?: $tajuk2 ?: $tajuk3;
    } elseif($pilihan === 'sedia_ada'){
        $tajuk_dipilih = $_POST['tajuk_dipilih'] ?? '';
        $tajuk1 = $tajuk_dipilih;
    }

    if(empty($tajuk_dipilih)){
        echo "<script>alert('⚠️ Sila pilih atau cadangkan sekurang-kurangnya satu tajuk.'); window.location='student_choose_supervisor.php?id_penyelia=$id_penyelia';</script>";
        exit();
    }

    $check = $conn->prepare("
        SELECT id, status 
        FROM permohonan 
        WHERE id_pelajar=? AND id_penyelia=? 
        ORDER BY tarikh_hantar DESC LIMIT 1
    ");
    $check->bind_param("ii", $id_pelajar, $id_penyelia);
    $check->execute();
    $latest = $check->get_result()->fetch_assoc();

    if(!$latest || strtolower($latest['status']) === 'ditolak'){
        $stmt4 = $conn->prepare("
            INSERT INTO permohonan 
            (id_pelajar, id_penyelia, tajuk1, abstrak1, tajuk2, abstrak2, tajuk3, abstrak3, tajuk_dipilih, status, tarikh_hantar)
            VALUES (?,?,?,?,?,?,?,?,?,'dalam semakan',NOW())
        ");
        $stmt4->bind_param("iisssssss", 
            $id_pelajar, $id_penyelia, 
            $tajuk1, $abstrak1, $tajuk2, $abstrak2, $tajuk3, $abstrak3, $tajuk_dipilih
        );
        $stmt4->execute();
    } else {
        echo "<script>alert('Anda sudah ada permohonan aktif dengan penyelia ini.'); window.location='student_view_submission.php';</script>";
        exit();
    }
}

$stmt5 = $conn->prepare("SELECT * FROM permohonan WHERE id_pelajar=? ORDER BY tarikh_hantar DESC LIMIT 1");
$stmt5->bind_param("i",$id_pelajar);
$stmt5->execute();
$submission = $stmt5->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Permohonan Tajuk Projek | UTHM</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* 🎓 GAYA RASMI UNIVERSITI (CLEAN BLACK & WHITE) 🎓 */
body {
  font-family: 'Segoe UI', sans-serif;
  background: #ffffff;
  color: #000;
  font-size: 14px;
  padding: 40px;
  zoom: 90%;
}

/* ===== BOX / SECTION ===== */
.box {
  background: #fff;
  border: 1px solid #000;
  border-radius: 0;
  padding: 25px 30px;
  margin-bottom: 25px;
}

/* ===== HEADING ===== */
h3 {
  color: #000;
  font-weight: 600;
  font-size: 16px;
  text-transform: uppercase;
  margin-bottom: 15px;
  border-bottom: 1px solid #000;
  padding-bottom: 5px;
}

/* ===== TABLE ===== */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  margin-top: 10px;
}

th, td {
  border: 1px solid #000;
  padding: 8px 10px;
  text-align: left;
}

th {
  background: #f2f2f2;
  font-weight: 600;
}

/* ===== FORM ELEMENTS ===== */
input, textarea, select {
  border: 1px solid #000 !important;
  border-radius: 0 !important;
  font-size: 13px !important;
  color: #000 !important;
  background: #fff !important;
}

input:focus, textarea:focus {
  outline: none !important;
  border-color: #000 !important;
  box-shadow: none !important;
}

/* ===== BUTTONS ===== */
.btn-primary {
  background: #000 !important;
  color: #fff !important;
  border-radius: 0;
  border: 1px solid #000;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
}
.btn-primary:hover {
  background: #333 !important;
  border-color: #333 !important;
}

/* ===== TAB BUTTONS ===== */
.tab-buttons {
  display: flex;
  gap: 5px;
  margin-bottom: 15px;
}
.tab-btn {
  background: #fff;
  border: 1px solid #000;
  color: #000;
  padding: 8px 14px;
  font-weight: 600;
  border-radius: 0;
}
.tab-btn.active {
  background: #000;
  color: #fff;
}

/* ===== CARD CONTENT ===== */
.card-content {
  border: 1px solid #000;
  background: #fff;
  padding: 15px;
  margin-bottom: 10px;
}
.card-radio input[type="radio"] {
  display: none;
}
.card-radio input[type="radio"]:checked + .card-content {
  border: 2px solid #000;
  background: #f8f8f8;
}

/* ===== FOOTER ===== */
.uthm-footer {
  text-align: center;
  color: #000;
  font-size: 12px;
  border-top: 1px solid #000;
  padding-top: 10px;
  margin-top: 40px;
}


</style>
</head>
<body>
<div class="container">

  <div class="box">
    <h3>Maklumat Pelajar & Penyelia Dipilih</h3>
   <table class="table" style="border-collapse: collapse; border: 1px solid #000;">
      <tr><th>Nama Pelajar</th><td><?= htmlspecialchars($pelajar['nama_pelajar'] ?? '-') ?></td></tr>
      <tr><th>No Matriks</th><td><?= htmlspecialchars($pelajar['no_matrik'] ?? '-') ?></td></tr>
      <tr><th>Penyelia Dipilih</th><td><?= htmlspecialchars($penyelia['nama_penyelia'] ?? '-') ?></td></tr>
    </table>
  </div>

  <div class="box">
    <h3>Pilih Cara Memohon Tajuk</h3>
    <div class="tab-buttons">
      <button type="button" class="tab-btn active" id="tabCadangan">Cadangkan Tajuk</button>
      <button type="button" class="tab-btn" id="tabSedia">Tajuk Sedia Ada</button>
    </div>

    <form method="post">
      <input type="hidden" name="id_penyelia" value="<?= htmlspecialchars($id_penyelia) ?>">
      <input type="hidden" name="pilihan" id="pilihan" value="cadangan">

      <div id="cadanganForm" class="tab-content active">
        <?php for($i=1;$i<=3;$i++): ?>
          <div class="mb-3">
            <label class="form-label">Tajuk Cadangan <?= $i ?>:</label>
            <input type="text" name="tajuk_cadangan[]" class="form-control" placeholder="Contoh: Sistem Pengurusan Pelajar Berasaskan Web">
          </div>
          <div class="mb-4">
            <label class="form-label">Abstrak <?= $i ?>:</label>
            <textarea name="abstrak_cadangan[]" class="form-control" rows="3" placeholder="Terangkan idea projek anda secara ringkas..."></textarea>
          </div>
        <?php endfor; ?>
      </div>

      <div id="sediaForm" class="tab-content" style="display:none;">
        <?php if(empty($tajuks)): ?>
          <p class="text-muted">Penyelia ini belum mempunyai tajuk yang tersedia.</p>
        <?php else: ?>
          <?php foreach($tajuks as $t): ?>
            <?php if(empty($t['nama_pelajar'])): ?>
              <label class="card-radio">
                <input type="radio" name="tajuk_dipilih" value="<?= htmlspecialchars($t['tajuk']) ?>">
                <div class="card-content">
                  <h5><?= htmlspecialchars($t['tajuk']) ?></h5>
                  <p><?= htmlspecialchars($t['abstrak']) ?></p>
                </div>
              </label>
            <?php else: ?>
              <div class="card-content" style="opacity:0.6; background:#f8f9fa; border-color:#ccc;">
                <h5><?= htmlspecialchars($t['tajuk']) ?> <span class="badge bg-danger ms-2">Sudah Diambil</span></h5>
                <p><?= htmlspecialchars($t['abstrak']) ?></p>
                <p class="text-muted fst-italic">Telah diambil oleh <?= htmlspecialchars($t['nama_pelajar']) ?></p>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="text-center mt-4">
        <button type="submit" name="mohon" class="btn btn-primary px-4">Hantar Permohonan</button>
      </div>
    </form>
  </div>

  <?php if($submission && strtolower($submission['status'])==='diluluskan'): ?>
  <div class="box">
    <h3>📎 Muat Naik Dokumen Selepas Kelulusan</h3>
    <form action="upload_forms.php" method="post" enctype="multipart/form-data" class="row g-3 text-center">
      <div class="col-md-4">
        <h5>Form A</h5>
        <input type="file" name="formA" accept="application/pdf" class="form-control" required>
      </div>
      <div class="col-md-4">
        <h5>Form B</h5>
        <input type="file" name="formB" accept="application/pdf" class="form-control" required>
      </div>
      <div class="col-md-4">
        <h5>Form C</h5>
        <input type="file" name="formC" accept="application/pdf" class="form-control" required>
      </div>
      <div class="col-12 mt-3">
        <button type="submit" class="btn btn-primary">Hantar Semua Dokumen</button>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>

<script>
const tabCadangan = document.getElementById('tabCadangan');
const tabSedia = document.getElementById('tabSedia');
const cadanganForm = document.getElementById('cadanganForm');
const sediaForm = document.getElementById('sediaForm');
const pilihanInput = document.getElementById('pilihan');

tabCadangan.addEventListener('click', () => {
  tabCadangan.classList.add('active');
  tabSedia.classList.remove('active');
  cadanganForm.style.display = 'block';
  sediaForm.style.display = 'none';
  pilihanInput.value = 'cadangan';
});
tabSedia.addEventListener('click', () => {
  tabSedia.classList.add('active');
  tabCadangan.classList.remove('active');
  sediaForm.style.display = 'block';
  cadanganForm.style.display = 'none';
  pilihanInput.value = 'sedia_ada';
});
</script>
</body>
</html>
