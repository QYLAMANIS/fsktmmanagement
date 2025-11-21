<?php
session_start();
require_once "config.php"; // your DB connection (mysqli $conn)

// Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ensure uploads dir exists
$uploadDir = __DIR__ . '/uploads/info/';
$webUploadDir = 'uploads/info/'; // relative url used in <img src=...>
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ---------------------------
   HANDLE ADD (POST)
   form uses name="action" value="add" for adding
   --------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $link = trim($_POST['link'] ?? '');
    $info_type = trim($_POST['info_type'] ?? ''); // optional, kept for compatibility
    $attachmentName = null; // generic file (pdf/doc) (stored in `attachment`)
    $imageName = null;      // image file (stored in `image`)

    // handle generic attachment (optional)
    if (!empty($_FILES['attachment']['name'])) {
        $orig = basename($_FILES['attachment']['name']);
        $attachmentName = time() . "_att_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $attachmentName);
    }

    // handle image upload (optional)
    if (!empty($_FILES['image']['name'])) {
        $orig = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $imageName = time() . "_img_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        } else {
            $_SESSION['error'] = "Jenis fail gambar tidak disokong. Gunakan jpg / png / gif / webp.";
            header("Location: admin_info.php");
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO info (title, content, link, attachment, image, info_type, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $title, $content, $link, $attachmentName, $imageName, $info_type);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Maklumat berjaya ditambah.";
    header("Location: admin_info.php");
    exit();
}

/* ---------------------------
   HANDLE UPDATE (POST)
   form uses name="action" value="edit"
   --------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $link = trim($_POST['link'] ?? '');
    $info_type = trim($_POST['info_type'] ?? '');

    // fetch existing to manage files
    $existing = $conn->query("SELECT attachment, image FROM info WHERE id = $id")->fetch_assoc();
    $attachmentName = $existing['attachment'] ?? null;
    $imageName = $existing['image'] ?? null;

    // handle new attachment replacement
    if (!empty($_FILES['attachment']['name'])) {
        // remove old attachment if exists
        if ($attachmentName && file_exists($uploadDir . $attachmentName)) {
            @unlink($uploadDir . $attachmentName);
        }
        $orig = basename($_FILES['attachment']['name']);
        $attachmentName = time() . "_att_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $attachmentName);
    }

    // handle new image replacement
    if (!empty($_FILES['image']['name'])) {
        // remove old image if exists
        if ($imageName && file_exists($uploadDir . $imageName)) {
            @unlink($uploadDir . $imageName);
        }
        $orig = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = "Jenis fail gambar tidak disokong. Gunakan jpg / png / gif / webp.";
            header("Location: admin_info.php");
            exit();
        }
        $imageName = time() . "_img_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    $stmt = $conn->prepare("UPDATE info SET title = ?, content = ?, link = ?, attachment = ?, image = ?, info_type = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $title, $content, $link, $attachmentName, $imageName, $info_type, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Maklumat berjaya dikemaskini.";
    header("Location: admin_info.php");
    exit();
}

/* ---------------------------
   HANDLE DELETE (GET)
   --------------------------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT attachment, image FROM info WHERE id = $id")->fetch_assoc();
    if ($res) {
        if (!empty($res['attachment']) && file_exists($uploadDir . $res['attachment'])) {
            @unlink($uploadDir . $res['attachment']);
        }
        if (!empty($res['image']) && file_exists($uploadDir . $res['image'])) {
            @unlink($uploadDir . $res['image']);
        }
    }
    $stmt = $conn->prepare("DELETE FROM info WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Maklumat dipadam.";
    header("Location: admin_info.php");
    exit();
}

/* ---------------------------
   FETCH ALL DATA
   --------------------------- */
$result = $conn->query("SELECT * FROM info ORDER BY created_at DESC");
$all = $result->fetch_all(MYSQLI_ASSOC);

// If editing (show edit mode), grab that row
$editRow = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM info WHERE id = $eid")->fetch_assoc();
}

