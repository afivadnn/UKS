<?php
include 'koneksi.php';
header('Content-Type: application/json');

if (isset($_GET['nis'])) {
  $nis = $_GET['nis'];
  
  // Gunakan prepared statement untuk keamanan
  $stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
  $stmt->bind_param("s", $nis);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $siswa = $result->fetch_assoc();
    
    // Return JSON response
    echo json_encode([
      'success' => true,
      'siswa' => [
        'nis' => $siswa['nis'],
        'nama' => $siswa['nama'],
        'kelas' => $siswa['kelas'],
        'jenis_kelamin' => $siswa['jenis_kelamin']
      ]
    ]);
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'Siswa dengan NIS tersebut tidak ditemukan'
    ]);
  }
  
  $stmt->close();
} else {
  echo json_encode([
    'success' => false,
    'message' => 'NIS tidak diberikan'
  ]);
}