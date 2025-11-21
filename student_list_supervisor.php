<?php
session_start();
require_once 'config.php';

// Semak login pelajar
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='pelajar'){
    header('Location: student_login.php');
    exit();
}

// Ambil input search & course filter
$search = $_GET['search'] ?? '';
$selected_course = $_GET['course'] ?? '';
$param = "%$search%";

// Query penyelia dengan bil pelajar diluluskan
$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM permohonan WHERE id_penyelia=p.id_penyelia AND status='diluluskan') AS bil_pelajar 
        FROM penyelia p
        WHERE 1=1";
$params = []; $types='';

if($search!==''){ $sql.=" AND p.nama_penyelia LIKE ?"; $params[]=$param; $types.='s'; }
if($selected_course!==''){ $sql.=" AND p.course=?"; $params[]=$selected_course; $types.='s'; }

$sql.=" ORDER BY p.nama_penyelia ASC";

$stmt = $conn->prepare($sql);
if(!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<meta charset="UTF-8">
<title>Senarai Penyelia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ===== RESET & BASE ===== */
body {
    font-family: 'Segoe UI', sans-serif;
    font-size: 12px;
    background: #f4f6f8;
    margin: 0;
    padding: 30px;
    color: #222;
}

/* ===== CONTAINER ===== */
.container {
    max-width: 1100px;
    margin: auto;
    padding: 25px;
    border-radius: 10px;
}

/* ===== SEARCH BAR ===== */
.search-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.search-bar input, .search-bar select, .search-bar button {
    font-size: 12px;
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.search-bar button {
    background: #0d3b66;
    color: white;
    border: none;
}
.search-bar button:hover {
    background: #0a3054;
}

/* ===== KAD PENYELIA ===== */
.card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    text-align: center;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #0d3b66;
    margin-bottom: 8px;
}

h5 {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #0d3b66;
}
p {
    font-size: 12px;
    margin-bottom: 4px;
    color: #333;
}

/* ===== BUTTONS ===== */
.btn-pilih, .btn-penuh, .btn-info {
    display: block;
    width: 100%;
    border: none;
    border-radius: 5px;
    padding: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-pilih {
    background: #0d3b66;
    color: #fff;
}
.btn-pilih:hover {
    background: #0a3054;
}
.btn-info {
    background: #f1f3f5;
    color: #0d3b66;
    border: 1px solid #ccc;
}
.btn-info:hover {
    background: #e9ecef;
}
.btn-penuh {
    background: #ccc;
    color: #fff;
    cursor: not-allowed;
}

/* ===== MODAL ===== */
.supervisor-modal {
    border-radius: 10px;
    border: 1px solid #ddd;
}
.supervisor-modal .modal-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
.supervisor-avatar {
    width: 160px;
    height: 130px;
    border-radius: 50%;
    border: 2px solid #0d3b66;
    margin-bottom: 10px;
    object-fit: cover; /* penting supaya muka tak herot */
}

.supervisor-info p {
    font-size: 12px;
    margin-bottom: 4px;
}
.cv-link {
    color: #0d3b66;
    text-decoration: none;
    font-weight: 500;
}
.cv-link:hover { text-decoration: underline; }

/* ===== MODAL: PROFIL PENYELIA (DIKEMASKINI) ===== */

/* Saiz modal dikurangkan ke 80% daripada asal */
.supervisor-modal {
    border-radius: 10px;
    border: 1px solid #ddd;
    transform: scale(0.8); /* Kecilkan keseluruhan modal */
    transform-origin: center center; /* Pastikan scaling dari tengah */
}

/* Pusatkan semua kandungan dalam modal */
.supervisor-modal .modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Gambar lebih kecil dan kemas di tengah */
.supervisor-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 3px solid #0d3b66;
    object-fit: cover;
    margin: 0 auto 10px auto;
}

/* Tajuk dan teks sejajar di tengah */
.supervisor-modal h4,
.supervisor-modal p {
    text-align: center;
}

/* Info box kemas & tengah */
.supervisor-info {
    width: 80%;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    text-align: left;
    line-height: 1.5;
}
/* ===== AVATAR DALAM KAD ===== */
.avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #0d3b66;
    display: block;
    margin: 0 auto 8px auto; /* Tengah secara mendatar */
}
/* ===== FIX MODAL TENGAH BETUL ===== */
.modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    margin: 0 !important; /* Buang margin default bootstrap */
    transform: scale(0.8); /* View 80% supaya tak besar sangat */
}

