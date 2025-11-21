<?php
session_start();
require_once 'config.php';

// ✅ Semak login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];
$permohonan_id = $_GET['id'] ?? null;

if (!$permohonan_id) {
    echo "<div style='text-align:center; margin-top:50px; color:red;'>❌ Tiada permohonan dipilih.</div>";
    exit();
}

// ✅ Dapatkan maklumat penuh pelajar dan permohonan
$sql = "
SELECT 
    pel.nama_pelajar, pel.no_matrik, pel.program, pel.emel,
    p.tajuk1, p.abstrak1, p.tajuk2, p.abstrak2, p.tajuk3, p.abstrak3,
    p.status, p.tajuk_dipilih, p.komen, p.tarikh_hantar,
    p.formA, p.formB, p.formC
FROM permohonan p
JOIN pelajar pel ON pel.id_pelajar = p.id_pelajar
WHERE p.id = ? AND p.id_penyelia = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $permohonan_id, $id_penyelia);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<div style='text-align:center; margin-top:50px; color:red;'>❌ Permohonan tidak dijumpai atau bukan di bawah penyelia ini.</div>";
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
<title>Maklumat Permohonan Pelajar | Sistem PSM</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: #F5F6FA;
    padding: 25px;
    font-size: 14px;
    color: #37474F;
}

/* Box – Kemas & Premium */
.box {
    background: #FFFFFF;
    border-radius: 10px;
    padding: 22px;
    margin-bottom: 28px;
    border: 1px solid #E6E9EE;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

/* Tajuk Dalam Box */
.box h3 {
    font-size: 15px;
    font-weight: 700;
    color: #244E78;
    margin-bottom: 12px;
    letter-spacing: 0.3px;
}

/* Table Soft Neutral */
.table {
    width: 100%;
    font-size: 14px;
    border-collapse: collapse;
}

.table th {
    width: 30%;
    background: #F8FAFD; 
    color: #244E78;
    padding: 10px 12px;
    font-weight: 600;
    border-bottom: 1px solid #E5E8ED;
}

.table td {
    background: #FFFFFF;
    padding: 10px 12px;
    border-bottom: 1px solid #EEF1F5;
}

/* Status Badge – buang background, hanya teks warna */
.status-diluluskan {
    background: none !important;
    color: #2E7D32; /* hijau lembut */
    font-weight: 700;
    padding: 0 !important;
}

.status-ditolak {
    background: none !important;
    color: #C62828; /* merah lembut */
    font-weight: 700;
    padding: 0 !important;
}

.status-menunggu {
    background: none !important;
    color: #F9A825; /* kuning gelap */
    font-weight: 700;
    padding: 0 !important;
}

/* Jadual – semua tulisan hitam */
.table th,
.table td {
    color: #000 !important;   /* semua tulisan hitam */
}


/* Document Button – kecil & kemas */
.doc-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.doc-item {
    background: #F5F7FB;
    border: 1px solid #DCE2EA;
    padding: 8px 12px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    transition: 0.2s ease;
    font-size: 13px;
}

.doc-item:hover {
    background: #E9F0FA;
}

.doc-item i {
    color: #244E78;
    margin-right: 8px;
}

.doc-item a {
    color: #244E78;
    font-weight: 600;
    text-decoration: none;
}

.doc-item a:hover {
    text-decoration: underline;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    body { padding: 15px; }
    .box { padding: 16px; }
    .table th, .table td { font-size: 13px; }
}

</style>

</head>

<body>

<!-- 🔹 Maklumat Pelajar -->
<div class="box">
  <h3><i class="fa-solid fa-user-graduate"></i> MAKLUMAT PELAJAR</h3>
  <table class="table">
    <tr><th>NAMA PELAJAR</th><td><?= htmlspecialchars($row['nama_pelajar']); ?></td></tr>
    <tr><th>N0.MATRIK</th><td><?= htmlspecialchars($row['no_matrik']); ?></td></tr>
    <tr><th>BIDANG</th><td><?= htmlspecialchars($row['program']); ?></td></tr>
    <tr><th>EMAIL</th><td><?= htmlspecialchars($row['emel']); ?></td></tr>
    <tr><th>N0.TELEFON</th><td><?= htmlspecialchars($row['no_tel'] ?? '-'); ?></td></tr>
  </table>
</div>

<!-- 🔹 Status Permohonan -->
<div class="box">
  <h3><i class="fa-solid fa-circle-check"></i> STATUS PERMOHONAN</h3>
  <table class="table">
    <tr>
      <th>STATUS PERMOHONAN</th>
      <td>
        <?php 
          $status = strtolower(trim($row['status']));
          $statusClass = match($status) {
            'diluluskan' => 'status-diluluskan',
            'ditolak' => 'status-ditolak',
            default => 'status-menunggu'
          };
        ?>
        <span class="<?= $statusClass ?>"><?= htmlspecialchars($row['status']); ?></span>
      </td>
    </tr>
    <?php if (!empty($row['tajuk_dipilih'])): ?>
    <tr>
  <th>TAJUK DILULUSKAN</th>
  <td>
    <?php
      if (strtolower($row['status']) === 'diluluskan' && !empty($row['tajuk_dipilih'])) {
          echo htmlspecialchars($row['tajuk_dipilih']);
      } else {
          echo '-';
      }
    ?>
  </td>
</tr>

    <?php endif; ?>
    <?php if (!empty($row['komen'])): ?>
    <tr><th>KOMEN PENYELIA</th><td><?= nl2br(htmlspecialchars($row['komen'])); ?></td></tr>
    <?php endif; ?>
    <tr><th>TARIKH HANTAR</th><td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['tarikh_hantar']))); ?></td></tr>
  </table>
</div>

<!-- 🔹 Cadangan Tajuk & Abstrak -->
<div class="box">
  <h3><i class="fa-solid fa-lightbulb"></i> SENARAI CADANGAN TAJUK & ABSTRAK</h3>
  <?php for ($i=1; $i<=3; $i++): ?>
    <?php if (!empty($row["tajuk$i"])): ?>
      <div class="mb-4">
        <p><strong>Tajuk <?= $i ?>:</strong> <?= htmlspecialchars($row["tajuk$i"]); ?></p>
        <?php if (!empty($row["abstrak$i"])): ?>
          <p><strong>Abstrak:</strong><br>
  <?= !empty($row["abstrak$i"]) ? nl2br(htmlspecialchars($row["abstrak$i"])) : '<em>Tiada abstrak disediakan.</em>'; ?>
</p>

        <?php endif; ?>
      </div>
      <?php if ($i < 3) echo "<hr>"; ?>
    <?php endif; ?>
  <?php endfor; ?>
</div>

<!-- 🔹 Dokumen Dilampirkan -->
<div class="box">
  <h3><i class="fa-solid fa-paperclip"></i> DOKUMEN DILAMPIRKAN</h3>
  <div class="doc-list">
    <?php 
    $docs = ['formA' => 'Borang A', 'formB' => 'Borang B', 'formC' => 'Borang C'];
    $hasDocs = false;
    foreach ($docs as $key => $label):
      if (!empty($row[$key])): 
        $hasDocs = true; ?>
        <div class="doc-item">
          <i class="fa-solid fa-file-pdf"></i>
          <a href="<?= htmlspecialchars($row[$key]); ?>" target="_blank"><?= $label; ?></a>
        </div>
      <?php endif;
    endforeach;
    if (!$hasDocs): ?>
      <p class="text-muted">Tiada dokumen dilampirkan.</p>
    <?php endif; ?>
  </div>
</div>


</body>
</html>
