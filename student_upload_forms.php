<?php
session_start();
require_once 'config.php';

// ✅ Semak login pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: student_login.php');
    exit();
}

$id_pelajar = $_SESSION['user_id'];
$submission_id = null;

// ✅ Pastikan ada permohonan diluluskan
$stmt = $conn->prepare("SELECT id FROM permohonan WHERE id_pelajar=? AND status='Diluluskan' ORDER BY tarikh_hantar DESC LIMIT 1");
$stmt->bind_param("i", $id_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$submission = $result->fetch_assoc();
if (!$submission) {
    $_SESSION['error'] = "Tiada permohonan diluluskan untuk muat naik dokumen.";
    header('Location: student_view_submission.php');
    exit();
}
$submission_id = $submission['id'];

// ✅ Folder simpanan
$uploadDir = "uploads/student_$id_pelajar/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ✅ Fail-fail yang perlu diupload
$files = ['formA', 'formB', 'formC'];
$updateFields = [];

foreach ($files as $fileKey) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $originalName = basename($_FILES[$fileKey]['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Pastikan PDF
        if ($ext !== 'pdf') {
            $_SESSION['error'] = "Semua borang mesti dalam format PDF.";
            header('Location: student_view_submission.php');
            exit();
        }

        $newFileName = $fileKey . "_" . time() . ".pdf"; // contoh: formA_1697778888.pdf
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
            $updateFields[$fileKey] = $targetPath;
        } else {
            $_SESSION['error'] = "Gagal upload $fileKey.";
            header('Location: student_view_submission.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Sila pilih semua borang sebelum menghantar.";
        header('Location: student_view_submission.php');
        exit();
    }
}

// ✅ Update database
$setStr = implode(", ", array_map(fn($k) => "$k=?", array_keys($updateFields)));
$stmtUpdate = $conn->prepare("UPDATE permohonan SET $setStr WHERE id=? AND id_pelajar=?");

$params = array_merge(array_values($updateFields), [$submission_id, $id_pelajar]);
$types = str_repeat("s", count($updateFields)) . "ii";

// Bind param dinamik
$stmtUpdate->bind_param($types, ...$params);
if ($stmtUpdate->execute()) {
    $_SESSION['success'] = "Semua dokumen berjaya dimuat naik!";
} else {
    $_SESSION['error'] = "Gagal menyimpan maklumat dokumen.";
}

$stmtUpdate->close();
header('Location: student_view_submission.php');
exit();
?>
