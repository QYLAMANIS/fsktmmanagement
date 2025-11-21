<?php
// fetch_archive_detail.php
header('Content-Type: application/json; charset=utf-8');

$link = $_GET['link'] ?? '';
if (!$link) {
  echo json_encode(['error' => 'Tiada pautan projek diberikan.']);
  exit;
}

// Cuba ambil HTML dari laman asal
$html = @file_get_contents($link);
if (!$html) {
  echo json_encode(['error' => 'Tidak dapat memuat turun halaman projek.']);
  exit;
}

// Ambil data utama
preg_match('/<title>(.*?)<\/title>/i', $html, $titleMatch);
preg_match('/dc.contributor.author">(.*?)<\/td>/i', $html, $creatorMatch);
preg_match('/dc.date.issued">(.*?)<\/td>/i', $html, $dateMatch);
preg_match('/dc.subject">(.*?)<\/td>/i', $html, $subjectMatch);
preg_match('/dc.publisher">(.*?)<\/td>/i', $html, $publisherMatch);
preg_match('/dc.description.abstract">(.*?)<\/td>/is', $html, $descMatch);

$data = [
  'title' => strip_tags($titleMatch[1] ?? 'Tiada Tajuk'),
  'creator' => strip_tags($creatorMatch[1] ?? 'Tidak Dinyatakan'),
  'date' => strip_tags($dateMatch[1] ?? '-'),
  'subject' => strip_tags($subjectMatch[1] ?? '-'),
  'publisher' => strip_tags($publisherMatch[1] ?? '-'),
  'description' => trim(strip_tags($descMatch[1] ?? 'Tiada abstrak tersedia.')),
  'link' => $link
];

echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
 <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">