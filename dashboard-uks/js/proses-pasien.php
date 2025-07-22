<?php
// proses-pasien.php - File untuk memproses form input pasien

include 'koneksi.php';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $tanggal = $_POST['tanggal'];
  $nama = $_POST['nama'];
  $kelas = $_POST['kelas'];
  $jk = $_POST['jk'];
  $keluhan = $_POST['keluhan'];
  $riwayat = $_POST['riwayat'];
  $tindakan = $_POST['tindakan'];
  $nis = $_POST['nis'];

  // Validasi data
  $errors = [];
  
  if (empty($tanggal)) {
    $errors[] = "Tanggal kunjungan harus diisi";
  }
  
  if (empty($nama)) {
    $errors[] = "Nama pasien harus diisi";
  }
  
  if (empty($kelas)) {
    $errors[] = "Kelas harus diisi";
  }
  
  if (empty($jk)) {
    $errors[] = "Jenis kelamin harus dipilih";
  }
  
  if (empty($nis)) {
    $errors[] = "NIS harus diisi";
  }
  
  // Jika tidak ada error, simpan data ke database
  if (empty($errors)) {
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO pasien (tanggal, nama, kelas, jk, keluhan, riwayat, tindakan, nis) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $tanggal, $nama, $kelas, $jk, $keluhan, $riwayat, $tindakan, $nis);
    
    if ($stmt->execute()) {
      // Redirect ke halaman data pasien dengan pesan sukses
      header("Location: data-pasien.php?status=success&message=Data pasien berhasil disimpan");
      exit();
    } else {
      $errors[] = "Gagal menyimpan data: " . $conn->error;
    }
    
    $stmt->close();
  }
  
  // Jika ada error, kembali ke halaman form dengan pesan error
  if (!empty($errors)) {
    $error_message = implode("<br>", $errors);
    header("Location: data-pasien.php?status=error&message=" . urlencode($error_message));
    exit();
  }
}