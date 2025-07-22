<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['admin']) || !isset($_SESSION['log']) || $_SESSION['log'] != 'login') {
    // Jika belum login, simpan URL yang sedang dicoba diakses
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Redirect ke halaman login
    header("Location: /login.php");
    exit();
}

// Jika sudah login, lanjutkan eksekusi file
?>