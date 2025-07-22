<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'koneksi.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get form data
  $nama = $_POST['nama'];
  $jumlah = $_POST['jumlah'];
  $fungsi = $_POST['fungsi'];
  $expired = $_POST['expired'];
  
  // Set status based on quantity
  $status = ($jumlah > 0) ? "Ada" : "Habis";
  
  // Process image upload
  $gambar = "";
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['gambar']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    // Verify file extension
    if (in_array(strtolower($filetype), $allowed)) {
      // Create unique filename
      $newname = uniqid() . '.' . $filetype;
      $target_dir = "images/obat/";
      
      // Ensure directory exists
      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }
      
      $target_file = $target_dir . $newname;
      
      // Upload file
      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
        $gambar = $newname;
      } else {
        echo "<script>alert('Gagal mengupload gambar!');</script>";
      }
    } else {
      echo "<script>alert('Format file gambar tidak diizinkan! Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.');</script>";
    }
  }
  
  // Insert data into database
  $stmt = $conn->prepare("INSERT INTO obat (nama, gambar, jumlah, status, fungsi, expired) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssisss", $nama, $gambar, $jumlah, $status, $fungsi, $expired);
  
  if ($stmt->execute()) {
    echo "<script>
      alert('Data obat berhasil ditambahkan!');
      window.location.href = 'data-obat.php';
    </script>";
  } else {
    echo "<script>alert('Gagal menambahkan data: " . $stmt->error . "');</script>";
  }
} else {
  // If someone tries to access this file directly
  header("Location: data-obat.php");
  exit;
}
?>