<?php
session_start();
require_once 'config.php';

// Semak admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// Proses padam penyelia
if (isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM penyelia WHERE id_penyelia = $delete_id");
    header("Location: admin_manage_supervisors.php");
    exit;
}

// Ambil semua penyelia
$result = $conn->query("SELECT * FROM penyelia ORDER BY nama_penyelia ASC");
$supervisors = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Senarai Penyelia</title>
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

a {
    text-decoration: none; /* buang garis bawah atau warna link */
    color: inherit;        /* guna warna teks sama macam sekeliling */
}

a:focus, a:active {
    outline: none; /* buang garisan biru bila klik */
}

td a button {
    margin-right: 5px;
}

</style>
</head>
<body>
<div class="container">

    <a class="btn-add" href="admin_add_supervisor.php">TAMBAH PENYELIA</a>

    <?php if (count($supervisors) > 0): ?>
        <table>
 <thead>
<tr>
    <th>NO</th> <!-- Tambah ni -->
    <th>NAMA PENYELIA</th>
    <th>EMAIL</th>
    <th>KURSUS</th>
    <th>KUOTA</th>
    <th>TINDAKAN</th>
</tr>
</thead>
<tbody>
<?php 
$no = 1; // 🟢 mula kira dari 1
foreach ($supervisors as $sup): ?>
<tr>
    <td><?= $no++; ?></td> <!-- 🟢 paparkan nombor -->
    <td><?= htmlspecialchars($sup['nama_penyelia']) ?></td>
    <td><?= htmlspecialchars($sup['email']) ?></td>
    <td><?= htmlspecialchars($sup['course']) ?></td>
    <td><?= htmlspecialchars($sup['kuota']) ?></td>
    <td>
        <a href="admin_edit_supervisor.php?id=<?= $sup['id_penyelia'] ?>">
            <button type="button">EDIT</button>
        </a>
        <form method="post" onsubmit="return confirm('Padam penyelia ini?');" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $sup['id_penyelia'] ?>">
            <button type="submit" class="btn-danger">PADAM</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

        </table>
    <?php else: ?>
        <p>Tiada penyelia dalam sistem.</p>
    <?php endif; ?>
</div>
</body>
</html>
