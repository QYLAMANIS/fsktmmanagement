<?php
session_start();
require_once 'config.php';

// ✅ Pastikan hanya admin boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// ✅ Dapatkan pilihan program jika ada
$selected_program = isset($_GET['program']) ? $_GET['program'] : '';
$selected_psm = isset($_GET['psm']) ? $_GET['psm'] : '';


// ✅ SQL asas
$query = "
SELECT p.id_pelajar, p.nama_pelajar, p.no_matrik, p.program, p.psm, p.emel,
       per.status, s.nama_penyelia
FROM pelajar p
LEFT JOIN permohonan per ON p.id_pelajar = per.id_pelajar AND per.status = 'Diluluskan'
LEFT JOIN penyelia s ON per.id_penyelia = s.id_penyelia
";


$conditions = [];

if (!empty($selected_program)) {
    $conditions[] = "p.program = '" . mysqli_real_escape_string($conn, $selected_program) . "'";
}

if (!empty($selected_psm)) {
    $conditions[] = "p.psm = '" . mysqli_real_escape_string($conn, $selected_psm) . "'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY p.nama_pelajar ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Senarai Pelajar - Admin</title>
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

/* Action buttons (optional color accent) */
button[type="submit"] { background: #0d3b66; color: #fff; border: none; }
button[type="submit"]:hover { background: #0a3054; }

.btn-reset { background: #adb5bd; color: #fff; border: none; }
.btn-reset:hover { background: #868e96; }

/* Jika nak neutral saja, buang class ni dari HTML */
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
.upload-error { color: #e63946; font-weight: 600; margin-top: 8px; }

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
thead { background: #0d3b66; color: white; }
tbody tr:hover { background: #f9f9f9; }
.no-data, .empty-message {
    text-align: center;
    padding: 15px;
    color: #888;
    font-style: italic;
}

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

/* ===== PRINT MODE ===== */
@media print {
    .toolbar {
        display: none !important;
    }

    /* Sembunyikan semua butang dalam table masa print */
    table button {
        display: none !important;
    }

    /* Optional: bagi table nampak kemas masa print */
    body {
        background: white;
        margin: 0;
        padding: 0;
    }
    table {
        border: 1px solid #000;
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
    }
}


/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    body { padding: 15px; }
    .container { padding: 15px; }
    table { font-size: 11px; }
    button, .btn { width: 100%; margin-bottom: 6px; }
    .filter-form, .btn-group { flex-direction: column; align-items: stretch; }
}
td a button {
    margin-right: 5px;
}

/* Jarak antara butang EDIT dan PADAM */
td a, 
td form {
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 0;
}

/* Buang warna biru / garis bawah link */
a {
    text-decoration: none;
    color: inherit;
}
a:focus, a:active {
    outline: none;
}
/* Susun butang EDIT dan PADAM sebelah-sebelah */
td {
    white-space: nowrap; /* elak turun bawah */
}

td a, 
td form {
    display: inline-block;
    margin-right: 6px;
    vertical-align: middle;
}
td a:last-child, 
td form:last-child {
    margin-right: 0;
}.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 15px;
}

.btn-left {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-right {
  display: flex;
  align-items: center;
}

.btn-print {
  background-color: #0d3b66;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 8px 16px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s;
}
.btn-print:hover {
  background-color: #155a8a;
}

</style>

</head>

<body>
<div class="container">


<div class="toolbar">
  <div class="btn-left">
    <button class="btn-add" onclick="window.location.href='admin_add_student.php'">TAMBAH PELAJAR</button>

    <!-- Dropdown Program -->
    <div class="dropdown">
      <button class="dropdown-btn">PROGRAM</button>
      <div class="dropdown-content">
        <a href="?program=BIT">BIT</a>
        <a href="?program=BIS">BIS</a>
        <a href="?program=BIM">BIM</a>
        <a href="?program=BIP">BIP</a>
        <a href="?program=BIW">BIW</a>
      </div>
    </div>

    <!-- Dropdown PSM View -->
    <div class="dropdown">
      <button class="dropdown-btn">PSM VIEW</button>
      <div class="dropdown-content">
        <a href="?psm=PSM1">PSM I</a>
        <a href="?psm=PSM2">PSM II</a>
      </div>
    </div>
  </div>

<div class="btn-right">
  <button class="btn-print" onclick="window.print()">CETAK</button>
  <form action="export_students.php" method="post" style="display:inline-block; margin-left:8px;">
    <button type="submit" name="export" class="btn-print" style="background:#28a745;">EXPORT EXCEL</button>
  </form>
</div>



    <table>
<thead>
    <tr>
        <th>NO</th>
        <th>NAMA PELAJAR</th>
        <th>N0.MATRIK</th>
        <th>PROGRAM</th>
        <th>EMAIL</th>
        <th>PSM</th>
        <th>NAMA PENYELIA</th>
        <th>TINDAKAN</th>
    </tr>
</thead>

        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_pelajar']); ?></td>
                        <td><?= htmlspecialchars($row['no_matrik']); ?></td>
                        <td><?= htmlspecialchars($row['program']); ?></td>
                       <td><?= htmlspecialchars($row['emel']); ?></td>
<td><?= htmlspecialchars($row['psm']); ?></td> <!-- ✅ Tambah sini -->
<td>
    <?php if ($row['status'] === 'Diluluskan'): ?>
         <?= htmlspecialchars($row['nama_penyelia']); ?>
    <?php else: ?>
        -
    <?php endif; ?>
</td>

                        <td>
                            <a href="admin_edit_student.php?id=<?= $row['id_pelajar']; ?>">
                                <button type="button" class="btn-edit">EDIT</button>
                            </a>
                            <a href="admin_delete_student.php?id=<?= $row['id_pelajar']; ?>" 
                               onclick="return confirm('Anda pasti ingin padam pelajar ini? Semua data berkaitan mungkin terkesan.')">
                                <button type="button" class="btn-danger">PADAM</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="empty-message">Tiada pelajar didaftarkan buat masa ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
