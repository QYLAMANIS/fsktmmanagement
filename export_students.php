<?php
session_start();
require_once 'config.php';

// ✅ Semak admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// ✅ Query sama seperti paparan utama
$query = "
SELECT p.nama_pelajar, p.no_matrik, p.program, p.emel, s.nama_penyelia
FROM pelajar p
LEFT JOIN permohonan per ON p.id_pelajar = per.id_pelajar AND per.status = 'Diluluskan'
LEFT JOIN penyelia s ON per.id_penyelia = s.id_penyelia
ORDER BY p.nama_pelajar ASC
";

$result = mysqli_query($conn, $query);

// ✅ Set header supaya browser download file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=senarai_pelajar_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Bina table untuk export
echo "<table border='1'>";
echo "<tr>
        <th>NO</th>
        <th>NAMA PELAJAR</th>
        <th>NO. MATRIK</th>
        <th>PROGRAM</th>
        <th>EMAIL</th>
        <th>NAMA PENYELIA</th>
      </tr>";

if ($result && mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_pelajar']) . "</td>";
        echo "<td>" . htmlspecialchars($row['no_matrik']) . "</td>";
        echo "<td>" . htmlspecialchars(strtoupper($row['program'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['emel']) . "</td>";
        echo "<td>" . ($row['nama_penyelia'] ? htmlspecialchars($row['nama_penyelia']) : '-') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' align='center'>Tiada data</td></tr>";
}

echo "</table>";
exit;
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">