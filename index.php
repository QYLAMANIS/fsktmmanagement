<!DOCTYPE html>
<html lang="ms">
<head>
    <link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta charset="UTF-8" />
  <title>FSKTM PSM</title>

  <!-- 🌟 Favicon -->
  <link rel="icon" type="image/png" sizes="64x64" href="images/favicon.png">
  <link rel="apple-touch-icon" sizes="128x128" href="images/favicon.png">

  <style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Poppins', sans-serif;
    background: url('images/bg-fsktm.jpg') center center/cover no-repeat;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    color: #000;
  }

  /* HEADER */
  .header {
    background: rgba(0,64,128,0.9);
    color: #fff;
    padding: 20px 10px;
    text-align: center;
    border-bottom: 3px solid #003060;
    animation: fadeInDown 1s ease-out;
  }
  .header img {
    height: 100px;
    width: auto;
    margin-bottom: 8px;
    transition: all 0.3s ease;
  }
  .header img:hover {
    transform: scale(1.05);
  }
  .header h1 {
    font-size: 1em;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    line-height: 1.4;
  }
  .header p {
    font-size: 0.9em;
  }

  /* CONTAINER */
  .container {
    max-width: 340px;
    margin: 40px auto;
    padding: 20px 16px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    text-align: center;
    animation: fadeInUp 1s ease-out;
    font-size: 0.9em;
  }
  .container h2 {
    font-size: 1em;
    color: #003c8f;
    margin-bottom: 10px;
  }
  .about {
    font-size: 0.9em;
    color: #333;
    margin-bottom: 18px;
    line-height: 1.4;
  }

  /* BUTTON */
  .btn {
    display: inline-block;
    margin: 6px;
    padding: 10px 25px;
    background: #004080;
    color: #fff;
    border-radius: 25px;
    font-size: 0.9em;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.25s ease;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  }
  .btn:hover {
    background: #0066cc;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0, 102, 204, 0.25);
  }

  /* FOOTER */
  .footer {
    text-align: center;
    color: #fff;
    background: rgba(0,64,128,0.9);
    font-size: 0.8em;
    padding: 12px 5px;
    margin-top: auto;
    border-top: 3px solid #003060;
    letter-spacing: 0.3px;
  }

  /* RESPONSIVE */
  @media (max-width: 600px) {
    .header img { height: 70px; }
    .header h1 { font-size: 0.9em; }
    .container { max-width: 90%; margin: 25px auto; padding: 15px; }
    .btn { width: 100%; margin: 6px 0; }
  }

  /* ANIMATIONS */
  @keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-25px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
  }

  </style>
</head>

<body>
  <!-- HEADER -->
  <div class="header">
    <img src="images/logo-fsktm.png" alt="Logo FSKTM" />
    <h1>SISTEM PENGURUSAN PEMILIHAN TAJUK DAN PENYELIA UNTUK PROJEK SARJANA MUDA</h1>
    <p>Fakulti Sains Komputer & Teknologi Maklumat</p>
  </div>

  <!-- CONTAINER -->
  <div class="container">
    <h2>Sila Pilih Peranan Anda Untuk Log Masuk</h2>
    <div class="about">
      Sistem ini membantu pelajar dan penyelia menguruskan projek sarjana muda (PSM) FSKTM secara atas talian.
    </div>

    <!-- 3 BUTTONS -->
    <a href="student_login.php" class="btn">Pelajar</a>
    <a href="supervisor_login.php" class="btn">Penyelia</a>
    <a href="admin_login.php" class="btn">Admin</a>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    Copyright © 2025 FSKTM. All rights reserved.<br />
    © 2025 UTHM #MakeItHappen #KasiJadi<br />
    Developed by Nur Aqilah FSKTM
  </div>
</body>
</html>
