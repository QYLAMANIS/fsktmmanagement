<?php
session_start();
require_once 'config.php'; // sambung ke database

// ✅ Pastikan user login & role pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? '';
$email = $_SESSION['email'] ?? '';
$matric = $_SESSION['matric'] ?? '';
$username = $_SESSION['username'] ?? '';

// 🔹 Ambil penyelia yang telah diluluskan
$sql = "SELECT s.id_penyelia, s.nama_penyelia
        FROM permohonan pm
        JOIN penyelia s ON pm.id_penyelia = s.id_penyelia
        WHERE pm.id_pelajar = ? AND pm.status = 'Diluluskan'
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$penyelia_lulus = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Borang Temujanji | FSKTM FYP System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* =============================
   UNIVERSITI FORM STYLE (FSKTM)
============================= */

body {
    font-family: "Times New Roman", serif;
    background: #ffffff;
    color: #000;
    padding: 40px;
    font-size: 15px;
}

/* Kotak borang utama */
.container-form {
    max-width: 800px;
    margin: auto;
    background: #fff;
    border: 1px solid #000;
    padding: 40px;
}

/* Header borang */
h4 {
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 0;
}

.sub-header {
    text-align: center;
    font-size: 13px;
    margin-bottom: 25px;
}

/* Label input */
.form-label {
    font-weight: bold;
    font-size: 14px;
}

/* Input style */
.form-control, 
.form-select {
    border: 1px solid #000 !important;
    border-radius: 0 !important;
    background: #fff;
    font-size: 14px;
    padding: 6px 8px;
}

/* Button */
.btn-dark {
    background: #000 !important;
    border: 1px solid #000 !important;
    border-radius: 0 !important;
    font-weight: bold;
    padding: 8px 20px;
}

.btn-dark:hover {
    background: #333 !important;
}

/* Checkbox */
.form-check-label {
    font-size: 14px;
}

/* Divider line */
hr {
    border-top: 1px solid #000 !important;
    margin: 20px 0;
}

/* Success alert */
.alert {
    font-size: 14px;
    border-radius: 0;
}

/* Remove Bootstrap shadow */
*:focus {
    box-shadow: none !important;
}

</style>
</head>
<body>

<div class="container-form">
    <div class="profile-header">
        <div>
            <h4>Borang Permohonan Sesi Temujanji</h4>
            <div class="sub-header">Fakulti Sains Komputer dan Teknologi Maklumat (FSKTM)</div>
        </div>
     
    </div>

    <hr>

    <!-- ✅ Mesej Berjaya -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center mb-4">
            ✅ Permohonan temujanji berjaya dihantar!
        </div>
    <?php endif; ?>

    <!-- 🔹 Borang -->
<form action="student_insert_temujanji.php" method="POST" onsubmit="return confirmSubmit()">

        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <input type="hidden" name="id_penyelia" value="<?= $penyelia_lulus ? htmlspecialchars($penyelia_lulus['id_penyelia']) : ''; ?>">

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">NAMA</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($name); ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">N0.MATRIK</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($matric); ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">EMAIL</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($email); ?>" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">NAMA PENYELIA</label>
                <input type="text" class="form-control"
                       value="<?= $penyelia_lulus ? htmlspecialchars($penyelia_lulus['nama_penyelia']) : 'Belum diluluskan'; ?>"
                       readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">TARIKH TEMUJANJI <span class="text-danger">*</span></label>
                <input type="date" name="tarikh" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">MASA <span class="text-danger">*</span></label>
                <input type="time" name="masa" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">TUJUAN TEMUJANJI <span class="text-danger">*</span></label>
            <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Bincang projek / Dapatkan khidmat nasihat" required>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="setuju" id="setuju" required>
            <label class="form-check-label" for="setuju">
                Saya bertanggungjawab sepenuhnya terhadap maklumat yang diberikan.
            </label>
        </div>

        <div class="d-flex justify-content-end gap-2">
    
            <button type="submit" class="btn btn-success"> HANTAR PERMOHONAN TEMUJANJI</button>
        </div>
    </form>

</div>
<script>
function confirmSubmit() {
    return confirm("Anda pasti mahu menghantar permohonan temujanji ini?");
}
</script>

</body>
</html>
