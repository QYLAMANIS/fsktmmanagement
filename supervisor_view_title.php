<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: supervisor_manage_titles.php');
    exit();
}

$id = intval($_GET['id']);
$supervisor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT t.*, u.full_name as supervisor_name FROM titles t LEFT JOIN users u ON t.supervisor_id = u.id WHERE t.id = ? AND t.supervisor_id = ?");
$stmt->bind_param("ii", $id, $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header('Location: supervisor_manage_titles.php');
    exit();
}
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Maklumat Tajuk Projek</title>
    <style>
        table { border-collapse: collapse; }
        td { padding: 8px; }
    </style>
</head>
<body>
    <h2>Maklumat Tajuk Projek</h2>
    <table>
        <tr><td><b>Tajuk</b></td><td><?= htmlspecialchars($row['title']) ?></td></tr>
        <tr><td><b>Penyelia</b></td><td><?= htmlspecialchars($row['supervisor_name']) ?></td></tr>
        <tr><td><b>Objektif</b></td><td><?= nl2br(htmlspecialchars($row['objective'])) ?></td></tr>
        <tr><td><b>Skop</b></td><td><?= nl2br(htmlspecialchars($row['scope'])) ?></td></tr>
        <tr><td><b>Abstrak</b></td><td><?= nl2br(htmlspecialchars($row['abstract'])) ?></td></tr>
        <tr><td><b>Kata Kunci</b></td><td><?= nl2br(htmlspecialchars($row['keywords'])) ?></td></tr>
        <tr><td><b>Deskripsi</b></td><td><?= nl2br(htmlspecialchars($row['description'])) ?></td></tr>
        <tr><td><b>Fail PDF</b></td>
            <td>
                <?php if (!empty($row['pdf_file'])): ?>
                    <a href="<?= htmlspecialchars($row['pdf_file']) ?>" target="_blank">Muat Turun</a>
                <?php endif; ?>
            </td>
        </tr>
        <tr><td><b>Tarikh Upload</b></td><td><?= $row['uploaded_at'] ?></td></tr>
    </table>
    <br>
    <a href="supervisor_manage_titles.php">← Kembali ke Senarai Tajuk</a>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style_supervisor.css">

</body>
</html>