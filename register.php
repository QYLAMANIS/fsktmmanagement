<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email sudah digunakan.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hash', '$role')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Pendaftaran berjaya! Sila login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Ralat: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
     <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <meta charset="UTF-8">
  <title>Daftar Akaun - FSKTM FYP System</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: url('images/bg-fsktm.jpg') center center/cover no-repeat;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .header {
      background: rgba(0,64,128,0.90);
      color: #fff;
      padding: 30px 20px 18px;
      text-align: center;
      border-bottom: 5px solid #003060;
      animation: fadeInDown 1s ease-out;
    }
    .header img {
      height: 90px;
      margin-bottom: 10px;
      margin-top: 10px;
    }
    .header h1 {
      font-size: 1.3em;
      margin-bottom: 8px;
    }
    .header p {
      font-size: 1em;
      margin-top: 4px;
    }
    .container {
      max-width: 480px;
      margin: 40px auto;
      padding: 30px 20px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      animation: fadeInUp 1s ease-out;
      color: #000;
    }
    .container h2 {
      text-align: center;
      color: #004080;
      margin-bottom: 20px;
    }
    form label {
      font-weight: 600;
      display: block;
      margin-top: 14px;
    }
    form input, form select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 10px;
    }
    .btn {
      width: 100%;
      margin-top: 20px;
      padding: 12px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 30px;
      font-size: 1em;
      cursor: pointer;
      transition: 0.3s ease;
    }
    .btn:hover {
      background: #0066cc;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .bottom-text {
      text-align: center;
      margin-top: 20px;
    }
    .bottom-text a {
      color: #004080;
      text-decoration: none;
      font-weight: bold;
    }
    .bottom-text a:hover {
      text-decoration: underline;
    }
    .footer {
      text-align: center;
      color: #fff;
      background: rgba(0,64,128,0.85);
      font-size: 0.95em;
      padding: 16px 8px 12px 8px;
      margin-top: 40px;
      border-top: 4px solid #003060;
    }
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px);}
      to { opacity: 1; transform: translateY(0);}
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px);}
      to { opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="header">
    <img src="images/logo-fsktm.png" alt="Logo FSKTM">
    <h1>SISTEM PENGURUSAN PEMILIHAN TAJUK DAN PENYELIA UNTUK PROJEK SARJANA MUDA</h1>
    <p>Fakulti Sains Komputer & Teknologi Maklumat</p>
  </div>

  <div class="container">
    <h2>Daftar Akaun Baru</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="register.php">
      <label>Nama:</label>
      <input type="text" name="name" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Kata Laluan:</label>
      <input type="password" name="password" required>

      <label>Peranan:</label>
      <select name="role" required>
        <option value="student">Pelajar</option>
        <option value="supervisor">Penyelia</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit" class="btn">Daftar</button>
    </form>

    <div class="bottom-text">
      Sudah ada akaun? <a href="login.php">Login di sini</a>
    </div>
  </div>

  <div class="footer">
    Copyright © 2025 FSKTM. All rights reserved.<br>
    © 2025 UTHM #MakeItHappen #KasiJadi<br>
    Developed by Nur Aqilah FSKTM
  </div>
</body>
</html>
