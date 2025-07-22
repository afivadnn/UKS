<?php
include 'koneksi.php';

$pasien_id = $_GET['pasien_id'] ?? 0;

header('Content-Type: application/json');

if (!$pasien_id || !is_numeric($pasien_id)) {
    echo json_encode(['error' => 'ID Pasien tidak valid']);
    exit();
}

try {
    $query = "SELECT o.nama AS nama_obat, d.jumlah, d.satuan, d.tanggal_pemberian
              FROM obat_pasien d 
              JOIN obat o ON o.id = d.obat_id 
              WHERE d.pasien_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pasien_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
           $date = new DateTime($row['tanggal_pemberian'], new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
        $row['tanggal_pemberian'] = $date->format('Y-m-d H:i:s');
        $data[] = $row;
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>