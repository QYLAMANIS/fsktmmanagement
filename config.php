<?php
$host = 'localhost';
$user = 'root';
$password = ''; // default kosong untuk XAMPP
$db = 'fsktmpsm'; // Pastikan ini nama DB anda
define('OPENAI_API_KEY', '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');


$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
