<?php
session_start();
require_once 'config.php';

// ✅ Semak login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
    header("Location: student_login.php");
    exit();
}


$id_pelajar = $_SESSION['user_id'];

// ✅ Dapatkan maklumat pelajar
$stmt = $conn->prepare("SELECT nama_pelajar, no_matrik FROM pelajar WHERE id_pelajar = ?");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$stmt->bind_result($nama_pelajar, $no_matrik);
$stmt->fetch();
$stmt->close();

$message = "";

// ✅ Proses Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chapter = $_POST['chapter'] ?? '';
    $type = $_POST['type'] ?? 'original'; // original atau corrected
    $file = $_FILES['report'] ?? null;

    if (!$chapter || !$file['name']) {
        $message = "Sila pilih chapter dan muat naik fail.";
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];

        if (!in_array($ext, $allowed)) {
            $message = "Hanya fail PDF/DOC/DOCX dibenarkan.";
        } else {
            $folder = "uploads/chapters/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $file_name = time() . "_" . basename($file['name']);
            $target = $folder . $file_name;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                if ($type == 'corrected') {
                    // ✅ Update file_corrected
                    $update = $conn->prepare("
                        UPDATE chapter 
                        SET file_corrected = ?, status = 'Dibaiki', submitted_at = NOW() 
                        WHERE id_pelajar = ? AND chapter = ?
                    ");
                    $update->bind_param("sis", $target, $id_pelajar, $chapter);
                    $update->execute();
                    $update->close();
                    $message = "Laporan $chapter telah dikemas kini (pembetulan).";
                } else {
                    // ✅ Semak jika chapter dah dihantar
                    $check = $conn->prepare("SELECT id FROM chapter WHERE id_pelajar = ? AND chapter = ?");
                    $check->bind_param("is", $id_pelajar, $chapter);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows > 0) {
                        $message = "Chapter ini sudah dihantar. Sila tunggu semakan penyelia atau buat pembetulan nanti.";
                    } else {
                        // ✅ Simpan laporan baru
                        $status = 'Menunggu Semakan';
                        $stmt = $conn->prepare("
                            INSERT INTO chapter (id_pelajar, chapter, chapter_file, status, submitted_at)
                            VALUES (?, ?, ?, ?, NOW())
                        ");
                        $stmt->bind_param("isss", $id_pelajar, $chapter, $target, $status);
                        $stmt->execute();
                        $stmt->close();
                        $message = "Laporan $chapter berjaya dihantar.";
                    }
                    $check->close();
                }
            } else {
                $message = "Gagal memuat naik fail.";
            }
        }
    }
}



// ✅ Senarai laporan pelajar
$reports = [];
$stmt = $conn->prepare("SELECT * FROM chapter WHERE id_pelajar = ? ORDER BY chapter ASC");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Hantar Laporan Projek Sarjana Muda (PSM)</title>

<style>
/* RESET SPACING */
body {
    background: #ffffff;
    margin: 0;
    padding: 0; 
    padding: 20px;
    font-family: Arial, sans-serif;
}

/* WRAPPER */
.container {
    width: 100%;
    max-width: 100% !important;
    margin: 0;
    padding: 0 15px; /* supaya tak rapat sangat ke tepi */
}


/* --- CARD SERAGAM --- */
.card-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    padding: 25px;
    border-radius: 0;
    margin-bottom: 30px;
    box-sizing: border-box;
    width: 100%;
}

/* Tajuk */
.card-box h4 {
    margin: 0 0 10px 0;
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
}

/* Subtitle */
.card-box p {
    margin-top: 5px;
    font-size: 14px;
    color: #4b5563;
}

/* Label */
.card-box label {
    margin-top: 12px;
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

/* Input */
.card-box select,
.card-box input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    border-radius: 8px;
    margin-top: 5px;
}

/* Button */
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

/* TABLE CLEAN */
.table-clean {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

/* Header putih */
.table-clean th {
    background: #0d3b66 !important;
    color: white !important;
    font-weight: 600;
    padding: 10px;
    border-bottom: 2px solid #093256;
}

/* Row */
.table-clean td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

/* Hover row */
.table-clean tr:hover td {
    background: #f9fafb;
}

/* Button kecil dalam table */
.table-btn {
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    color: #1f2937;
}

.table-btn:hover {
    background: #f3f4f6;
}

/* Samakan bentuk input file dengan select */
input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    border-radius: 12px; /* sama macam select */
    margin-top: 5px;
    font-size: 14px;

}
body, label, select, option, input, button, table, th, td, p, h1, h2, h3, h4 {
    color: #000000 !important;
}
.card-box h4 {
    text-align: center !important;
}

.card-box p {
    text-align: center !important;
}
.table-clean {
    width: 100%;
    border-collapse: collapse;
}

.table-clean th, .table-clean td {
    border: 1px solid #ccc; /* kotak */
    padding: 8px 10px;
    text-align: center;
}


</style>
</head>

<body>

<div class="card-box">
    <h4>HANTAR LAPORAN PROJEK SARJANA MUDA (PSM)</h4>
    <p>Pelajar perlu memuat naik laporan bagi setiap chapter untuk semakan penyelia.</p>

    <form method="POST" enctype="multipart/form-data">
        <label>PILIH LAPORAN</label>
        <select name="chapter" required>
            <option value="">-- PILIH LAPORAN --</option>
            <?php for($i=1;$i<=5;$i++): ?>
                <option>LAPORAN<?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label>MUAT NAIK LAPORAN</label>
        <input type="file" name="report" required>

        <button type="submit" class="btn-primary">HANTAR LAPORAN</button>
    </form>
</div>


<?php if(count($reports) > 0): ?>
<div class="card-box">
    <h4>SENARAI LAPORAN DIHANTAR</h4>

    <table class="table-clean">
        <thead>
            <tr>
                <th>LAPORAN</th>
                <th>LAPORAN ASAL</th>
                <th>STATUS</th>
                <th>DISEMAK PENYELIA</th>
                <th>TINDAKAN</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($reports as $r): ?>
            <tr>
                <td><?= $r['chapter'] ?></td>

                <td>
                    <?php if($r['chapter_file']): ?>
                        <a class="table-btn" href="<?= $r['chapter_file'] ?>" target="_blank">LIHAT</a>
                    <?php else: ?>
                        Tiada
                    <?php endif; ?>
                </td>

                <td><?= $r['status'] ?></td>

                <td>
                    <?php if($r['file_corrected']): ?>
                        <a class="table-btn" href="<?= $r['file_corrected'] ?>" target="_blank">LIHAT FAIL</a>
                    <?php else: ?>
                        Tiada
                    <?php endif; ?>
                </td>

               <td>
    <a class="table-btn" href="student_edit_chapter.php?id=<?= $r['id'] ?>" onclick="return confirmEdit();">EDIT</a>
    <a class="table-btn" href="student_delete_chapter.php?id=<?= $r['id'] ?>" onclick="return confirmDelete();">PADAM</a>
</td>

<script>
function confirmEdit() {
    return confirm("Anda pasti mahu mengubah bab ini?");
}

function confirmDelete() {
    return confirm("Anda pasti mahu memadam bab ini?");
}
</script>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    if (!confirm("Anda pasti mahu menghantar dokumen ini?")) {
        e.preventDefault();
    }
});
</script>


</body>
</html>
