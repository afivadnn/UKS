<?php
include 'koneksi.php';

if (isset($_POST['update'])) {
    // Ambil data dari form
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $jam_pelajaran = $_POST['jam_pelajaran'];
    $jurusan = $_POST['jurusan'];
    $jk = $_POST['jk'];
    $keluhan = $_POST['keluhan'];
    $riwayat = $_POST['riwayat'];
    $tindakan = $_POST['tindakan'];
 // 1. HAPUS OBAT YANG DICENTANG
   if (isset($_POST['all_obat_ids'])) {
    $all_obat_ids = $_POST['all_obat_ids'];
    
    // Dapatkan obat yang dicentang untuk dihapus
    $obat_dihapus = $_POST['obat_dihapus'] ?? [];
    
    // Cari obat yang TIDAK dicentang (tidak dihapus)
    $obat_tetap = array_diff($all_obat_ids, $obat_dihapus);
    
    // Hapus obat yang tidak ada di $obat_tetap
    $placeholders = implode(',', array_fill(0, count($obat_tetap), '?'));
    
    $query_delete = "DELETE FROM obat_pasien 
                    WHERE pasien_id = ? 
                    AND id NOT IN ($placeholders)";
    
    $stmt_delete = $conn->prepare($query_delete);
    $types = str_repeat('i', count($obat_tetap) + 1);
    $params = array_merge([$id], $obat_tetap);
    $stmt_delete->bind_param($types, ...$params);
    $stmt_delete->execute();
}
    
    // 2. TAMBAH OBAT BARU
    $obat_baru = isset($_POST['obat_baru']) ? (int)$_POST['obat_baru'] : null;
    $jumlah_baru = isset($_POST['jumlah_baru']) ? (int)$_POST['jumlah_baru'] : 0;
    
    if ($obat_baru && $jumlah_baru > 0) {
        // Periksa stok
        $query_stok = "SELECT jumlah FROM obat WHERE id = ?";
        $stmt_stok = $conn->prepare($query_stok);
        $stmt_stok->bind_param("i", $obat_baru);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        
        if ($result_stok->num_rows > 0) {
            $stok = $result_stok->fetch_assoc()['jumlah'];
            
            if ($stok >= $jumlah_baru) {
                // Tambahkan obat baru
                $tanggal_sekarang = date('Y-m-d H:i:s');
                $insert_obat_query = "INSERT INTO obat_pasien (pasien_id, obat_id, jumlah, tanggal_pemberian) 
                                     VALUES (?, ?, ?, ?)";
                $stmt_ins = $conn->prepare($insert_obat_query);
                $stmt_ins->bind_param("iiis", $id, $obat_baru, $jumlah_baru, $tanggal_sekarang);
                $stmt_ins->execute();
                
                // Kurangi stok
                $update_stok_query = "UPDATE obat SET jumlah = jumlah - ? WHERE id = ?";
                $stmt_update_stok = $conn->prepare($update_stok_query);
                $stmt_update_stok->bind_param("ii", $jumlah_baru, $obat_baru);
                $stmt_update_stok->execute();
            } else {
                $_SESSION['error'] = "Stok obat tidak mencukupi!";
            }
        }
    }

    // 3. UPDATE DATA PASIEN
    $query = "UPDATE pasien SET 
              tanggal = ?,
              nama = ?,
              kelas = ?,
              jam_pelajaran = ?,
              jurusan = ?,
              jk = ?,
              keluhan = ?,
              riwayat = ?,
              tindakan = ?
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssi", 
        $tanggal, $nama, $kelas, $jam_pelajaran, $jurusan, 
        $jk, $keluhan, $riwayat, $tindakan, $id);
    
if ($stmt->execute()) {
    echo "<script>
            alert('Data pasien $nama berhasil diupdate.');
            window.location.href = 'data-pasien.php';
          </script>";
    exit(); // Pastikan eksekusi dihentikan setelah echo
} else {
    $_SESSION['error'] = "Gagal update pasien: ".$stmt->error;
    // Tangani kesalahan sesuai kebutuhan
}


}
?>