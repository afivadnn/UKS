<?php
include 'koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Periksa apakah ID obat diberikan
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // Ambil informasi gambar terlebih dahulu (jika ada)
        $query_gambar = "SELECT gambar FROM obat WHERE id = ?";
        $stmt_gambar = $conn->prepare($query_gambar);
        $stmt_gambar->bind_param("i", $id);
        $stmt_gambar->execute();
        $result_gambar = $stmt_gambar->get_result();
        $gambar = $result_gambar->fetch_assoc()['gambar'] ?? null;
        
        // Hapus data obat dari database
        $query_delete = "DELETE FROM obat WHERE id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            // Commit transaksi jika berhasil
            $conn->commit();
            
            // Hapus file gambar jika ada
            if ($gambar && !empty($gambar)) {
                $file_path = "images/obat/" . $gambar;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Redirect dengan pesan sukses
            echo "<script>
                alert('Data obat berhasil dihapus!');
                window.location.href = 'data-obat.php';
            </script>";
        } else {
            // Rollback jika gagal
            $conn->rollback();
            echo "<script>
                alert('Gagal menghapus data: " . $conn->error . "');
                window.location.href = 'data-obat.php';
            </script>";
        }
        
    } catch (Exception $e) {
        // Rollback jika terjadi exception
        $conn->rollback();
        echo "<script>
            alert('Terjadi kesalahan: " . $e->getMessage() . "');
            window.location.href = 'data-obat.php';
        </script>";
    }
    
    // Tutup statement
    if (isset($stmt_gambar)) $stmt_gambar->close();
    if (isset($stmt_delete)) $stmt_delete->close();
    
} else {
    // Jika ID tidak diberikan
    echo "<script>
        alert('ID obat tidak diberikan!');
        window.location.href = 'data-obat.php';
    </script>";
}

// Tutup koneksi
$conn->close();
?>