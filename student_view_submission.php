<?php
session_start();
require_once 'config.php';

// ✅ Semak login pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: student_login.php');
    exit();
}

$id_pelajar = $_SESSION['user_id'];

// ✅ Maklumat pelajar
$stmt = $conn->prepare("SELECT nama_pelajar, no_matrik FROM pelajar WHERE id_pelajar = ?");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$resultPelajar = $stmt->get_result();
$pelajar = $resultPelajar->fetch_assoc();

// ✅ Semua permohonan
$stmt = $conn->prepare("
    SELECT p.*, py.nama_penyelia 
    FROM permohonan p
    LEFT JOIN penyelia py ON p.id_penyelia = py.id_penyelia
    WHERE p.id_pelajar = ?
    ORDER BY p.tarikh_hantar DESC
");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$resultPermohonan = $stmt->get_result();

// ✅ Permohonan terkini (untuk tajuk cadangan)
$stmt2 = $conn->prepare("
    SELECT * FROM permohonan 
    WHERE id_pelajar = ? 
    ORDER BY tarikh_hantar DESC 
    LIMIT 1
");
$stmt2->bind_param("i", $id_pelajar);
$stmt2->execute();
$submission = $stmt2->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    

<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Senarai Permohonan Pelajar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ===============================
   GLOBAL COLORS & BODY
=============================== */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #ffffff !important;
    color: #000000 !important;
    padding: 40px;
    font-size: 15px;
}
/* Pastikan alert tidak duduk di belakang sidebar */
.alert {
    margin-left: 270px !important;  /* matching width sidebar */
    margin-top: 10px;
    width: calc(100% - 300px); /* elak overflow */
    position: relative;
    z-index: 9999;
}

/* Box styling */
.box {
    background: #ffffff !important;
    border: 1px solid #d6d6d6 !important;
    border-radius: 10px;
    padding: 28px 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.box h5 {
    font-weight: 700;
    color: #000000 !important;   /* Semua tulisan hitam */
    border-left: 5px solid #000000 !important;
    padding-left: 12px;
    margin-bottom: 18px;
}

/* ===============================
   TABLE
=============================== */
table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff !important;
    color: #000000 !important;
}

th {
    background: #ffffff !important;
    color: #000000 !important;
    padding: 12px 10px;
    border-bottom: 1px solid #000;
    font-weight: 700;
}

td {
    background: #ffffff !important;
    color: #000000 !important;
    border-bottom: 1px solid #d6d6d6;
    padding: 10px 12px;
}

tr:hover td {
    background: #f5f5f5 !important;
}

/* ===============================
   FORM INPUTS
=============================== */
input, textarea, select {
    border: 1px solid #000000 !important;
    border-radius: 6px !important;
    background: #ffffff !important;
    color: #000000 !important;
    padding: 8px 10px;
}

input:focus, textarea:focus, select:focus {
    border-color: #000000 !important;
    box-shadow: 0 0 5px rgba(0,0,0,0.2) !important;
}

/* ===============================
   BUTTON
=============================== */
.btn-primary {
    background: #1a3fa3 !important;
    color: #ffffff !important;
    border-radius: 6px;
    font-weight: 600;
}

.btn-primary:hover {
    background: #0f2f7d !important;
}

/* ===============================
   STATUS COLORS
=============================== */

/* STATUS LULUS = HIJAU SAHAJA */
.status-diluluskan {
    color: #0a7d00 !important; 
    font-weight: 700;
    background: transparent !important;
}

/* STATUS LAIN = HITAM */
.status-ditolak,
.status-dalam-semakan {
    color: #000000 !important;
    font-weight: 700;
    background: transparent !important;
}

/* ===============================
   BLUE BOX (ABSTRACT BOX)
=============================== */
.abstract-box {
    background: #ffffff !important;
    border-left: 5px solid #000000 !important;
    padding: 15px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    color: #000000 !important;
}

/* ===============================
   UPLOAD CARDS
=============================== */

.bg-light {
    background: #ffffff !important;
    border: 1px solid #000000 !important;
    border-radius: 10px !important;
    color: #000000 !important;
}

.bg-light h5,
.bg-light p {
    color: #000000 !important;
}
table.table-striped tbody tr:hover td {
    background-color: #ffffff !important;
}

/* Buang warna kelabu daripada bootstrap table */
.table-striped > tbody > tr:nth-of-type(odd) > * {
    background-color: #ffffff !important;
}

.table-striped > tbody > tr:nth-of-type(even) > * {
    background-color: #ffffff !important;
}

.table-light {
    background-color: #ffffff !important;
    color: #000 !important;
}
table {
    border-collapse: collapse;
}

table, table th, table td {
    border: none !important;
}

table tr td {
    padding: 10px 0;
    border-bottom: 1px solid #e5e5e5; /* garis bawah lembut */
}
a, a:hover, a:focus, a:active {
    color: inherit !important;
    text-decoration: none !important;
}
/* FORCE EVERYTHING TO BE PURE WHITE */
body,
.box,
table,
.table-light,
.bg-light,
.form-control,
input,
textarea,
select {
    background: #ffffff !important;
}

/* Remove grey background from table rows */
.table-striped > tbody > tr:nth-of-type(odd) > *,
.table-striped > tbody > tr:nth-of-type(even) > *,
.table-light th,
.table-light td,
table tbody tr td {
    background-color: #ffffff !important;
}
/* FINAL FIX — BUATKAN TABLE 100% PUTIH */
.table,
.table th,
.table td,
.table-striped tbody tr,
.table-striped tbody tr td,
.table-light th,
.table-light td {
    background-color: #ffffff !important;
    color: #000000 !important;
}

/* Buang border kelabu dari Bootstrap */
.table > :not(caption) > * > * {
    box-shadow: none !important;
}
table, table th, table td {
    border: none !important;
}

body,
.box,
table,
.table-light,
.bg-light,
.form-control,
input,
textarea,
select {
    background: #ffffff !important;
}
table tr td,
table tbody tr td,
tr:hover td {
    background-color: #ffffff !important;
}

</style>

</head>

<body>

<!-- 🧑 Maklumat Pelajar -->
<div class="box">
  <h5 class="mb-3"></i> MAKLUMAT PELAJAR</h5>
    <table class="table" style="border-collapse: collapse; border: 1px solid #000;">
    <tr><th style="width:30%; text-align:left;">NAMA PELAJAR</th><td style="text-align:left;"><?= htmlspecialchars($pelajar['nama_pelajar']); ?></td></tr>
    <tr><th style="text-align:left;">NO.MATRIK</th><td style="text-align:left;"><?= htmlspecialchars($pelajar['no_matrik']); ?></td></tr>
  </table>
</div>

<!-- 📋 Senarai Permohonan -->
<div class="box">
  <h5 class="mb-3"></i> SENARAI PERMOHONAN</h5>

  <?php if($resultPermohonan->num_rows > 0): ?>
  <table class="table table-striped">
    <thead class="table-light">
      <tr>
        <th>NO</th>
        <th>PENYELIA</th>
        <th>TAJUK CADANGAN</th>
        <th>STATUS</th>
        <th>TARIKH HANTAR</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row=$resultPermohonan->fetch_assoc()):
        $status = strtolower($row['status']);
        $statusClass = match($status) {
          'diluluskan' => 'status-diluluskan',
          'ditolak' => 'status-ditolak',
          'dalam semakan' => 'status-dalam-semakan',
          default => ''
        };
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($row['nama_penyelia'] ?? '-'); ?></td>
        <td>
          <?php 
          if ($status === 'diluluskan') {
              echo htmlspecialchars($row['tajuk_dipilih']);
          } else {
              echo '<span class="text-muted">-</span>';
          }
          ?>
        </td>
        <td><span class="<?= $statusClass ?>"><?= htmlspecialchars($row['status']); ?></span></td>
        <td><?= htmlspecialchars($row['tarikh_hantar']); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p class="text-muted">Tiada permohonan dihantar.</p>
  <?php endif; ?>
