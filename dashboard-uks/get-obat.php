<?php
include 'koneksi.php';

// Periksa apakah ID obat diberikan
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil data obat dari database
    $stmt = $conn->prepare("SELECT * FROM obat WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $obat = $result->fetch_assoc();
        
        // Konversi data ke format JSON
        header('Content-Type: application/json');
        echo json_encode($obat);
    } else {
        // Jika data tidak ditemukan
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Data obat tidak ditemukan']);
    }
} else {
    // Jika ID tidak diberikan
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID obat tidak diberikan']);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>