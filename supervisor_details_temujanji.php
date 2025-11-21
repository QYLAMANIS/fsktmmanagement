<?php
session_start();
require_once 'config.php';

// ✅ Semak login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

// ✅ Semak jika ID pelajar dihantar
if (!isset($_GET['id_pelajar'])) {
    echo "<script>alert('Ralat: Tiada ID pelajar.'); window.location='supervisor_temujanji.php';</script>";
    exit();
}

$id_pelajar = intval($_GET['id_pelajar']);
$id_penyelia = $_SESSION['user_id'];

// ✅ Dapatkan maklumat pelajar
$pelajar = $conn->prepare("SELECT nama_pelajar, no_matrik FROM pelajar WHERE id_pelajar = ?");
$pelajar->bind_param("i", $id_pelajar);
$pelajar->execute();
$info = $pelajar->get_result()->fetch_assoc();

// ✅ Dapatkan semua temujanji pelajar
$sql = "
    SELECT id, tarikh, masa, tujuan, status, komen
    FROM temujanji
    WHERE id_pelajar = ? AND id_penyelia = ?
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_pelajar, $id_penyelia);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta charset="UTF-8">
    <title>Butiran Temujanji Pelajar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body{font-family:'Segoe UI',sans-serif;background:#f4f6f8;padding:30px;font-size: 12px;}
.table thead{background:#0d3b66;color:#0d3b66;}
.table tbody tr:hover{background:#0d3b66;}
.table th, .table td {
    text-align: center;
    vertical-align: middle; /* supaya teks di tengah secara menegak juga */
}
</style>

</head>
<body>

 <div class="container">


<table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>TARIKH</th>
                    <th>MASA</th>
                    <th>TUJUAN</th>
                    <th>STATUS</th>
                    <th>KOMEN</th>
                    <th>TINDAKAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tarikh']) ?></td>
                            <td><?= htmlspecialchars(substr($row['masa'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($row['tujuan']) ?></td>
                            <td>
                                <?php
                                    if ($row['status'] == 'Dalam Proses')
                                        echo '<span>DALAM PROSES</span>';
                                    elseif ($row['status'] == 'Diluluskan')
                                        echo '<span>DILULUSKAN</span>';
                                    elseif ($row['status'] == 'Ditolak')
                                        echo '<span>DITOLAK</span>';
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['komen'] ?: '-') ?></td>
                            <td>
                                <?php if ($row['status'] == 'Dalam Proses'): ?>
                                    <button class="btn btn-lulus btn-success" data-bs-toggle="modal" data-bs-target="#komenModal" data-id="<?= $row['id'] ?>" data-status="Diluluskan">LULUS</button>
                                    <button class="btn btn-tolak btn-danger" data-bs-toggle="modal" data-bs-target="#komenModal" data-id="<?= $row['id'] ?>" data-status="Ditolak">TOLAK</button>
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">Tiada temujanji direkodkan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal komen -->
<div class="modal fade" id="komenModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="supervisor_update_temujanji_status.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">RUANGAN KOMEN</h5>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="modal-id">
        <input type="hidden" name="status" id="modal-status">
        <div class="mb-3">
            <label for="komen" class="form-label">KOMEN:</label>
            <textarea name="komen" id="komen" class="form-control" required placeholder="Tulis komen anda..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary w-100">HANTAR</button>
      </div>
    </form>
  </div>
</div>

<script>
    const komenModal = document.getElementById('komenModal');
    komenModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('modal-id').value = button.getAttribute('data-id');
        document.getElementById('modal-status').value = button.getAttribute('data-status');
    });
</script>


</body>
</html>
