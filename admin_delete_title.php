<?php
$upload_dir = 'uploads/';
$csv_file = $upload_dir . 'data.csv';

// dapatkan parameter tajuk nak padam dari URL (GET)
if (!isset($_GET['title'])) {
    die("Tiada tajuk dipilih untuk dipadam.");
}

$title_to_delete = $_GET['title'];

// baca semua data dari CSV
function parse_csv_file($filepath) {
    $data = [];
    if (($handle = fopen($filepath, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== FALSE) {
            $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

$data = parse_csv_file($csv_file);

// tapis keluar rekod yang tajuknya sama dengan yang nak delete
$new_data = array_filter($data, function($row) use ($title_to_delete) {
    return $row['title'] !== $title_to_delete;
});

// tulis semula ke CSV
if (($handle = fopen($csv_file, "w")) !== FALSE) {
    // tulis header dulu
    if (!empty($data)) {
        fputcsv($handle, array_keys($data[0]));
    }
    // tulis data baru
    foreach ($new_data as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
    header("Location: admin_manage_titles.php?msg=delete_success");
    exit();
} else {
    die("Gagal buka fail untuk ditulis.");
}

