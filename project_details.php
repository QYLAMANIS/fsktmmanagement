<?php
$link = $_GET['link'] ?? '';

if (!$link) {
  echo "<h2 style='text-align:center;color:red;'>⚠️ Tiada pautan projek diberikan.</h2>";
  exit;
}

$html = @file_get_contents($link);
if (!$html) {
  echo "<h2 style='text-align:center;color:red;'>⚠️ Tidak dapat memuatkan butiran projek.</h2>";
  exit;
}

// ===== Extract data dari laman asal =====
function getMatch($pattern, $html) {
  return preg_match($pattern, $html, $m) ? trim(strip_tags($m[1])) : '';
}

$title = getMatch('/<h2[^>]*class="page-header"[^>]*>(.*?)<\/h2>/i', $html);
$abstract = getMatch('/<div[^>]*class="simple-item-view-description"[^>]*>(.*?)<\/div>/is', $html);
$authors = [];
if (preg_match_all('/<a href="\/browse\?type=author[^"]*"[^>]*>(.*?)<\/a>/i', $html, $m)) {
  $authors = array_map('strip_tags', $m[1]);
}
$keywords = getMatch('/Keywords:<\/td>\s*<td[^>]*>(.*?)<\/td>/i', $html);
$issue_date = getMatch('/Issue Date:<\/td>\s*<td[^>]*>(.*?)<\/td>/i', $html);
$uri = getMatch('/<a href="(http:\/\/hdl.handle.net\/[^"]+)"/i', $html);
$collection = getMatch('/Appears in Collections:[^<]*<a[^>]*>(.*?)<\/a>/i', $html);

if (preg_match('/<a href="(\/bitstream\/[^"]+)"[^>]*>([^<]+)<\/a>/i', $html, $m)) {
  $file_url = "http://archive.uthm.edu.my" . $m[1];
  $file_name = $m[2];
} else {
  $file_url = '';
  $file_name = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?: "Project Details") ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f3f6fb; }
  </style>
</head>
<body class="min-h-screen flex flex-col items-center py-10">

  <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
    <div class="bg-[#00549A] text-white px-6 py-3 font-semibold">
      PTTA Digital Archive / Undergraduate Project Report
    </div>

    <div class="flex flex-col md:flex-row">
      <!-- LEFT CONTENT -->
      <div class="flex-1 p-6">
        <div class="bg-gray-100 border border-gray-200 p-3 text-sm mb-4 rounded">
          Please use this identifier to cite or link to this item:
          <a href="<?= htmlspecialchars($uri) ?>" class="text-[#b71c1c] hover:underline ml-1"><?= htmlspecialchars($uri) ?></a>
        </div>

        <table class="w-full text-sm mb-4">
          <tr class="border-b">
            <td class="font-semibold w-32 py-2">Title:</td>
            <td><?= htmlspecialchars($title ?: '-') ?></td>
          </tr>
          <tr class="border-b">
            <td class="font-semibold py-2">Authors:</td>
            <td>
              <?= $authors ? implode(', ', array_map(fn($a)=>"<span class='text-blue-700'>$a</span>", $authors)) : '-' ?>
            </td>
          </tr>
          <tr class="border-b">
            <td class="font-semibold py-2">Keywords:</td>
            <td><?= htmlspecialchars($keywords ?: '-') ?></td>
          </tr>
          <tr class="border-b">
            <td class="font-semibold py-2">Issue Date:</td>
            <td><?= htmlspecialchars($issue_date ?: '-') ?></td>
          </tr>
          <tr class="align-top">
            <td class="font-semibold py-2">Abstract:</td>
            <td class="text-justify leading-relaxed"><?= nl2br(htmlspecialchars($abstract ?: '-')) ?></td>
          </tr>
        </table>

        <?php if ($file_url): ?>
        <div class="mt-4 p-3 bg-gray-100 rounded border border-gray-200 flex justify-between items-center">
          <div>
            <p class="font-medium text-gray-700"><?= htmlspecialchars($file_name) ?></p>
          </div>
          <a href="<?= htmlspecialchars($file_url) ?>" target="_blank" class="px-4 py-2 bg-[#b71c1c] text-white rounded hover:bg-[#a31414]">
            📄 View/Open
          </a>
        </div>
        <?php endif; ?>

        <div class="mt-4 text-sm">
          <p><strong>URI:</strong> <a href="<?= htmlspecialchars($uri) ?>" class="text-blue-700 hover:underline"><?= htmlspecialchars($uri) ?></a></p>
          <p><strong>Appears in Collections:</strong> <?= htmlspecialchars($collection ?: '-') ?></p>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="w-full md:w-1/4 p-4 space-y-4">
        <div class="bg-green-500 text-white rounded p-4 text-center shadow">
          <p class="font-semibold text-lg">👁 Page view(s)</p>
          <p class="text-2xl font-bold mt-1">29</p>
          <p class="text-xs mt-1">checked on Oct 30, 2025</p>
        </div>
        <div class="bg-red-600 text-white rounded p-4 text-center shadow">
          <p class="font-semibold text-lg">⬇ Download(s)</p>
          <p class="text-2xl font-bold mt-1">42</p>
          <p class="text-xs mt-1">checked on Oct 30, 2025</p>
        </div>
        <div class="bg-blue-600 text-white rounded p-4 text-center shadow">
          <p class="font-semibold text-lg">Google Scholar™</p>
          <a href="https://scholar.google.com/" target="_blank" class="underline">Check</a>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-8 text-xs text-gray-500">
    © 2025 UTHM | AI Search Prototype
  </footer>
</body>
</html>
