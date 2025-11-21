<?php
session_start();
require_once 'config.php';

// ✅ Semak login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];

// ✅ Proses tindakan (Lulus / Tolak)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $permohonan_id = $_POST['permohonan_id'];
    $action = $_POST['action'];
    $komen = $_POST['komen'] ?? null;
    $tajuk_dipilih = $_POST['tajuk_dipilih_'.$permohonan_id] ?? null;

    // Cegah kes approve tanpa tajuk
    if ($action === 'approve' && empty($tajuk_dipilih)) {
        echo "<script>alert('⚠️ Sila pilih tajuk sebelum meluluskan.'); history.back();</script>";
        exit;
    }

    $status = $action === 'approve' ? 'Diluluskan' : ($action === 'reject' ? 'Ditolak' : 'Menunggu');

    // ✅ Update status + simpan penyelia jika belum ada
    $sql = "UPDATE permohonan 
            SET status = ?, tajuk_dipilih = ?, komen = ?, id_penyelia = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $status, $tajuk_dipilih, $komen, $id_penyelia, $permohonan_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Status permohonan berjaya dikemaskini!'); window.location.href='supervisor_manage_request_permohonan.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Ralat semasa mengemaskini.');</script>";
    }
}

// ✅ Ambil senarai permohonan pelajar di bawah penyelia ini
$sql = "
SELECT 
    p.id, p.id_pelajar, p.status, p.tajuk_dipilih, p.tarikh_hantar,
    pel.nama_pelajar, pel.no_matrik, pel.program,
    p.tajuk1, p.tajuk2, p.tajuk3
FROM permohonan p
JOIN pelajar pel ON pel.id_pelajar = p.id_pelajar
WHERE (p.id_penyelia = ? OR p.id_penyelia IS NULL)
ORDER BY p.tarikh_hantar DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_penyelia);
$stmt->execute();
$permohonan_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Senarai Permohonan Pelajar | Sistem PSM</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== RESET & BASE ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 30px;
    color: #222;
    background: #f4f6f8;
    text-align: center; /* Tengah kandungan utama */
}

/* ===== CONTAINER ===== */
.container {
    max-width: 1000px;
    margin: auto;
    padding: 25px;
    border-radius: 10px;
}

/* ===== HEADING ===== */
h2 {
    color: #0d3b66;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-bottom: 20px;
}

/* ===== BUTTONS ===== */
button, .btn, .btn-add, .btn-print, .dropdown-btn {
    background: #f9f9f9;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 6px 12px;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.25s ease;
}

button:hover,
.btn:hover,
.btn-add:hover,
.btn-print:hover,
.dropdown-btn:hover {
    background: #e9ecef;
}

button[type="submit"] {
    background: #0d3b66;
    color: #fff;
    border: none;
}
button[type="submit"]:hover {
    background: #0a3054;
}

