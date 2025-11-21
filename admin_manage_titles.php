<?php
require 'config.php';

$search = $_GET['search'] ?? '';
$year_filter = $_GET['year'] ?? '';
$supervisor_filter = $_GET['supervisor'] ?? '';
$course_filter = $_GET['course'] ?? '';
$upload_error = '';

// Pagination setup
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default 10
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Upload CSV dan masukkan ke DB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_upload'])) {
    $file_tmp = $_FILES['csv_upload']['tmp_name'];
    $file_ext = strtolower(pathinfo($_FILES['csv_upload']['name'], PATHINFO_EXTENSION));

    if ($file_ext !== 'csv') {
        $upload_error = "Sila upload fail CSV sahaja.";
    } else {
        if (($handle = fopen($file_tmp, "r")) !== FALSE) {
            $header = fgetcsv($handle);
          while (($row = fgetcsv($handle)) !== FALSE) {
    // pastikan ada sekurang-kurangnya 4 lajur
    $row = array_pad($row, 5, ''); // isi nilai kosong kalau tak cukup

    $title = $conn->real_escape_string($row[0]);
    $supervisor = $conn->real_escape_string($row[1]);
    $year = $conn->real_escape_string($row[2]);
    $course = $conn->real_escape_string($row[3]);
    $authors = $conn->real_escape_string($row[4]);

    $conn->query("INSERT INTO sejarah_tajuk (title, supervisor, year, course, authors)
                  VALUES ('$title', '$supervisor', '$year', '$course', '$authors')");
}

            fclose($handle);
            header("Location: admin_manage_titles.php");
            exit;
        } else {
            $upload_error = "Gagal buka fail CSV.";
        }
    }
}

// Padam satu tajuk
if (isset($_POST['delete_title'])) {
    $id = (int) $_POST['delete_title'];
    $conn->query("DELETE FROM sejarah_tajuk WHERE id = $id");
    header("Location: admin_manage_titles.php");
    exit;
}

// Padam semua tajuk
if (isset($_POST['delete_all'])) {
    $conn->query("DELETE FROM sejarah_tajuk");
    header("Location: admin_manage_titles.php");
    exit;
}

// Base query
$sql_base = "FROM sejarah_tajuk WHERE 1=1";
if ($search) $sql_base .= " AND title LIKE '%" . $conn->real_escape_string($search) . "%'";
if ($year_filter) $sql_base .= " AND year = '" . $conn->real_escape_string($year_filter) . "'";
if ($supervisor_filter) $sql_base .= " AND supervisor = '" . $conn->real_escape_string($supervisor_filter) . "'";
if ($course_filter) $sql_base .= " AND course = '" . $conn->real_escape_string($course_filter) . "'";

// Kira jumlah semua rekod
$total_sql = "SELECT COUNT(*) AS total " . $sql_base;
$total_result = $conn->query($total_sql)->fetch_assoc();
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $limit);

// Ambil data untuk page sekarang
$sql = "SELECT * " . $sql_base . " ORDER BY year DESC LIMIT $offset, $limit";
$result = $conn->query($sql);
$psm_list = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Dropdown filter values
$all_years = [];
$yres = $conn->query("SELECT DISTINCT year FROM sejarah_tajuk ORDER BY year ASC");
while ($y = $yres->fetch_assoc()) $all_years[] = $y['year'];

$all_supervisors = [];
$sres = $conn->query("SELECT DISTINCT supervisor FROM sejarah_tajuk ORDER BY supervisor ASC");
while ($s = $sres->fetch_assoc()) $all_supervisors[] = $s['supervisor'];

