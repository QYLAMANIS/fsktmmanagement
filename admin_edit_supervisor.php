<?php
session_start();
require_once 'config.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';

if (!isset($_GET['id'])) {
    header("Location: admin_manage_supervisors.php");
    exit();
}

$id = $_GET['id'];

// Fetch current supervisor data including kuota
$stmt = $conn->prepare("SELECT id_penyelia, nama_penyelia, email, kuota FROM penyelia WHERE id_penyelia = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_manage_supervisors.php");
    exit();
}

$supervisor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $kuota = isset($_POST['kuota']) ? (int)$_POST['kuota'] : 5;

    // Check if email is used by other supervisor (exclude current id)
    $check = $conn->prepare("SELECT id_penyelia FROM penyelia WHERE email = ? AND id_penyelia != ?");
    $check->bind_param("ss", $email, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email sudah digunakan oleh penyelia lain.";
    } else {
        // Update supervisor
        $update = $conn->prepare("UPDATE penyelia SET nama_penyelia = ?, email = ?, kuota = ? WHERE id_penyelia = ?");
        $update->bind_param("ssis", $name, $email, $kuota, $id);

        if ($update->execute()) {
            header("Location: admin_manage_supervisors.php?update=success");
            exit();
        } else {
            $error = "Gagal kemaskini penyelia: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <title>Edit Penyelia</title>
        <style>
/* 🌿 Gaya utama */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f8;
    padding: 30px;
    font-size: 12px;
    color: #333;
}

/* Kad */
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
    margin: 20px auto;
    max-width: 600px;
}

/* Tajuk */
h2 {
    text-align: center;
    color: #0d3b66;
    margin-bottom: 20px;
}

/* Input */
label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}
input[type="text"],
input[type="email"],
input[type="file"] {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
    font-size: 13px;
}

/* Butang */
button {
    margin-top: 15px;
    width: 100%;
    background: #0d3b66;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px;
    font-size: 14px;
    cursor: pointer;
}
button:hover {
    background: #09325a;
}

/* Nota dan ralat */
.note {
    font-size: 11px;
    margin-top: 5px;
    color: #555;
}
.error {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}
</style>
</head>
<body>
 <div class="card">
<h2>EDIT PENYELIA</h2>

<?php if ($error): ?>
    <p class="error-message"><?= $error ?></p>
<?php endif; ?>

<form method="POST" action="">


    <label>NAMA PENYELIA:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($supervisor['nama_penyelia']); ?>" required>

    <label>EMAIL:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($supervisor['email']); ?>" required>

    <label>KUOTA PENYELIA:</label>
    <input type="number" name="kuota" min="1" value="<?= htmlspecialchars($supervisor['kuota']); ?>" required>

    <button type="submit">KEMASKINI</button>
</form>

</body>
</html>
</div>