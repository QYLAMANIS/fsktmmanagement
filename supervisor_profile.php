<?php
session_start();
require_once 'config.php';

// Semak login penyelia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyelia') {
    header('Location: login.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);
$query = "SELECT * FROM penyelia WHERE id_penyelia = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$supervisor = $result->fetch_assoc();

if (!$supervisor) {
    echo "Profil tidak dijumpai.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Profil Penyelia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ================================
   Modern Read-Only Profile Card
   ================================ */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f7fb;
    margin: 0;
    padding: 40px 20px;
    color: #1e293b;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.profile-card {
    max-width: 700px;
    width: 100%;
    background: #fff;
    border-radius: 15px;
    padding: 35px 25px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.12);
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
    margin-bottom: 15px;
}

.profile-header h2 {
    font-size: 24px;
    color: #002b5c;
    font-weight: 700;
    margin-bottom: 5px;
}

.profile-header p {
    color: #6b7280;
    font-size: 14px;
}

.profile-info {
    margin-top: 25px;
    text-align: left;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
    font-size: 15px;
}

.info-row span {
    font-weight: 600;
    color: #334155;
}

.cv-link {
    color: #2563eb;
    text-decoration: none;
}

.cv-link:hover {
    color: #1e40af;
    text-decoration: underline;
}

.btn-edit {
    display: inline-block;
    margin-top: 30px;
    padding: 10px 22px;
    background: #16a34a;
    color: #fff;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.3s ease-in-out;
}

.btn-edit:hover {
    background-color: #15803d;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .info-row {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
</head>
<body>

<div class="profile-card">
    <div class="profile-header">
        <?php if (!empty($supervisor['profile_image'])): ?>
            <img src="uploads/profile_images/<?= htmlspecialchars($supervisor['profile_image']) ?>" alt="Profile Image" class="profile-image">
        <?php else: ?>
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Default Profile" class="profile-image">
        <?php endif; ?>
        <h2><?= htmlspecialchars($supervisor['nama_penyelia'] ?? '-') ?></h2>
        <p><?= htmlspecialchars($supervisor['jawatan'] ?? '-') ?> | <?= htmlspecialchars($supervisor['course'] ?? '-') ?></p>
    </div>

    <div class="profile-info">
        <div class="info-row"><span>EMAIL:</span> <?= htmlspecialchars($supervisor['email'] ?? '-') ?></div>
        <div class="info-row"><span>NO.TELEFON:</span> <?= htmlspecialchars($supervisor['telefon'] ?? '-') ?></div>
        <div class="info-row"><span>JABATAN:</span> <?= htmlspecialchars($supervisor['jabatan'] ?? '-') ?></div>
        <div class="info-row"><span>KUOTA PELAJAR:</span> <?= htmlspecialchars($supervisor['kuota'] ?? '0') ?></div>
        <div class="info-row"><span>PAUTAN CV:</span>
            <?php if (!empty($supervisor['pautan_cv']) && filter_var($supervisor['pautan_cv'], FILTER_VALIDATE_URL)): ?>
                <a href="<?= htmlspecialchars($supervisor['pautan_cv']) ?>" target="_blank" class="cv-link">Lihat CV</a>
            <?php else: ?>
                Tiada
            <?php endif; ?>
        </div>
    </div>

    <a href="supervisor_edit_profil.php" class="btn-edit">KEMASKINI PROFIL</a>
</div>

</body>
</html>
