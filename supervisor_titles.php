<?php
session_start();
require_once 'config.php';

// ✅ Semak login penyelia
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='penyelia'){
    header('Location: supervisor_login.php');
    exit();
}

$id_penyelia = $_SESSION['user_id'];

// Ambil semua tajuk penyelia
$stmt = $conn->prepare("
    SELECT 
        t.id, 
        t.tajuk, 
        t.abstrak, 
        t.bidang, 
        t.status, 
        t.keterangan,
        p.nama_pelajar
    FROM tajuk_penyelia t
    LEFT JOIN permohonan m 
        ON TRIM(LOWER(t.tajuk)) = TRIM(LOWER(m.tajuk_dipilih))
        AND m.status = 'Diluluskan'
    LEFT JOIN pelajar p 
        ON m.id_pelajar = p.id_pelajar
    WHERE t.id_penyelia = ?
    ORDER BY t.id DESC
");



$stmt->bind_param("i", $id_penyelia);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SENARAI TAJUK PENYELIA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== RESET & BASE ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    font-size: 12px;
 
    margin: 0;
    padding: 30px;
    color: #222;
}

/* ===== CONTAINER ===== */
.container {
    max-width: 1100px;
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

/* Action buttons */
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
.btn-reset:hover {
    background: #868e96;
}

/* Neutralize colored buttons */
.btn-warning, .btn-danger {
    background: #f9f9f9 !important;
    color: #333 !important;
    border: 1px solid #ccc !important;
}

/* ===== BUTTON GROUP ===== */
.btn-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    flex-wrap: wrap;
}
.btn-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-right { margin-left: auto; }

/* ===== FORMS ===== */
form[enctype="multipart/form-data"] {
    background: #f9fbfc;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d8dee9;
    margin-bottom: 20px;
}
input[type="text"], select, input[type="file"] {
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px;
    margin-right: 5px;
}
.upload-error {
    color: #e63946;
    font-weight: 600;
    margin-top: 8px;
}

/* ===== DROPDOWN ===== */
.dropdown { position: relative; }
.dropdown-content {
    display: none;
    position: absolute;
    top: 35px;
    left: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 9999;
}
.dropdown-content a {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #333;
    font-size: 12px;
}
.dropdown-content a:hover { background-color: #f5f5f5; }
.dropdown:hover .dropdown-content { display: block; }

/* ===== TABLE ===== */
table {
    width: 100%;
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
thead {
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

/* ===== TABLE STYLE COMBINE ===== */
.table thead { background: #0d3b66; color: white; }
.table tbody tr:hover { background: #f9f9f9; }
.table th, .table td { text-align: center; vertical-align: middle; }

/* ===== PAGINATION ===== */
.pagination {
    margin-top: 12px;
    text-align: center;
}
.pagination a {
    display: inline-block;
    padding: 5px 8px;
    margin: 2px;
    border-radius: 4px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #333;
}
.pagination a.active {
    background: #0d3b66;
    color: white;
    border-color: #0d3b66;
}
.pagination a:hover { background: #e9ecef; }

/* ===== LINK & ACTION BUTTONS ===== */
td a button {
    margin-right: 5px;
}
td a, td form {
    display: inline-block;
    margin-right: 15px;
    vertical-align: middle;
}
td button {
    min-width: 65px;
}

td {
    white-space: nowrap;
    text-align: center;
}
td a button {
    margin-bottom: 4px; /* jarak menegak sikit kalau satu baris ke dua baris */
}

td a:last-child, td form:last-child {
    margin-right: 0;
}
a {
    text-decoration: none;
    color: inherit;
}
a:focus, a:active {
    outline: none;
}

/* ===== PRINT MODE ===== */
@media print {
    .btn-group { display: none !important; }
    body { background: white; }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    body { padding: 15px; }
    .container { padding: 15px; }
    table { font-size: 11px; }
    button, .btn { width: 100%; margin-bottom: 6px; }
    .filter-form, .btn-group { flex-direction: column; align-items: stretch; }
}

/* Warna teks link supaya sama dengan teks biasa */
td a {
    color: #222 !important;
    text-decoration: none !important;
    font-weight: 500;
}
td a:hover {
    color: #0d3b66 !important;
}

/* Pastikan semua link dalam jadual warna sama */
td a:link,
td a:visited,
td a:active {
    color: #222 !important;
    text-decoration: none !important;
}
td a:hover {
    color: #0d3b66 !important;
}

</style>

</head>
<body>
<div class="container">

<a href="supervisor_add_title.php" class="btn btn-primary mb-3">TAMBAH TAJUK BARU</a>


<table class="table table-bordered table-striped">
<thead>
<tr>
<th>NO.</th>
<th>TAJUK</th>
<th>ABSTRAK</th>
<th>BIDANG</th>
<th>STATUS</th>
<th>KETERANGAN</th>
<th>NAMA PELAJAR</th> 
<th>TINDAKAN</th>
</tr>
</thead>
<tbody>
<?php 
$no = 1;
while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['tajuk']) ?></td>
<td><?= nl2br(htmlspecialchars($row['abstrak'])) ?></td>
<td><?= htmlspecialchars($row['bidang']) ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>
<td><?= htmlspecialchars($row['keterangan']) ?></td>
<td>
  <?php if ($row['nama_pelajar']): ?>
      <span class="text-success"><?= htmlspecialchars($row['nama_pelajar']) ?></span>
  <?php else: ?>
      <span class="text-secondary">Belum diambil</span>
  <?php endif; ?>
</td>

<td>
    <a href="supervisor_edit_title.php?id=<?= $row['id'] ?>" 
       class="btn btn-sm btn-warning"
       onclick="return confirm('Anda pasti mahu mengubah tajuk ini?');">
       EDIT
    </a>
    <a href="supervisor_delete_title.php?id=<?= $row['id'] ?>" 
       class="btn btn-sm btn-danger" 
       onclick="return confirm('Anda pasti mahu padam tajuk ini?');">
       PADAM
    </a>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</body>
</html>
