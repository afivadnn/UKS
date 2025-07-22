<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  
  // Gunakan prepared statement untuk keamanan
  $stmt = $conn->prepare("DELETE FROM pasien WHERE id = ?");
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
    header("Location: data-pasien.php?status=success&message=Data+pasien+berhasil+dihapus");
  } else {
    header("Location: data-pasien.php?status=error&message=" . urlencode("Gagal menghapus data: " . $conn->error));
  }
}