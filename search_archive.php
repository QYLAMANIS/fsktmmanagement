<?php
// --- BAHAGIAN PHP (PROSES CARIAN) ---
$results = "";
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $keyword = urlencode($_GET['q']);
    $url = "http://archive.uthm.edu.my/cgi/search/simple?q=" . $keyword;

    // Dapatkan data daripada archive.uthm.edu.my
    $context = stream_context_create(array(
        'http' => array('header' => "User-Agent: Mozilla/5.0\r\n")
    ));
    $html = @file_get_contents($url, false, $context);

    if ($html) {
        // Ambil hanya link hasil carian
        preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $html, $matches);
        if (!empty($matches[1])) {
            $results .= "<h3>Keputusan carian untuk: <em>" . htmlspecialchars($_GET['q']) . "</em></h3><ul>";
            for ($i = 0; $i < count($matches[1]); $i++) {
                $link = htmlspecialchars($matches[1][$i]);
                $title = htmlspecialchars(strip_tags($matches[2][$i]));
                // Pastikan link lengkap
                if (strpos($link, 'http') === false) {
                    $link = "http://archive.uthm.edu.my" . $link;
                }
                $results .= "<li><a href='$link' target='_blank'>$title</a></li>";
            }
            $results .= "</ul>";
        } else {
            $results = "Tiada hasil dijumpai.";
        }
    } else {
        $results = "Gagal dapatkan data dari UTHM Archive.";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Carian Arkib UTHM</title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 40px; text-align: center; }
    h2 { color: #003366; }
    form { margin-bottom: 20px; }
    input[type="text"] {
        width: 320px; padding: 10px;
        border: 1px solid #ccc; border-radius: 8px;
    }
    button {
        padding: 10px 15px;
        background-color: #003366; color: white;
        border: none; border-radius: 8px; cursor: pointer;
    }
    button:hover { background-color: #0055aa; }
    #results {
        text-align: left;
        margin: 0 auto;
        max-width: 700px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    a { color: #0066cc; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h2>🔍 Carian Arkib UTHM</h2>
<p>Taip tajuk atau kata kunci untuk cari artikel dari <strong>archive.uthm.edu.my</strong></p>

<form method="get" action="">
    <input type="text" name="q" placeholder="Contoh: Engineering, Thesis, Report..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
    <button type="submit">Cari</button>
</form>

<div id="results">
    <?= $results ?>
</div>

</body>
</html>
