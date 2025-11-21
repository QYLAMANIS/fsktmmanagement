<?php
include 'config.php';

// Password baru
$new_password = "uthm";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Sediakan query update untuk semua pengguna
$sql = "UPDATE users SET password = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "✅ Semua password telah ditukar kepada 'uthm'.";
} else {
    echo "❌ Gagal kemas kini password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">