<?php
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$url = "http://archive.uthm.edu.my/simple-search?query=" . urlencode($query);

// Ambil HTML dari laman arkib
$html = file_get_contents($url);

// Guna regex / DOMDocument untuk cari tajuk, pautan & metadata
$results = [];

// Contoh pseudo
preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $html, $matches);
for ($i = 0; $i < count($matches[1]); $i++) {
    $results[] = [
        'title' => strip_tags($matches[2][$i]),
        'link' => "http://archive.uthm.edu.my" . $matches[1][$i],
        'meta' => "Hasil carian daripada arkib UTHM"
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
