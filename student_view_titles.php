<?php
session_start();
require_once 'config.php';

// Pastikan user login (pelajar atau penyelia)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pelajar','penyelia'])) {
    header('Location: login.php');
    exit();
}

// FILTER
$search = $_GET['search'] ?? '';
$year_filter = $_GET['year'] ?? '';
$supervisor_filter = $_GET['supervisor'] ?? '';
$course_filter = $_GET['course'] ?? '';

// Pagination setup
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Base query
$sql_base = "FROM sejarah_tajuk WHERE 1=1";
if ($search) $sql_base .= " AND title LIKE '%" . $conn->real_escape_string($search) . "%'";
if ($year_filter) $sql_base .= " AND year = '" . $conn->real_escape_string($year_filter) . "'";
if ($supervisor_filter) $sql_base .= " AND supervisor = '" . $conn->real_escape_string($supervisor_filter) . "'";
if ($course_filter) $sql_base .= " AND course = '" . $conn->real_escape_string($course_filter) . "'";

// kira total
$total_sql = "SELECT COUNT(*) AS total " . $sql_base;
$total_result = $conn->query($total_sql)->fetch_assoc();
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $limit);

// Ambil data
$sql = "SELECT * " . $sql_base . " ORDER BY year DESC LIMIT $offset, $limit";
$result = $conn->query($sql);
$psm_list = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Dropdown data
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
<title>Senarai Tajuk PSM</title>

<style>
/* ===== UNIVERSITI CLEAN THEME ===== */

body {
    font-family: 'Segoe UI', sans-serif;
    font-size: 13px;
    margin: 0;
    padding: 40px;
    background: #f4f6f9;
    color: #222;
}

/* ===== CONTAINER ===== */
.container {
    max-width: 1100px;
    margin: auto;
    background: white;
    padding: 35px;
    border-radius: 10px;
    border: 1px solid #d7dbe0;
}

/* ===== TITLE ===== */
h2 {
    font-size: 22px;
    color: #0d3b66;
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* ===== FILTER BAR CARD ===== */
.filter-bar {
    background: #ffffff;
    padding: 20px 25px;
    border-radius: 10px;
    border: 1px solid #d7dbe0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
}

.filter-bar input[type="text"],
.filter-bar select {
    border: 1px solid #cfd6de;
    padding: 8px 10px;
    border-radius: 6px;
    min-width: 160px;
    font-size: 13px;
}

/* ===== BUTTONS ===== */
button, .btn-reset {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 500;
}

button[type="submit"] {
    background: #0d3b66;
    color: white;
}

button[type="submit"]:hover {
    background: #0a3054;
}

.btn-reset {
    background: #adb5bd;
    color: white;
}

.btn-reset:hover {
    background: #8b949b;
}

/* ===== TABLE CARD ===== */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    margin-top: 10px;
    font-size: 13px;
    border: 1px solid #d7dbe0;
    border-radius: 10px;
    overflow: hidden;
}

thead {
    background: #0d3b66;
    color: white;
}

th, td {
    padding: 12px 14px;
    text-align: left;
    border-bottom: 1px solid #e1e5ea;
}

tbody tr:hover {
    background: #f2f4f7;
}

/* ===== NO DATA TEXT ===== */
.no-data {
    text-align: center;
    padding: 20px;
    font-style: italic;
    color: #777;
}

/* ===== PAGINATION ===== */
.pagination {
    margin-top: 25px;
    display: flex;
    justify-content: center;
    gap: 8px;
}

.pagination a {
    padding: 7px 13px;
    border-radius: 6px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #333;
    font-size: 13px;
    background: white;
}

.pagination a.active {
    background: #0d3b66;
    color: white;
    border-color: #0d3b66;
}

.pagination a:hover {
    background: #dfe6ee;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    body { padding: 15px; }
    .container { padding: 20px; }

    th, td {
        font-size: 12px;
        padding: 8px;
    }

    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
}

</style>
</head>

<body>

<div class="container">
<h2>Senarai Tajuk Projek Sarjana Muda (PSM)</h2>

<form method="get" class="filter-bar">
    <input type="text" name="search" placeholder="Cari tajuk..." value="<?= htmlspecialchars($search) ?>">

    <select name="year">
        <option value="">TAHUN</option>
        <?php foreach ($all_years as $y): ?>
            <option value="<?= $y ?>" <?= $y == $year_filter ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
    </select>

    <select name="supervisor">
        <option value="">PENYELIA</option>
        <?php foreach ($all_supervisors as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= $s == $supervisor_filter ? 'selected' : '' ?>>
                <?= htmlspecialchars($s) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="course">
        <option value="">PROGRAM</option>
        <?php foreach ($all_courses as $c): ?>
            <option value="<?= $c ?>" <?= $c == $course_filter ? 'selected' : '' ?>><?= $c ?></option>
        <?php endforeach; ?>
    </select>

    <select name="limit" onchange="this.form.submit()">
        <option value="10" <?= $limit==10?'selected':'' ?>>10</option>
        <option value="20" <?= $limit==20?'selected':'' ?>>20</option>
        <option value="30" <?= $limit==30?'selected':'' ?>>30</option>
        <option value="50" <?= $limit==50?'selected':'' ?>>50</option>
    </select>

    <button type="submit">TAPIS</button>

    <a href="?" style="text-decoration:none;">
        <button type="button" class="btn-reset">RESET</button>
    </a>
</form>

<?php if (empty($psm_list)): ?>
    <p style="color:#888;font-style:italic;">Tiada data dijumpai.</p>
<?php else: ?>

<table>
<thead>
<tr>
    <th>NO</th>
    <th>TAJUK PSM</th>
    <th>PENYELIA</th>
    <th>TAHUN</th>
    <th>PROGRAM</th>
    <th>PELAJAR</th>
</tr>
</thead>

<tbody>
<?php $n = $offset + 1; ?>
<?php foreach ($psm_list as $row): ?>
<tr>
    <td><?= $n++ ?></td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= htmlspecialchars($row['supervisor']) ?></td>
    <td><?= htmlspecialchars($row['year']) ?></td>
    <td><?= htmlspecialchars($row['course']) ?></td>
    <td><?= htmlspecialchars($row['authors']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

<!-- PAGINATION -->
<div class="pagination">
<?php if ($page > 1): ?>
    <a href="?page=<?= $page-1 ?>">‹ Prev</a>
<?php endif; ?>

<?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a class="<?= $page==$i?'active':'' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
<?php endfor; ?>

<?php if ($page < $total_pages): ?>
    <a href="?page=<?= $page+1 ?>">Next ›</a>
<?php endif; ?>
</div>

</div>
</body>
</html>