</div>

<!-- 💡 Tajuk Cadangan Pelajar -->
<?php
/* ================================
   TAJUK YANG DILULUSKAN + ABSTRAK
================================ */
$stmt_lulus = $conn->prepare("
    SELECT 
        p.tajuk_dipilih,
        tp.abstrak AS abstrak_sv,
        p.abstrak1, p.abstrak2, p.abstrak3,
        p.formA, p.formB, p.formC,
        p.tajuk1, p.tajuk2, p.tajuk3
    FROM permohonan p
    LEFT JOIN tajuk_penyelia tp 
        ON tp.tajuk = p.tajuk_dipilih
    WHERE p.id_pelajar = ? 
      AND LOWER(p.status) = 'diluluskan'
    ORDER BY p.tarikh_hantar DESC
    LIMIT 1
");


$stmt_lulus->bind_param("i", $id_pelajar);
$stmt_lulus->execute();
$data_lulus = $stmt_lulus->get_result()->fetch_assoc();
?>

<?php if($data_lulus): ?>
<div class="box">
    <h5>TAJUK YANG DILULUSKAN</h5>

    <p><strong>TAJUK:</strong><br>
        <?= htmlspecialchars($data_lulus['tajuk_dipilih']); ?>
    </p>

    <p><strong>ABSTRAK:</strong><br>

    <?php
$abstrak = "";

// 1️⃣ Jika abstrak supervisor wujud → guna itu
if (!empty($data_lulus['abstrak_sv'])) {
    $abstrak = $data_lulus['abstrak_sv'];
}
// 2️⃣ Jika tajuk pelajar dipilih → ambil abstrak pelajar
else {
   if ($data_lulus['tajuk_dipilih'] === $data_lulus['tajuk1']) {
    $abstrak = $data_lulus['abstrak1'];
} elseif ($data_lulus['tajuk_dipilih'] === $data_lulus['tajuk2']) {
    $abstrak = $data_lulus['abstrak2'];
} elseif ($data_lulus['tajuk_dipilih'] === $data_lulus['tajuk3']) {
    $abstrak = $data_lulus['abstrak3'];
}

}

// Jika masih kosong → pelajar tidak isi abstrak
if (empty($abstrak)) {
    echo "<em>Tiada abstrak — ini adalah tajuk cadangan pelajar.</em>";
} else {
    echo nl2br(htmlspecialchars($abstrak));
}
?>

    </p>
</div>
<?php endif; ?>



<!-- ✅ Tajuk Diluluskan -->
<div class="box">
  <h5 class="mb-3"></i> TAJUK CADANGAN DILULUSKAN</h5>
  <?php
$stmt_lulus = $conn->prepare("
    SELECT tajuk_dipilih, status, formA, formB, formC
    FROM permohonan 
    WHERE id_pelajar = ? AND LOWER(status) = 'diluluskan'
    ORDER BY tarikh_hantar DESC
    LIMIT 1
");

  $stmt_lulus->bind_param("i", $id_pelajar);
  $stmt_lulus->execute();
  $result_lulus = $stmt_lulus->get_result();

  if($row_lulus = $result_lulus->fetch_assoc()) {
$formA = $row_lulus['formA'] ?? '';
$formB = $row_lulus['formB'] ?? '';
$formC = $row_lulus['formC'] ?? '';


      // ✅ Terus paparkan borang upload tanpa tunjuk tajuk
      ?>
      <!-- 📎 Upload Dokumen -->
      <div class="box mt-4">
        <div class="section-title">
          <h5><i class="bi bi-upload"></i>DOKUMEN PENTING SELEPAS KELULUSAN</h5>
        </div>
        <p class="text-muted">SILA MUAT NAIK SEMUA BORANG DALAM FORMAT PDF.</p>
   <form action="student_upload_forms.php" method="post" enctype="multipart/form-data" class="row g-4 text-center">

    <!-- ========== BORANG A ========== -->
    <div class="col-md-4">
        <div class="p-3 border rounded bg-light h-100">
            <h5>📄 BORANG A</h5>
            <p>Borang Persetujuan Penyelia PSM </p>

            <?php if(!empty($formA)): ?>
                <p class="text-success fw-bold"></p>
                <a href="<?= $formA ?>" target="_blank" class="btn btn-sm btn-success">LIHAT</a>

            <?php else: ?>
                <input type="file" name="formA" accept="application/pdf" class="form-control" required>
            <?php endif; ?>

        </div>
    </div>


    <!-- ========== BORANG B ========== -->
    <div class="col-md-4">
        <div class="p-3 border rounded bg-light h-100">
            <h5>📄 BORANG B</h5>
            <p>Borang Cadangan Tajuk PSM</p>

            <?php if(!empty($formB)): ?>
                <p class="text-success fw-bold"></p>
                <a href="<?= $formB ?>" target="_blank" class="btn btn-sm btn-success">LIHAT</a>
            <?php else: ?>
                <input type="file" name="formB" accept="application/pdf" class="form-control" required>
            <?php endif; ?>

        </div>
    </div>


    <!-- ========== BORANG C ========== -->
    <div class="col-md-4">
        <div class="p-3 border rounded bg-light h-100">
            <h5>📄 BORANG C</h5>
            <p>Lampiran Sokongan</p>

            <?php if(!empty($formC)): ?>
                <p class="text-success fw-bold"></p>
                <a href="<?= $formC ?>" target="_blank" class="btn btn-sm btn-success">LIHAT</a>
            <?php else: ?>
                <input type="file" name="formC" accept="application/pdf" class="form-control" required>
            <?php endif; ?>

        </div>
    </div>

    <!-- ========== BUTTON SUBMIT ========== -->
    <?php if(empty($formA) || empty($formB) || empty($formC)): ?>
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-success">HANTAR DOKUMEN</button>

        </div>
    <?php endif; ?>

</form>

      </div>
      <?php
  } else {
      echo "<p class='text-muted'>Tiada tajuk yang diluluskan oleh penyelia setakat ini.</p>";
  }
  ?>
</div>


<script>
document.querySelector("form").addEventListener("submit", function(e) {
    if (!confirm("Anda pasti mahu menghantar dokumen ini?")) {
        e.preventDefault(); // hentikan form submit
    }
});
</script>


</body>
</html>
