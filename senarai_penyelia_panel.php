<?php
session_start();
require_once 'config.php';

// 🔐 Semak login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// 🔍 Dapatkan pilihan paparan dari URL (default = semua)
$filter = $_GET['filter'] ?? 'semua';

switch ($filter) {
    case 'penyelia':
        $sql = "SELECT * FROM penyelia WHERE role LIKE '%penyelia%' AND role NOT LIKE '%panel%'";
        break;
    case 'panel':
        $sql = "SELECT * FROM penyelia WHERE role LIKE '%panel%' AND role NOT LIKE '%penyelia%'";
        break;
    case 'dua':
        $sql = "SELECT * FROM penyelia WHERE role LIKE '%penyelia%' AND role LIKE '%panel%'";
        break;
    default:
        $sql = "SELECT * FROM penyelia";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ms">
<head> 
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta charset="UTF-8">
    <title>Senarai Penyelia & Panel</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        .filter-btn {
            display: inline-block;
            margin: 10px;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .filter-btn:hover {
            background: #0056b3;
        }
        .container {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>📋 Senarai Penyelia & Panel</h2>

    <div class="container">
        <a href="?filter=semua" class="filter-btn">Semua</a>
        <a href="?filter=penyelia" class="filter-btn">Penyelia Sahaja</a>
        <a href="?filter=panel" class="filter-btn">Panel Sahaja</a>
        <a href="?filter=dua" class="filter-btn">Penyelia + Panel</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Emel</th>
            <th>No. Telefon</th>
            <th>Peranan</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_penyelia']) ?></td>
                    <td><?= htmlspecialchars($row['nama_penyelia']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefon']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Tiada rekod ditemui.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
