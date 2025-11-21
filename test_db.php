<?php
$host = "sql213.infinityfree.com";
$user = "if0_40369403";
$password = "FsktmPsm1";
$db = "if0_40369403_fsktmpsm";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Database connected successfully!";
}
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">