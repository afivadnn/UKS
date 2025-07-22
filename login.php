<?php
session_start();
include 'koneksi.php';

// Redirect jika sudah login
if (isset($_SESSION['admin']) && isset($_SESSION['log']) && $_SESSION['log'] == 'login') {
    // Jika ada URL redirect yang tersimpan, gunakan itu
    if (isset($_SESSION['redirect_url'])) {
        $redirect_url = $_SESSION['redirect_url'];
        unset($_SESSION['redirect_url']); // Hapus dari session
        header("Location: $redirect_url");
    } else {
        header('Location: index.php');
    }
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']); // Menggunakan $conn dari file kedua
    $password = hash('sha256', $_POST['password']); // Menggunakan metode hash SHA-256 dari file kedua

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['userid'] = $user['id'];
        $_SESSION['admin'] = $username; // Menggunakan nama session dari file kedua
        $_SESSION['username'] = $username;
        $_SESSION['log'] = 'login';

        echo '<script>alert("Anda berhasil login sebagai ' . htmlspecialchars($username) . '"); window.location="dashboard-uks/index.php";</script>';
        exit();
    } else {
        echo '<script>alert("Username atau password salah!"); history.go(-1);</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Login Admin UKS</title>
  <meta name="description" content="Sistem Administrasi Unit Kesehatan Sekolah" />
  <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4a6cf7;
      --primary-dark: #3451b2;
      --secondary: #6c757d;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #28a745;
      --danger: #dc3545;
      --border-radius: 12px;
      --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      color: var(--dark);
      line-height: 1.6;
    }
    
    .login-container {
      display: flex;
      max-width: 1000px;
      width: 100%;
      background: white;
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: var(--box-shadow);
    }
    
    .login-illustration {
      flex: 1;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      position: relative;
      overflow: hidden;
    }
    
    .login-illustration::before {
      content: "";
      position: absolute;
      width: 200%;
      height: 200%;
      top: -50%;
      left: -50%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(30deg);
    }
    
    .login-illustration h2 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 2rem;
      margin-bottom: 15px;
      text-align: center;
      z-index: 1;
    }
    
    .login-illustration p {
      opacity: 0.9;
      text-align: center;
      max-width: 350px;
      z-index: 1;
    }
    
    .graphic-element {
      width: 100%;
      height: 200px;
      margin: 30px 0;
      position: relative;
      z-index: 1;
    }
    
    .circle {
      position: absolute;
      border-radius: 50%;
    }
    
    .circle-1 {
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.15);
      top: 20px;
      left: 50%;
      transform: translateX(-80%);
    }
    
    .circle-2 {
      width: 80px;
      height: 80px;
      background: rgba(255, 255, 255, 0.1);
      top: 80px;
      left: 50%;
      transform: translateX(20%);
    }
    
    .circle-3 {
      width: 50px;
      height: 50px;
      background: rgba(255, 255, 255, 0.2);
      top: 150px;
      left: 50%;
      transform: translateX(-50%);
    }
    
    .login-form {
      flex: 1;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .app-brand {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
    }
    
    .app-logo {
      width: 50px;
      height: 50px;
      background: var(--primary);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      font-weight: bold;
      margin-right: 15px;
    }
    
    .app-brand-text {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--dark);
    }
    
    .app-brand-text span {
      color: var(--primary);
    }
    
    .welcome-text {
      margin-bottom: 30px;
    }
    
    .welcome-text h1 {
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: var(--dark);
    }
    
    .welcome-text p {
      color: var(--secondary);
      font-size: 1rem;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--dark);
    }
    
    .form-control {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #ddd;
      border-radius: var(--border-radius);
      font-size: 1rem;
      transition: var(--transition);
      background-color: #fafafa;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.15);
      background-color: white;
    }
    
    .password-container {
      position: relative;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--secondary);
      background: none;
      border: none;
      font-size: 1.2rem;
    }
    
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }
    
    .form-check {
      display: flex;
      align-items: center;
    }
    
    .form-check-input {
      margin-right: 8px;
      width: 18px;
      height: 18px;
      border-radius: 4px;
      border: 1px solid #ddd;
      cursor: pointer;
    }
    
    .form-check-label {
      font-size: 0.95rem;
      color: var(--secondary);
    }
    
    .forgot-password {
      color: var(--primary);
      text-decoration: none;
      font-size: 0.95rem;
      transition: var(--transition);
    }
    
    .forgot-password:hover {
      text-decoration: underline;
      color: var(--primary-dark);
    }
    
    .btn-login {
      background: var(--primary);
      color: white;
      border: none;
      padding: 14px;
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      width: 100%;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .btn-login:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
    }
    
    .btn-login i {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    .divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
    }
    
    .divider::before,
    .divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid #eee;
    }
    
    .divider-text {
      padding: 0 15px;
      color: var(--secondary);
      font-size: 0.9rem;
    }
    
    .copyright {
      text-align: center;
      color: var(--secondary);
      font-size: 0.9rem;
      margin-top: 30px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }
      
      .login-illustration {
        padding: 30px 20px;
      }
      
      .login-illustration h2 {
        font-size: 1.5rem;
      }
      
      .login-form {
        padding: 40px 30px;
      }
      
      .app-brand-text {
        font-size: 1.5rem;
      }
      
      .welcome-text h1 {
        font-size: 1.5rem;
      }
    }
    
    @media (max-width: 480px) {
      .login-container {
        border-radius: 12px;
      }
      
      .form-options {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .forgot-password {
        margin-top: 10px;
      }
      
      .app-brand {
        justify-content: center;
      }
    }
    
    /* Animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .login-form {
      animation: fadeIn 0.6s ease-out;
    }
    
    .circle-1 {
      animation: float 8s ease-in-out infinite;
    }
    
    .circle-2 {
      animation: float 6s ease-in-out infinite;
      animation-delay: 1s;
    }
    
    .circle-3 {
      animation: float 4s ease-in-out infinite;
      animation-delay: 2s;
    }
    
    @keyframes float {
      0% { transform: translateY(0) translateX(-80%); }
      50% { transform: translateY(-20px) translateX(-80%); }
      100% { transform: translateY(0) translateX(-80%); }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-illustration">
      <h2>Sistem Administrasi UKS</h2>
      <p>Unit Kesehatan Sekolah SMECONE - Portal Admin</p>
      <div class="graphic-element">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
      </div>
      <p>Masuk untuk mengelola data kesehatan siswa, obat-obatan, dan laporan kegiatan UKS</p>
    </div>
    
    <div class="login-form">
      <div class="app-brand">
        <div class="app-logo">U</div>
        <div class="app-brand-text">UKS <span>SMECONE</span></div>
      </div>
      
      <div class="welcome-text">
        <h1>Selamat Datang Kembali</h1>
        <p>Silakan masuk untuk melanjutkan ke dashboard admin</p>
      </div>
      
      <form method="POST">
        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" autofocus required>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-container">
            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            <button type="button" class="password-toggle" id="togglePassword">
              <i>👁️</i>
            </button>
          </div>
        </div>
        
        <div class="form-options">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember-me">
            <label class="form-check-label" for="remember-me"> Ingat Saya</label>
          </div>
          <a href="#" class="forgot-password">Lupa Password?</a>
        </div>
        
        <button type="submit" name="login" class="btn-login">
          <i>🔐</i> Masuk ke Akun
        </button>
      </form>
      
      <div class="copyright">
        &copy; 2025 SANITAS TECH . Hak Cipta Dilindungi.
      </div>
    </div>
  </div>

  <script>
    // Password toggle functionality
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function () {
      // Toggle the type attribute
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      
      // Toggle the eye icon
      this.innerHTML = type === 'password' ? '<i>👁️</i>' : '<i>🔒</i>';
    });
    
    // Form validation and enhancement
    document.querySelector('form').addEventListener('submit', function(e) {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      
      if (!username || !password) {
        e.preventDefault();
        alert('Silakan isi username dan password!');
      }
    });
    
    // Add focus effect to inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
      });
    });
  </script>
</body>

</html>