.btn-reset {
    background: #adb5bd;
    color: #fff;
    border: none;
}
.btn-reset:hover { background: #868e96; }

.btn-warning, .btn-danger {
    background: #f9f9f9 !important;
    color: #333 !important;
    border: 1px solid #ccc !important;
}

/* ===== BUTTON GROUP ===== */
.btn-group {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

/* ===== FORM (UPLOAD / FILTER) ===== */
form[enctype="multipart/form-data"] {
    background: #f9fbfc;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d8dee9;
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
}

input[type="text"], select, input[type="file"] {
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px;
}

.upload-error { color: #e63946; font-weight: 600; margin-top: 8px; }

/* ===== TABLE ===== */
table, .table {
    width: 95%;
    margin: 0 auto;
    border-collapse: collapse;
    margin-top: 15px;
    background: white;
    font-size: 12px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
    vertical-align: middle;
}

thead, .table thead {
    background: #0d3b66;
    color: white;
}

tbody tr:hover {
    background: #f9f9f9;
}

.no-data, .empty-message {
    text-align: center;
    padding: 15px;
    color: #888;
    font-style: italic;
}

/* ===== ACTION BUTTONS ===== */
td a button,
td form button {
    margin: 3px 5px;
}

td a, td form {
    display: inline-block;
    margin-right: 8px;
    vertical-align: middle;
}

td a:last-child, td form:last-child {
    margin-right: 0;
}

td {
    white-space: nowrap;
}

a {
    text-decoration: none;
    color: inherit;
}
a:focus, a:active {
    outline: none;
}

/* ===== LINK COLOR FIX ===== */
td a:link,
td a:visited,
td a:active {
    color: #222 !important;
    text-decoration: none !important;
    font-weight: 500;
}
td a:hover {
    color: #0d3b66 !important;
}

/* ===== FILTER BAR ===== */
.filter-bar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    background: #f9fbfc;
    padding: 10px;
    border: 1px solid #d8dee9;
    border-radius: 6px;
    margin-bottom: 15px;
}

.filter-bar input[type="text"],
.filter-bar select {
    min-width: 130px;
}

/* ===== PAGINATION ===== */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.pagination a, .pagination span {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    background: #fff;
    font-size: 12px;
}

.pagination a:hover {
    background: #0d3b66;
    color: white;
}

.pagination a.active {
    background: #0d3b66;
    color: white;
    font-weight: bold;
}

/* ===== MODAL (POP-UP) ===== */
.modal-dialog {
    max-width: 600px;
    margin: 1.75rem auto;
    font-size: 12px;
}

.modal-body label,
.modal-body textarea,
.modal-body p,
.modal-body b {
    font-size: 12px;
}

.modal-footer .btn {
    font-size: 12px;
    padding: 4px 10px;
}

/* ===== PRINT MODE ===== */
@media print {
    .btn-group, form, .filter-bar { display: none !important; }
    body { background: white; }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    body { padding: 15px; }
    .container { padding: 15px; }
    table { font-size: 11px; }
    button, .btn { width: 100%; margin-bottom: 6px; }
    form, .btn-group { flex-direction: column; align-items: stretch; }
}
</style>



</head>
<body>

<div class="container">



<table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>NO.</th>
        <th>NAMA PELAJAR</th>
        <th>NO.MATRIK</th>
        <th>BIDANG</th>
        <th>STATUS</th>
        <th>TINDAKAN</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($permohonan_list) > 0): 
        $no = 1;
        foreach ($permohonan_list as $row): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_pelajar']); ?></td>
          <td><?= htmlspecialchars($row['no_matrik']); ?></td>
          <td><?= htmlspecialchars($row['program']); ?></td>
          <td class="status <?= htmlspecialchars($row['status']); ?>">
            <?= htmlspecialchars($row['status']); ?>
          </td>
          <td>
            <a href="supervisor_view_student_request_permohonan.php?id=<?= $row['id']; ?>" 
               class="btn btn-info btn-sm">LIHAT</a>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#actionModal<?= $row['id']; ?>">
              TINDAKAN
            </button>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center text-muted">Tiada permohonan ditemui.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ✅ Modal untuk setiap permohonan -->
<?php foreach ($permohonan_list as $row): ?>
<div class="modal fade" id="actionModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title">TINDAKAN: <?= htmlspecialchars($row['nama_pelajar']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="permohonan_id" value="<?= $row['id']; ?>">

          <p><b>NO.MATRIK:</b> <?= htmlspecialchars($row['no_matrik']); ?></p>
          <p><b>BIDANG:</b> <?= htmlspecialchars($row['program']); ?></p>

          <hr>

          <label class="form-label"><b>PILIH TAJUK UNTUK DILULUSKAN:</b></label>
          <?php for($i=1; $i<=3; $i++): ?>
            <?php if(!empty($row["tajuk$i"])): ?>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" 
                     name="tajuk_dipilih_<?= $row['id']; ?>" 
                     value="<?= htmlspecialchars($row["tajuk$i"]); ?>">
              <label class="form-check-label"><?= htmlspecialchars($row["tajuk$i"]); ?></label>
            </div>
            <?php endif; ?>
          <?php endfor; ?>

          <div class="mb-3 mt-3">
            <label class="form-label"><b>KOMEN PENYELIA:</b></label>
            <textarea name="komen" class="form-control" rows="3" placeholder="Masukkan komen atau sebab penolakan..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="action" value="approve" class="btn btn-success">LULUSKAN</button>
          <button type="submit" name="action" value="reject" class="btn btn-danger">TOLAK</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">TUTUP</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>



</html>
