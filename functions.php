<?php
require_once 'config.php';

/**
 * Fungsi untuk tambah notifikasi baru ke dalam database
 * @param mysqli $conn - sambungan DB
 * @param int $penerima_id - ID pengguna penerima
 * @param string $penerima_role - peranan penerima (pelajar/penyelia/admin)
 * @param string $mesej - mesej notifikasi
 * @param string|null $link - (optional) pautan untuk dibuka apabila klik notifikasi
 */
function add_notification($conn, $penerima_id, $penerima_role, $mesej, $link = null) {
    $sql = "INSERT INTO notifikasi (penerima_id, penerima_role, mesej, link)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("isss", $penerima_id, $penerima_role, $mesej, $link);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
?>
