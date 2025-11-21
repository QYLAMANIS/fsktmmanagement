<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_manage_titles.php");
    exit;
}

$id = (int)$_GET['id'];
$upload_error = '';

// Dapatkan data asal
$result = $conn->query("SELECT * FROM sejarah_tajuk WHERE id = $id");
if (!$result || $result->num_rows === 0) {
    echo "Tajuk tidak dijumpai.";
    exit;
}
$psm = $result->fetch_assoc();

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $supervisor = $conn->real_escape_string($_POST['supervisor']);
    $year = $conn->real_escape_string($_POST['year']);
    $course = $conn->real_escape_string($_POST['course']);
    $authors = $conn->real_escape_string($_POST['authors']);

    $sql = "UPDATE sejarah_tajuk SET 
                title = '$title', 
                supervisor = '$supervisor', 
                year = '$year', 
                course = '$course', 
                authors = '$authors' 
            WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: admin_manage_titles.php");
        exit;
    } else {
        $upload_error = "Gagal kemaskini data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tajuk PSM</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; padding: 40px; }
        .container { background: white; padding: 30px; border-radius: 12px; max-width: 600px; margin: auto; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        form label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;
        }
        button {
            margin-top: 20px; padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;
        }
        button:hover { background-color: #2980b9; }
        .error { color: #e74c3c; margin-top: 15px; }
        a { display: inline-block; margin-top: 15px; color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h2>EDIT TAJUK PSM</h2>

    <?php if ($upload_error): ?>
        <p class="error"><?= htmlspecialchars($upload_error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="title">TAJUK</label>
        <input type="text" id="title" name="title" required value="<?= htmlspecialchars($psm['title']) ?>">

        <label for="supervisor">PENYELIA</label>
        <input type="text" id="supervisor" name="supervisor" required value="<?= htmlspecialchars($psm['supervisor']) ?>">

        <label for="year">TAHUN</label>
        <input type="number" id="year" name="year" min="2000" max="2100" required value="<?= htmlspecialchars($psm['year']) ?>">

        <label for="course">KURSUS</label>
        <input type="text" id="course" name="course" required value="<?= htmlspecialchars($psm['course']) ?>">

        <label for="authors">PELAJAR</label>
        <input type="text" id="authors" name="authors" required value="<?= htmlspecialchars($psm['authors']) ?>">

        <button type="submit">SIMPAN PERUBAHAN</button>
    </form>
</div>
</body>
</html>