$all_courses = [];
$cres = $conn->query("SELECT DISTINCT course FROM sejarah_tajuk ORDER BY course ASC");
while ($c = $cres->fetch_assoc()) $all_courses[] = $c['course'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pengurusan Tajuk PSM</title>
<style>
/* ===== RESET & BASE ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 30px;
    color: #222;
    background: #f2f3f5;
    text-align: center; /* 🔹 Tengah semua kandungan */
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
    justify-content: center; /* 🔹 Tengah tajuk */
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
button[type="submit"] { background: #0d3b66; color: #fff; border: none; }
button[type="submit"]:hover { background: #0a3054; }
.btn-reset { background: #adb5bd; color: #fff; border: none; }
.btn-reset:hover { background: #868e96; }

/* Neutral look for action buttons */
.btn-warning, .btn-danger {
    background: #f9f9f9 !important;
    color: #333 !important;
    border: 1px solid #ccc !important;
}

/* ===== BUTTON GROUP ===== */
.btn-group {
    display: flex;
    justify-content: center; /* 🔹 Tengah butang */
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

/* ===== FORMS ===== */
form[enctype="multipart/form-data"] {
    background: #f9fbfc;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d8dee9;
    margin-bottom: 20px;
    display: flex;
    justify-content: center; /* 🔹 Tengah input upload */
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
    width: 95%; /* 🔹 Kurang sikit dari 100% supaya ada ruang kiri kanan */
    margin: 0 auto; /* 🔹 Tengah table */
    border-collapse: collapse;
    margin-top: 15px;
    background: white;
    font-size: 11.5px;
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

/* ===== ACTION BUTTONS ===== */
td a button { margin-right: 5px; }
td a, td form {
    display: inline-block;
    margin-right: 6px;
    margin-bottom: 0;
}
td a:last-child, td form:last-child { margin-right: 0; }
a { text-decoration: none; color: inherit; }
a:focus, a:active { outline: none; }

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
    .btn-group, form { display: none !important; }
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

/* ===== JARAK ANTARA BUTANG DALAM TABLE ===== */
td a button,
td form button {
  margin: 3px 4px; /* atas-bawah 3px, kiri-kanan 4px */
}

/* ===== SUSUNAN BAR CARIAN DAN TAPIS ===== */
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
  font-size: 14px;
}

.pagination a:hover {
  background: #007bff;
  color: white;
}

.pagination a.active {
  background: #007bff;
  color: white;
  font-weight: bold;
}

</style>

</head>
<body>
<div class="container">

    <form method="post" enctype="multipart/form-data">
        <label><strong>UPLOAD FAIL CSV</strong></label>
        <input type="file" name="csv_upload" accept=".csv" required>
        <button type="submit">UPLOAD</button>
    </form>

    <?php if ($upload_error): ?>
        <div class="upload-error"><?= htmlspecialchars($upload_error) ?></div>
    <?php endif; ?>

<form method="get" class="filter-bar">
  <input type="text" name="search" placeholder="Cari tajuk..." value="<?= htmlspecialchars($search) ?>">
  
  <select name="year">
    <option value="">TAHUN</option>
    <?php foreach ($all_years as $year): ?>
      <option value="<?= $year ?>" <?= $year == $year_filter ? 'selected' : '' ?>><?= $year ?></option>
    <?php endforeach; ?>
  </select>

  <select name="supervisor">
    <option value="">SEMUA PENYELIA</option>
    <?php foreach ($all_supervisors as $sup): ?>
      <option value="<?= htmlspecialchars($sup) ?>" <?= $sup == $supervisor_filter ? 'selected' : '' ?>><?= htmlspecialchars($sup) ?></option>
    <?php endforeach; ?>
  </select>

  <select name="course">
    <option value="">SEMUA KURSUS</option>
    <?php foreach ($all_courses as $course): ?>
      <option value="<?= htmlspecialchars($course) ?>" <?= $course == $course_filter ? 'selected' : '' ?>><?= htmlspecialchars($course) ?></option>
    <?php endforeach; ?>
  </select>

  <select name="limit" onchange="this.form.submit()">
    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
    <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
    <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
  </select>

  <button type="submit">TAPIS</button>
  <a href="admin_manage_titles.php" style="text-decoration:none;">
    <button type="button" class="btn-reset">SET SEMULA</button>
  </a>
</form>


    <form method="post" onsubmit="return confirm('Padam SEMUA tajuk?');" style="margin-bottom:10px;">
        <input type="hidden" name="delete_all" value="1">
        <button type="submit" class="btn-danger">PADAM SEMUA</button>
    </form>

    <?php if (!empty($psm_list)): ?>
        <table>
           <thead>
    <tr>
        <th>NO.</th> <!-- Tambah lajur baru -->
        <th>TAJUK</th>
        <th>PENYELIA</th>
        <th>TAHUN</th>
        <th>KURSUS</th>
        <th>PELAJAR</th>
        <th>TINDAKAN</th>
    </tr>
</thead>
<tbody>
    <?php 
    $no = $offset + 1; // kira nombor ikut page semasa
    foreach ($psm_list as $psm): 
    ?>
        <tr>
            <td><?= $no++ ?></td> <!-- Papar nombor -->
            <td><?= htmlspecialchars($psm['title']) ?></td>
            <td><?= htmlspecialchars($psm['supervisor']) ?></td>
            <td><?= htmlspecialchars($psm['year']) ?></td>
            <td><?= htmlspecialchars($psm['course']) ?></td>
            <td><?= htmlspecialchars($psm['authors']) ?></td>
            <td>
                <a href="admin_edit_title.php?id=<?= $psm['id'] ?>">
                    <button type="button">EDIT</button>
                </a>
                <form method="post" style="display:inline;" onsubmit="return confirm('Padam tajuk ini?');">
                    <input type="hidden" name="delete_title" value="<?= $psm['id'] ?>">
                    <button type="submit">PADAM</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>

      <div class="pagination">
    <form method="get" style="display:inline-flex; align-items:center; gap:6px;">
        <label for="page">Page:</label>
        <select name="page" id="page" onchange="this.form.submit()">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <option value="<?= $i ?>" <?= $i == $page ? 'selected' : '' ?>>
                    <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>
        <span>of <?= $total_pages ?></span>

        <?php
        // kekalkan filter lain bila submit dropdown
        foreach ($_GET as $key => $value) {
            if ($key !== 'page') {
                echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
            }
        }
        ?>
    </form>
</div>


    <?php else: ?>
        <p class="no-data">Tiada tajuk ditemui mengikut carian atau tapisan.</p>
    <?php endif; ?>
</div>
</body>
</html>
