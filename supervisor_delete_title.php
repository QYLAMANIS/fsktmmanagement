<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='penyelia'){
    header('Location: supervisor_login.php');
    exit();
}

$id_penyelia = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM tajuk_penyelia WHERE id=? AND id_penyelia=?");
$stmt->bind_param("ii",$id,$id_penyelia);
$stmt->execute();

header('Location: supervisor_titles.php');
exit();

