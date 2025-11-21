<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
    header("Location: student_login.php");
    exit();
}

$id_pelajar = $_SESSION['user_id'];

// Batalkan permohonan
if (isset($_POST['batal_id'])) {
    $id = intval($_POST['batal_id']);
    $sql = "UPDATE temujanji 
            SET status='Dibatalkan' 
            WHERE id='$id' AND id_pelajar='$id_pelajar' AND status='Dalam Proses'";
    $conn->query($sql);
}

// Ambil data pelajar + temujanji
$result = $conn->query("
    SELECT t.*, p.nama_pelajar, p.no_matrik
    FROM temujanji t
    JOIN pelajar p ON t.id_pelajar = p.id_pelajar
    WHERE t.id_pelajar = '$id_pelajar'
    ORDER BY t.tarikh DESC, t.masa DESC
");
?>
<!DOCTYPE html>
<html lang="ms">
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <meta charset="UTF-8">
  <title>Sejarah Temujanji</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #fff;
    color: #333;
    margin: 0;            /* buang ruang kosong atas */
    padding: 20px;        /* kecilkan padding */
    font-size: 14px;
}

h4 {
    margin-top: 0;        /* elak heading turun ke bawah */
}

.container-form {
    max-width: 850px;
    margin: auto;
    border-radius: 8px;
    padding: 35px;
}

h4 {
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 5px;
    color: #0d3b66; /* biru header */
}

.sub-header {
    text-align: center;
    font-size: 13px;
    color: #666;
    margin-bottom: 25px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff; /* putih semua */
}

th, td {
    padding: 10px 12px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

th {
    background-color: #0d3b66; /* biru yang awak nak */
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    border: none;
}

td {
    background: #fff; /* semua td putih */
    color: #333;
}

tr:hover td {
    background: #f9f9f9; /* hover ringan */
}

.btn-secondary, .btn-dark {
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-secondary {
    background: #e0e0e0;
    color: #333;
}

.btn-secondary:hover {
    background: #d4d4d4;
}

.btn-dark {
    background: #0d3b66; /* biru sama header */
    color: #fff;
}

.btn-dark:hover {
    background: #0a2d50; /* sedikit gelap untuk hover */
}

/* status tag tanpa background, cuma warna teks */
.status-proses {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 12px;
    background: none;      /* tiada background */
    color: red;            /* merah */
}

.status-lulus {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 12px;
    background: none;
    color: green;          /* hijau */
}

.status-batal {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 12px;
    background: none;
    color: red;            /* merah */
}

.no-record {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 20px 0;
}
</style>


</head>

<body>
  <div class="container-form">
    <h4>SENARAI & SEJARAH TEMUJANJI</h4>
    <div class="sub-header">Rekod permohonan temujanji pelajar</div>

    <table>
      <thead>
        <tr>
          <th>NO.</th>
          <th>NAMA PELAJAR</th>
          <th>N0.MATRIK</th>
          <th>TARIKH</th>
          <th>MASA</th>
          <th>TUJUAN</th>
          <th>STATUS</th>
          <th>TINDAKAN</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php $bil = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $bil++; ?></td>
              <td><?= htmlspecialchars($row['nama_pelajar']); ?></td>
              <td><?= htmlspecialchars($row['no_matrik']); ?></td>
              <td><?= htmlspecialchars($row['tarikh']); ?></td>
              <td><?= htmlspecialchars(substr($row['masa'], 0, 5)); ?></td>
              <td><?= htmlspecialchars($row['tujuan']); ?></td>
              <td>
                <?php
                  if ($row['status'] == 'Dalam Proses') echo "<span class='status-proses'>DALAM PROSES</span>";
                  elseif ($row['status'] == 'Diluluskan') echo "<span class='status-lulus'>DILULUSKAN</span>";
                  elseif ($row['status'] == 'Dibatalkan') echo "<span class='status-batal'>DIBATALKAN</span>";
                  else echo htmlspecialchars($row['status']);
                ?>
              </td>
              <td>
                <?php if ($row['status'] == 'Dalam Proses'): ?>
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Anda pasti mahu batalkan permohonan ini?');">
                    <input type="hidden" name="batal_id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn-secondary">BATAL</button>
                  </form>
                <?php else: ?>
                  <span style="color:#aaa;">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="no-record">Tiada rekod temujanji ditemui.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
