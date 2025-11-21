<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='penyelia'){
    header('Location: supervisor_login.php');
    exit();
}

$id_penyelia = $_SESSION['user_id'];

if(isset($_POST['submit'])){
    $tajuk = $_POST['tajuk'] ?? '';
    $abstrak = $_POST['abstrak'] ?? '';
    $bidang = $_POST['bidang'] ?? '';
    $status = $_POST['status'] ?? 'aktif';
    $keterangan = $_POST['keterangan'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO tajuk_penyelia (id_penyelia, tajuk, abstrak, bidang, status, keterangan)
        VALUES (?,?,?,?,?,?)
    ");
    $stmt->bind_param("isssss", $id_penyelia, $tajuk, $abstrak, $bidang, $status, $keterangan);
   $stmt->execute();
header('Location: supervisor_titles.php?success=1');
exit();

}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Tambah Tajuk Baru</title>
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
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
<div class="success-popup">
    <div class="popup-content">
        <h4>✅ Tajuk berjaya ditambah!</h4>
        <p>Tajuk anda telah dimasukkan ke dalam sistem.</p>
        <a href="supervisor_titles.php" class="btn btn-primary mt-2">OK</a>
    </div>
</div>
<?php endif; ?>

<div class="container">
<h2 class="mb-4">TAMBAH TAJUK BARU</h2>

<form method="post">
<div class="mb-3">
<label>TAJUK</label>
<input type="text" name="tajuk" class="form-control" required>
</div>
<div class="mb-3">
<label>ABSTRAK</label>
<textarea name="abstrak" class="form-control" rows="4"></textarea>
</div>
<div class="mb-3">
<label>BIDANG</label>
<input type="text" name="bidang" class="form-control">
</div>
<div class="mb-3">
<label>STATUS</label>
<select name="status" class="form-control">
<option value="aktif" selected>AKTIF</option>
<option value="tidak aktif">TIDAK AKTIF</option>
</select>
</div>
<div class="mb-3">
<label>KETERANGAN</label>
<textarea name="keterangan" class="form-control" rows="2"></textarea>
</div>
<button type="submit" name="submit" class="btn btn-success"
        onclick="return confirm('Anda pasti mahu menyimpan tajuk ini?');">
    SIMPAN TAJUK
</button>

<a href="supervisor_titles.php" class="btn btn-secondary">KEMBALI</a>
</form>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style_supervisor.css">

</body>
</html>
