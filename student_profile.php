<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo "Pengguna belum log masuk.";
    exit();
}

// Dapatkan maklumat pelajar
$stmt = $conn->prepare("
    SELECT nama_pelajar, no_matrik, emel, program, telefon, profile_image 
    FROM pelajar 
    WHERE id_pelajar = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $matric_no, $email, $course, $phone, $profile_image);
$stmt->fetch();
$stmt->close();

// Gambar profil
if ($profile_image && file_exists(__DIR__.'/uploads/'.$profile_image)) {
    $image = 'uploads/'.$profile_image;
} else {
    $image = 'images/default.png';
}

// Dapatkan nama penyelia dan tajuk projek hanya jika permohonan diluluskan
$stmt2 = $conn->prepare("
    SELECT s.nama_penyelia, pm.tajuk_dipilih
    FROM permohonan pm
    JOIN penyelia s ON pm.id_penyelia = s.id_penyelia
    WHERE pm.id_pelajar = ? AND pm.status = 'Diluluskan'
    LIMIT 1
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$stmt2->bind_result($supervisor_name, $title);
$stmt2->fetch();
$stmt2->close();

// Jika tiada kelulusan, set kosong
$supervisor_name = $supervisor_name ?? '';
$title = $title ?? '';
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profil Pelajar</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #fdfbfb);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 100vh;
}

/* ✅ Mesej berjaya */
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 12px 20px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
    width: 80%;
    max-width: 620px;
    margin: 20px auto 0 auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    animation: fadeInDown 0.6s ease;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ✅ Kad profil */
.profile-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    width: 95%;
    max-width: 450px;
    padding: 25px 30px;
    text-align: center;
       justify-content: center;
    animation: fadeIn 0.7s ease;
    margin-top: 20px;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #2575fc;
    margin-bottom: 20px;
    box-shadow: 0 0 10px rgba(37,117,252,0.3);
}

h2 {
    font-size: 26px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
}

.info {
    text-align: left;
    margin: 15px auto;
    width: 100%;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row strong {
    color: #555;
    width: 160px;
    font-weight: 600;
}

.info-row span {
    color: #2c3e50;
}

.edit-btn {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 30px;
    background: linear-gradient(135deg, #2575fc, #6a11cb);
    color: #fff;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 12px rgba(37,117,252,0.4);
}

.edit-btn:hover {
    background: linear-gradient(135deg, #1a52d1, #5b0ead);
    box-shadow: 0 7px 20px rgba(26,82,209,0.5);
    transform: translateY(-2px);
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
}

@media (max-width: 600px) {
    .profile-card {
        padding: 30px 25px;
    }
    .info-row {
        flex-direction: column;
        align-items: flex-start;
    }
    .info-row strong {
        width: auto;
        margin-bottom: 5px;
    }
}

/* Scale view to 80% */
.container-scale {
    transform: scale(0.8);
    transform-origin: top center;
    width: 100%;
}

.scale-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 20px;
}

.container-scale {
    transform: scale(0.8);
    transform-origin: top center;
}

/* Wrapper untuk align dalam content kawasan dashboard */
.main-content {
    width: 100%;
    display: flex;
    justify-content: center; 
    align-items: flex-start;
    padding: 20px 0;
}

/* Wrapper untuk kecilkan view */
.scale-wrapper {
    transform: scale(0.85);
    transform-origin: top center;
}

/* Prevent overflow bila scale */
body {
    overflow-x: hidden;
}

</style>
</head>
<body>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert-success">
        ✅ Profil anda telah berjaya dikemaskini.
    </div>
<?php endif; ?>

<div class="main-content">
    <div class="scale-wrapper">
        <div class="profile-card">

        <img src="<?= htmlspecialchars($image) ?>" alt="Profile Image" class="profile-image">
        <h2><?= htmlspecialchars($name) ?></h2>

        <div class="info">
            <div class="info-row"><strong>NO.MATRIK:</strong><span><?= htmlspecialchars($matric_no) ?></span></div>
            <div class="info-row"><strong>EMAIL:</strong><span><?= htmlspecialchars($email) ?></span></div>
            <div class="info-row"><strong>PROGRAM:</strong><span><?= htmlspecialchars($course) ?></span></div>
            <div class="info-row"><strong>N0.TELEFON:</strong><span><?= htmlspecialchars($phone) ?></span></div>
            <div class="info-row"><strong>NAMA PENYELIA:</strong><span><?= htmlspecialchars($supervisor_name) ?></span></div>
            <div class="info-row"><strong>TAJUK PROJEK:</strong><span><?= htmlspecialchars($title) ?></span></div>
        </div>

        <a href="student_edit_profile.php" class="edit-btn">EDIT PROFIL</a>
        </div>
    </div>
</div>


</body>

</html>
