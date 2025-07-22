<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

// Process the patient input form and update medication stock
if (isset($_POST['simpan'])) {
    // Get all patient data from the form
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jam_pelajaran = mysqli_real_escape_string($conn, $_POST['jam_pelajaran']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $keluhan = mysqli_real_escape_string($conn, $_POST['keluhan']);
    $riwayat = mysqli_real_escape_string($conn, $_POST['riwayat']);
    $tindakan = mysqli_real_escape_string($conn, $_POST['tindakan']);
    $petugas = mysqli_real_escape_string($conn, $_POST['petugas']);
    
    // Get medication data if selected
    $obat_id = isset($_POST['obat']) ? mysqli_real_escape_string($conn, $_POST['obat']) : '';
    $jumlah_obat = isset($_POST['jumlah_obat']) ? (int)$_POST['jumlah_obat'] : 0;
    
    // Store name of medication for display in success message
    $nama_obat = '';
    if (!empty($obat_id)) {
        $obat_info_query = "SELECT nama FROM obat WHERE id = '$obat_id'";
        $obat_info_result = mysqli_query($conn, $obat_info_query);
        if ($obat_info = mysqli_fetch_assoc($obat_info_result)) {
            $nama_obat = $obat_info['nama'];
        }
    }
    
    // Start transaction to ensure both operations succeed or fail together
    mysqli_begin_transaction($conn);
    
    try {
        // Insert patient record
        $insert_query = "INSERT INTO pasien (tanggal, nama, kelas, jam_pelajaran,jurusan, jk, keluhan, riwayat, tindakan,petugas) 
                        VALUES ('$tanggal', '$nama', '$kelas', '$jam_pelajaran', '$jurusan', '$jk', '$keluhan', '$riwayat', '$tindakan', '$petugas')";
        
        mysqli_query($conn, $insert_query);
        $pasien_id = mysqli_insert_id($conn);
        
        // Update medication stock if medication was given
        if (!empty($obat_id) && $jumlah_obat > 0) {
            // Verify stock is available
            $check_stock_query = "SELECT jumlah FROM obat WHERE id = '$obat_id' AND jumlah >= $jumlah_obat";
            $stock_result = mysqli_query($conn, $check_stock_query);
            
            if (mysqli_num_rows($stock_result) == 0) {
                throw new Exception("Stok obat tidak mencukupi atau obat tidak ditemukan!");
            }
            
            // Update stock
            $update_stock_query = "UPDATE obat SET jumlah = jumlah - $jumlah_obat WHERE id = '$obat_id'";
            mysqli_query($conn, $update_stock_query);
            
            $insert_obat_query = "INSERT INTO obat_pasien (pasien_id, obat_id, jumlah, tanggal_pemberian) 
                                VALUES ('$pasien_id', '$obat_id', '$jumlah_obat', NOW())";
            mysqli_query($conn, $insert_obat_query);
        
            // Update status if stock becomes zero
            $update_status_query = "UPDATE obat SET status = CASE WHEN jumlah = 0 THEN 'Habis' ELSE 'Ada' END WHERE id = '$obat_id'";
            mysqli_query($conn, $update_status_query);
        }
        
        // Commit the transaction
        mysqli_commit($conn);
        
        // Show success message and redirect
        $obat_message = !empty($nama_obat) ? " dan $jumlah_obat $nama_obat telah diberikan" : "";
        echo "<script>
                alert('Data pasien $nama berhasil disimpan$obat_message.');
                window.location.href = 'data-pasien.php';
              </script>";
              
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        echo "<script>
                alert('Error: " . $e->getMessage() . "');
                window.history.back();
              </script>";
    }
}
?>