.modal-content {
    width: 80%; /* Kecilkan saiz view modal */
    max-width: 800px; /* Optional: hadkan max lebar */
    border-radius: 10px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .card { font-size: 11px; }
    .search-bar { flex-direction: column; }
}
</style>


</head>
<body>
<div class="container">

<!-- Search Form -->
<div class="search-bar">
<form method="get" class="row g-2">
  <div class="col-md-6">
    <input type="text" name="search" class="form-control" placeholder="Cari nama penyelia..." value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-md-4">
    <select name="course" class="form-select">
      <option value="">PROGRAM</option>
      <?php 
      $courses = ['BIT','BIW','BIS','BIP','BIM'];
      foreach($courses as $c){
          $selected = ($selected_course === $c) ? 'selected' : '';
          echo "<option value=\"$c\" $selected>$c</option>";
      }
      ?>
    </select>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">CARI</button>
  </div>
</form>
</div>

<?php if($result->num_rows>0): ?>
<div class="row g-4">
<?php while($row=$result->fetch_assoc()):
    $kuota = $row['kuota'] ?? 5;
    $kosong = max($kuota - $row['bil_pelajar'], 0);
    $img_path = !empty($row['profile_image']) ? 'uploads/profile_images/'.$row['profile_image'] : 'images/default_avatar.png';
?>
  <div class="col-md-4">
    <div class="card">
      <img src="<?= htmlspecialchars($img_path) ?>" alt="Avatar" class="avatar">
      <h5><?= htmlspecialchars($row['nama_penyelia']) ?></h5>
      <p>BIDANG: <?= htmlspecialchars($row['course']) ?></p>
      <p>KUOTA: <?= $row['bil_pelajar']."/".$kuota ?> | KEKOSONGAN: <?= $kosong ?></p>

      <!-- Butang Lihat (modal) -->
      <button type="button" class="btn btn-info mb-2 w-100" data-bs-toggle="modal" data-bs-target="#modal<?= $row['id_penyelia'] ?>">LIHAT</button>

      <!-- FORM Pilih Penyelia (POST ke student_choose_supervisor.php) -->
      <?php if($kosong>0): ?>
  <form method="post" action="student_choose_supervisor.php">
    <input type="hidden" name="id_penyelia" value="<?= $row['id_penyelia'] ?>">
    <button type="submit" class="btn-pilih w-100">PILIH PENYELIA</button>
</form>

      <?php else: ?>
        <button class="btn-penuh w-100" disabled>PENUH</button>
      <?php endif; ?>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="modal<?= $row['id_penyelia'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id_penyelia'] ?>" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-xl">

      <div class="modal-content supervisor-modal text-center">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-primary w-100 text-center">PROFIL PENYELIA</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-4">
            <img src="<?= htmlspecialchars($img_path) ?>" class="supervisor-avatar mb-3" alt="Profile Image">
            <h4 class="fw-semibold mb-1"><?= htmlspecialchars($row['nama_penyelia']) ?></h4>
            <p class="text-muted mb-0"><?= htmlspecialchars($row['jawatan'] ?? '-') ?> | <?= htmlspecialchars($row['course'] ?? '-') ?></p>
          </div>

          <div class="supervisor-info">
            <p><strong>EMAIL:</strong> <?= htmlspecialchars($row['email'] ?? '-') ?></p>
            <p><strong>N0.TELEFON:</strong> <?= htmlspecialchars($row['telefon'] ?? '-') ?></p>
            <p><strong>JABATAN:</strong> <?= htmlspecialchars($row['jabatan'] ?? '-') ?></p>
            <p><strong>KUOTA PELAJAR:</strong> <?= htmlspecialchars($row['bil_pelajar']."/".$row['kuota']) ?></p>
            <p><strong>PAUTAN CV:</strong>
              <?php if (!empty($row['pautan_cv']) && filter_var($row['pautan_cv'], FILTER_VALIDATE_URL)): ?>
                <a href="<?= htmlspecialchars($row['pautan_cv']) ?>" target="_blank" class="cv-link">LIHAT CV</a>
              <?php else: ?>
                Tiada
              <?php endif; ?>
            </p>
          </div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">TUTUP</button>
        </div>
      </div>
    </div>
  </div>





<?php endwhile; ?>
</div>
<?php else: ?>
<p>Tiada penyelia ditemui.</p>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

<link rel="stylesheet" href="assets/css/style.css">

</html>
