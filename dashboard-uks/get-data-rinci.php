<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan tidak ada output sebelum header JSON
ob_start();

include 'koneksi.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    // Pastikan hanya output JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID tidak ditemukan']);
    exit();
}

// Sanitize the input
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Query to get patient data
$sql = "SELECT * FROM pasien WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    // Tangani error query
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Query error: ' . mysqli_error($conn)]);
    exit();
}

// Check if data exists
if (mysqli_num_rows($result) > 0) {
    $pasienData = mysqli_fetch_assoc($result);
    
    // Query to get medication data
$obatQuery = "SELECT op.id, o.nama AS nama_obat, op.jumlah, op.tanggal_pemberian 
              FROM obat_pasien op 
              JOIN obat o ON o.id = op.obat_id 
              WHERE op.pasien_id = '$id'";
    $obatResult = mysqli_query($conn, $obatQuery);
    
    $obatData = [];
    if ($obatResult) {
        while ($row = mysqli_fetch_assoc($obatResult)) {
            $obatData[] = $row;
        }
    }
    
    // Prepare response
    $response = [
        'pasien' => $pasienData,
        'obat' => $obatData
    ];
    
    // Clean any previous output
    ob_end_clean();
    
    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Clean any previous output
    ob_end_clean();
    
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Data tidak ditemukan']);
}

// Close connection
mysqli_close($conn);
exit(); // Pastikan tidak ada output tambahan
?>