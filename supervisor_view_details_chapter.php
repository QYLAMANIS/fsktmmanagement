<?php
session_start();
require_once 'config.php';

// 🔐 Semak login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header("Location: supervisor_login.php");
    exit();
}

$id_penyelia = $_SESSION['user_id'];

// ✅ Proses feedback & file corrected
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chapter_id'])) {
    $chapter_id = (int)$_POST['chapter_id'];
    $feedback = $_POST['feedback'] ?? '';

    $file_corrected = null;
    if (isset($_FILES['file_corrected']) && $_FILES['file_corrected']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/corrected/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['file_corrected']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['file_corrected']['tmp_name'], $targetFile)) {
            $file_corrected = $filename;
        }
    }

    if ($file_corrected) {
        $stmt = $conn->prepare("UPDATE chapter SET status='Reviewed', supervisor_comment=?, file_corrected=? WHERE id=?");
        $stmt->bind_param("ssi", $feedback, $file_corrected, $chapter_id);
    } else {
        $stmt = $conn->prepare("UPDATE chapter SET status='Reviewed', supervisor_comment=? WHERE id=?");
        $stmt->bind_param("si", $feedback, $chapter_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Feedback dan fail pembetulan berjaya disimpan.";
        header("Location: supervisor_review_chapter.php");
        exit();
    } else {
        $_SESSION['error'] = "⚠️ Ralat semasa menyimpan data.";
    }
}

// ✅ Ambil semua chapter pelajar
$id_pelajar = isset($_GET['id_pelajar']) ? (int)$_GET['id_pelajar'] : 0;

$query = "
    SELECT c.*, pel.nama_pelajar, pel.no_matrik
    FROM chapter c
    JOIN permohonan p ON c.id_pelajar = p.id_pelajar
    JOIN pelajar pel ON c.id_pelajar = pel.id_pelajar
    WHERE p.id_penyelia = ? AND c.id_pelajar = ?
    ORDER BY c.id DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_penyelia, $id_pelajar);
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
<title>Semakan Chapter Pelajar</title>

<!-- ✅ Gaya utama awak -->
<style>
body{font-family:'Segoe UI',sans-serif;background:#f4f6f8;padding:30px;font-size: 12px;}
.table thead{background:#0d3b66;color:#0d3b66;}
.table tbody tr:hover{background:#eaf0f6;}
.table th, .table td {
    text-align: center;
    vertical-align: middle;
}

/* ✅ Butang komen */
.btn.feedback {
    background:#0d3b66;
    color:white;
    border:none;
    border-radius:4px;
    padding:5px 10px;
    font-size:11px;
    cursor:pointer;
    transition:0.2s;
}
.btn.feedback:hover {
    background:#155a9a;
}

/* ✅ Modal styling */
.modal {
    display:none;
    position:fixed;
    z-index:1000;
    left:0;
    top:0;
    width:100%;
    height:100%;
    overflow:auto;
    background-color:rgba(0,0,0,0.4);
}
.modal-content {
    background:#fff;
    margin:8% auto;
    padding:20px;
    border-radius:10px;
    width: 40%;     /* atau 50% ikut citarasa */
    max-width: 450px;  /* pastikan tak besar sangat */
    box-shadow:0 0 10px rgba(0,0,0,0.2);
    animation:fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from {opacity:0; transform:scale(0.9);}
    to {opacity:1; transform:scale(1);}
}
.close {
    color:#aaa;
    float:right;
    font-size:18px;
    cursor:pointer;
}
.close:hover {
    color:#000;
}
.modal-content h3 {
    text-align:center;
    color:#0d3b66;
    margin-bottom:10px;
    font-size:14px;
}
.modal-content textarea {
    width:100%;
    height:80px;
    border:1px solid #ccc;
    border-radius:5px;
    padding:8px;
    font-size:12px;
}
.modal-content input[type="file"] {
    margin-top:10px;
    font-size:11px;
}
.modal-content button {
    margin-top:12px;
    width:100%;
    padding:7px;
    border:none;
    border-radius:5px;
    background:#0d3b66;
    color:white;
    font-size:12px;
    cursor:pointer;
}
.modal-content button:hover {
    background:#155a9a;
}
</style>
</head>
<body>

<div class="container">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>NAMA PELAJAR</th>
            <th>N0.MATRIK</th>
            <th>LAPORAN</th>
            <th>FAIL</th>
            <th>STATUS</th>
            <th>KOMEN</th>
            <th>FAIL DISEMAK</th>
            <th>TINDAKAN</th>
        </tr>
    </thead>
    <tbody>
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_pelajar']); ?></td>
                <td><?= htmlspecialchars($row['no_matrik']); ?></td>
                <td><?= htmlspecialchars($row['chapter']); ?></td>
                <td>
                    <?php if($row['chapter_file']): ?>
                        <a href="<?= htmlspecialchars($row['chapter_file']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">LIHAT</a>
                    <?php else: ?>-
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['status'] ?? 'Menunggu'); ?></td>
                <td><?= htmlspecialchars($row['supervisor_comment'] ?? '-'); ?></td>
                <td>
                    <?php if($row['file_corrected']): ?>
                        <a href="uploads/corrected/<?= htmlspecialchars($row['file_corrected']); ?>" target="_blank" class="btn btn-sm btn-outline-success">MUAT TURUN</a>
                    <?php else: ?>-
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn feedback" onclick="openModal(<?= $row['id']; ?>)">KOMEN</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center; padding:15px;">TIADA LAPORAN DIHANTAR.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- ✅ MODAL POPUP -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>MAKLUM BALAS</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="chapter_id" id="chapter_id">
            <label for="feedback">KOMEN PENYELIA:</label>
            <textarea name="feedback" id="feedback" required></textarea>

            <label for="file_corrected">MUAT NAIK FAIL DISEMAK</label>
            <input type="file" name="file_corrected" accept=".pdf,.doc,.docx">

            <button type="submit">SIMPAN</button>
        </form>
    </div>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success text-center mt-3"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center mt-3"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
<?php endif; ?>

<script>
function openModal(id) {
    document.getElementById('chapter_id').value = id;
    document.getElementById('feedbackModal').style.display = "block";
}
function closeModal() {
    document.getElementById('feedbackModal').style.display = "none";
}
window.onclick = function(event) {
    const modal = document.getElementById('feedbackModal');
    if (event.target === modal) modal.style.display = "none";
}
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</body>
</html>