// helper for escape output
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta charset="utf-8">
<title>Admin Info Management</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    :root{
        --primary:#b30000;
        --accent:#f8f0f0;
        --card-bg:#ffffff;
        --muted:#666;
    }
    body {
  font-family: 'Poppins', sans-serif;
  background:#fafafa;
  margin:0;
  padding:20px;
  color:#222;
  font-size: 13px; /* tambah ni */
}
body {
  transform: scale(0.9);
  transform-origin: top center;
}

.form-box, .card {
  transform: scale(0.95);
  transform-origin: top left;
}


    h1 { text-align:center; color:var(--primary); margin-bottom:18px; font-weight:600; }
    .top-row { display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; }
    /* form box */
    .form-box { flex:1 1 380px; background:var(--card-bg); padding:18px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); }
    .form-box h2{ margin:0 0 10px 0; font-size:18px; color:#333; }
    input[type="text"], input[type="url"], textarea, select { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #ddd; margin-bottom:10px; font-size:14px; }
    label { display:block; font-weight:600; margin-bottom:6px; font-size:13px; color:#333; }
    .small { font-size:13px; color:var(--muted); margin-top:-6px; margin-bottom:10px; }

    .btn { display:inline-block; padding:10px 14px; border-radius:8px; border:none; cursor:pointer; font-weight:600; }
    .btn-primary { background:var(--primary); color:#fff; }
    .btn-danger { background:#dc3545; color:#fff; }
    .btn-muted { background:#f0f0f0; color:#333; border:1px solid #e0e0e0; }

    /* preview image */
.img-preview { 
    width:100%; 
    height:auto; 
    padding:10px; 
    background:#fff; 
    border:1px solid #ddd; 
    border-radius:10px; 
    display:flex; 
    justify-content:center; 
    align-items:center;
}

.img-preview img { 
    width:100%; 
    height:auto; 
    object-fit:contain; 
}

    /* items grid */
.grid {
    width:100%;
    display:flex;
    flex-direction:column;
    gap:18px;
}

    .card { background:var(--card-bg); border-radius:12px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,0.05); display:flex; flex-direction:column; }
  .card .card-img { 
    width:100%; 
    height:auto; 
    background:#fff; 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    padding:10px;
}

.card .card-img img { 
    width:100%; 
    height:auto; 
    object-fit:contain; 
    display:block; 
}

    .card .card-body { padding:14px; flex:1; display:flex; flex-direction:column; }
    .card h3 { margin:0 0 8px 0; font-size:18px; color:var(--primary); }
    .card .meta { color:var(--muted); font-size:13px; margin-bottom:10px; }
    .card .content { flex:1; color:#333; font-size:14px; margin-bottom:12px; }
    .card .links a { display:inline-block; margin-right:8px; color:var(--primary); text-decoration:none; font-weight:600; }
    .card .card-actions { padding:12px; border-top:1px solid #f1f1f1; background:#fafafa; display:flex; justify-content:space-between; align-items:center; }
    .card .card-actions .left { color:#555; font-size:13px; }

    .msg { padding:10px 12px; border-radius:8px; margin-bottom:12px; }
    .msg-success { background:#e8f6ee; color:#106246; border:1px solid #cfead3; }
    .msg-error { background:#fdecea; color:#611212; border:1px solid #f5c6cb; }
    @media(max-width:880px){ .top-row { flex-direction:column; } .img-preview{height:180px}.card .card-img{height:200px} }
</style>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>
<body>
<div class="container">


    <!-- messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="msg msg-success"><?= h($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="msg msg-error"><?= h($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="top-row">
        <!-- ADD / EDIT FORM -->
        <div class="form-box">
            <?php if ($editRow): ?>
                <h2>Edit Maklumat</h2>
            <?php else: ?>
                <h2>Tambah Maklumat Baru</h2>
            <?php endif; ?>

            <!-- preview box (shows either existing image for edit, or preview for chosen file) -->
            <div class="img-preview" id="imgPreviewBox">
                <?php if ($editRow && !empty($editRow['image']) && file_exists($uploadDir . $editRow['image'])): ?>
                    <img id="previewImg" src="<?= h($webUploadDir . $editRow['image']) ?>" alt="preview">
                <?php else: ?>
                    <img id="previewImg" src="" alt="preview" style="display:none;">
                    <div id="previewPlaceholder" style="color:#888; font-weight:600;">Preview Image</div>
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data" id="theForm">
                <?php if ($editRow): ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= intval($editRow['id']) ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="add">
                <?php endif; ?>

                <label>Title</label>
                <input type="text" name="title" required value="<?= $editRow ? h($editRow['title']) : '' ?>">

                <label>Content</label>
                <textarea name="content" id="editor"><?= $editRow ? h($editRow['content']) : '' ?></textarea>

                <label>Optional Link</label>
                <input type="url" name="link" placeholder="https://example.com" value="<?= $editRow ? h($editRow['link']) : '' ?>">

                <label>Info Type (optional)</label>
                <input type="text" name="info_type" placeholder="e.g. PSM / Announcement" value="<?= $editRow ? h($editRow['info_type']) : '' ?>">

                <label>Upload Image (jpg/png/gif/webp) — replace if editing</label>
                <input type="file" name="image" id="inputImage" accept="image/*">

                <label>Other Attachment (pdf/doc/zip) — optional</label>
                <input type="file" name="attachment" id="inputAttachment">

                <div style="margin-top:12px; display:flex; gap:8px;">
                    <?php if ($editRow): ?>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="admin_info.php" class="btn btn-muted" style="text-decoration:none; display:inline-flex; align-items:center;">Batal</a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary">Tambah Maklumat</button>
                    <?php endif; ?>
                </div>
            </form>
            <div class="small" style="margin-top:10px;">Tip: Upload gambar besar supaya paparan card nampak cantik. Saiz gambar automatik diubah (object-fit: cover).</div>
        </div>

        <!-- CARDS GRID -->
        <div class="grid">
            <?php if (empty($all)): ?>
                <div style="padding:18px; background:#fff; border-radius:10px;">Tiada maklumat lagi.</div>
            <?php else: ?>
                <?php foreach ($all as $row): ?>
                    <div class="card">
                        <div class="card-img">
                            <?php if (!empty($row['image']) && file_exists($uploadDir . $row['image'])): ?>
                                <img src="<?= h($webUploadDir . $row['image']) ?>" alt="<?= h($row['title']) ?>">
                            <?php else: ?>
                                <div style="padding:12px; color:#999; text-align:center;">Tiada gambar</div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h3><?= h($row['title']) ?></h3>
                            <div class="meta"><?= h(date("d M Y, H:i", strtotime($row['created_at']))) ?> · <?= $row['info_type'] ? h($row['info_type']) : 'General' ?></div>
                            <div class="content"><?= $row['content'] ?></div>

                            <div class="links">
                                <?php if (!empty($row['link'])): ?>
                                    <a href="<?= h($row['link']) ?>" target="_blank">🔗 Buka Link</a>
                                <?php endif; ?>
                                <?php if (!empty($row['attachment']) && file_exists($uploadDir . $row['attachment'])): ?>
                                    <a href="<?= h($webUploadDir . $row['attachment']) ?>" download>📎 Muat Turun</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-actions">
                   
                            <div>
                                <a class="btn btn-muted" href="?edit=<?= intval($row['id']) ?>">Edit</a>
                                <a class="btn btn-danger" style="text-decoration:none; margin-left:6px;"
                                   href="?delete=<?= intval($row['id']) ?>"
                                   onclick="return confirm('Padam maklumat ini? Semua fail berkaitan akan dipadam.')">Padam</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
/* CKEditor for content */
CKEDITOR.replace('editor');

/* Image preview logic */
const inputImage = document.getElementById('inputImage');
const previewImg = document.getElementById('previewImg');
const previewPlaceholder = document.getElementById('previewPlaceholder');

inputImage && inputImage.addEventListener('change', function(e){
    const f = this.files[0];
    if (!f) return;
    const url = URL.createObjectURL(f);
    previewPlaceholder && (previewPlaceholder.style.display = 'none');
    previewImg.style.display = 'block';
    previewImg.src = url;
});

/* If user navigates to add new (no edit param), ensure preview shows placeholder */
if (!<?= $editRow ? 'true' : 'false' ?>) {
    // hide any src on previewImg
    previewImg.style.display = 'none';
}
</script>
</body>
</html>
