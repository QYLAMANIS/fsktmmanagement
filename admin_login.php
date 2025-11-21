<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === 'uthm') {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['username'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = 'admin';

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Kata laluan salah. Sila masukkan 'uthm'.";
        }
    } else {
        $error = "Email tidak dijumpai dalam sistem.";
    }

    if ($stmt) $stmt->close();
    if ($conn) $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta charset="UTF-8" />
  <title>Login Admin - FSKTM FYP</title>
 <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Poppins', sans-serif;
  background: url('images/bg-fsktm.jpg') center/cover no-repeat;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  color: #000;
}

/* HEADER */
.header {
  background: rgba(0,64,128,0.9);
  color: #fff;
  padding: 18px 12px;
  text-align: center;
  border-bottom: 3px solid #003060;
}
.header img {
  height: 55px;
  margin-bottom: 6px;
}
.header h1 {
  font-size: 0.9em;
  letter-spacing: 0.5px;
  line-height: 1.4;
  margin-bottom: 4px;
}
.header p {
  font-size: 0.8em;
}

/* CONTAINER */
.container {
  max-width: 320px;
  margin: 40px auto;
  padding: 20px 16px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(10px);
  box-shadow: 0 5px 18px rgba(0, 0, 0, 0.15);
  text-align: center;
  font-size: 0.85em;
  animation: fadeInUp 1s ease-out;
}
.container h2 {
  font-size: 0.9em;
  color: #003c8f;
  margin-bottom: 14px;
}

/* FORM */
.form-group {
  margin-bottom: 14px;
  text-align: left;
}
.form-group label {
  font-weight: 600;
  font-size: 0.85em;
  display: block;
  margin-bottom: 6px;
}
input[type="email"], input[type="password"] {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  font-size: 0.85em;
}

/* BUTTON */
.btn {
  background: #004080;
  color: white;
  padding: 8px 24px;
  border: none;
  border-radius: 25px;
  font-size: 0.85em;
  cursor: pointer;
  margin-top: 10px;
  transition: all 0.3s ease;
}
.btn:hover {
  background: #0066cc;
  transform: translateY(-2px);
}

/* ERROR */
.error {
  color: red;
  margin-bottom: 12px;
  font-size: 0.85em;
}

/* FOOTER */
.footer {
  text-align: center;
  color: #fff;
  background: rgba(0,64,128,0.9);
  font-size: 0.8em;
  padding: 10px 5px;
  margin-top: auto;
  border-top: 3px solid #003060;
}

/* RESPONSIVE */
@media (max-width: 600px) {
  .header img { height: 45px; }
  .header h1 { font-size: 0.85em; }
  .container { max-width: 90%; margin: 25px auto; padding: 16px; }
  .btn { width: 100%; margin: 6px 0; }
}

/* ANIMATION */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(25px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>
  <div class="header">
    <img src="images/logo-fsktm.png" alt="Logo FSKTM" />
    <h1>SISTEM PENGURUSAN PEMILIHAN TAJUK DAN PENYELIA UNTUK PROJEK SARJANA MUDA</h1>
    <p>Fakulti Sains Komputer & Teknologi Maklumat</p>
  </div>
  <div class="container">
    <h2>Log Masuk Admin</h2>
    <?php if ($error) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
    <form method="post" action="">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required autofocus />
      </div>
      <div class="form-group">
        <label for="password">Kata Laluan:</label>
        <input type="password" name="password" id="password" required />
      </div>
      <button type="submit" class="btn">Log Masuk</button>
    </form>
  </div>
  <div class="footer">
    Copyright © 2025 FSKTM<br />
    Developed by Nur Aqilah
  </div>
</body>
</html>
