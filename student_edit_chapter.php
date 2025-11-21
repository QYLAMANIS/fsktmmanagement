<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar'){
    header("Location: student_login.php");
    exit();
}

$id_pelajar = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

// Ambil data chapter
$stmt = $conn->prepare("SELECT * FROM chapter WHERE id=? AND id_pelajar=?");
$stmt->bind_param("ii", $id, $id_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$chapter = $result->fetch_assoc();
$stmt->close();

if(!$chapter){
    die("Chapter tidak dijumpai.");
}

$message = "";

// Proses kemas kini
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $chapter_name = $_POST['chapter'] ?? '';
    $file = $_FILES['report'] ?? null;

    if(!$chapter_name || !$file['name']){
        $message = "Sila pilih chapter dan muat naik fail baru.";
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf','doc','docx'];

        if(!in_array($ext, $allowed)){
            $message = "Format fail tidak sah. Hanya PDF/DOC/DOCX dibenarkan.";
        } else {
            $folder = "uploads/chapters/";
            if(!is_dir($folder)) mkdir($folder, 0777, true);

            $file_name = time() . "_" . basename($file['name']);
            $target = $folder . $file_name;

            if(move_uploaded_file($file['tmp_name'], $target)){
                $stmt = $conn->prepare("UPDATE chapter SET chapter=?, chapter_file=?, status='Menunggu Semakan', submitted_at=NOW() WHERE id=? AND id_pelajar=?");
                $stmt->bind_param("ssii", $chapter_name, $target, $id, $id_pelajar);
                $stmt->execute();
                $stmt->close();

                header("Location: student_upload_report.php?success=1");
                exit();
            } else {
                $message = "Gagal muat naik fail.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan Chapter</title>

    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* RESET */
body {
    background: #ffffff;
    margin: 0;
    padding: 20px;
    font-family: Arial, sans-serif;
}

/* Container */
.container {
    width: 100%;
    max-width: 100% !important;
    padding: 0 15px;
}

/* --- CARD SAMA MACAM PAGE UTAMA --- */
.card-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    padding: 25px;
    border-radius: 0;
    margin-bottom: 30px;
    box-sizing: border-box;
    width: 100%;
}

/* Title */
.card-box h4 {
    margin: 0 0 10px 0;
    font-size: 20px;
    font-weight: 600;
    color: #000;
    text-align: center;
}

/* Subtitle */
.card-box p {
    margin-top: 5px;
    font-size: 14px;
    color: #4b5563;
    text-align: center;
}

/* Labels */
.card-box label {
    margin-top: 12px;
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #000;
}

/* Inputs */
.card-box input[type="text"],
.card-box input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    border-radius: 8px;
    margin-top: 5px;
    font-size: 14px;
}

/* Button sama */
.btn-primary {
    margin-top: 18px;
    padding: 10px 20px;
    width: auto;
    border: none;
    border-radius: 8px;
    background-color: #0d3b66;
    color: white !important;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

</style>

</head>
<body>

<div class="container">
    
    <div class="card-box">
        <h4>KEMASKINI LAPORAN PSM</h4>
        <p>Sila kemas kini maklumat laporan dan muat naik fail baharu.</p>

        <?php if($message): ?>
            <div class="alert alert-danger" style="text-align:center;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Nama Chapter</label>
            <input type="text" name="chapter" 
                   value="<?= htmlspecialchars($chapter['chapter']) ?>" required>

            <label>Muat Naik Fail Baru</label>
            <input type="file" name="report" accept=".pdf,.doc,.docx" required>

            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>
    </div>

</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    if (!confirm("Anda pasti mahu menyimpan perubahan laporan ini?")) {
        e.preventDefault();
    }
});
</script>


</body>
</html>
