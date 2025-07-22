<?php
include 'koneksi.php';

// Memproses data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $fungsi = $_POST['fungsi'];
    $expired = $_POST['expired'];
    
    // Tentukan status berdasarkan jumlah stok
    $status = ($jumlah > 0) ? "Ada" : "Habis";
    
    // Handle upload gambar
    $gambar = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verifikasi ekstensi file
        if (in_array(strtolower($filetype), $allowed)) {
            // Buat nama file unik untuk mencegah overwrite
            $newname = uniqid() . '.' . $filetype;
            $target_dir = "images/obat/";
            
            // Pastikan direktori ada
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $newname;
            
            // Upload file
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $newname;
            } else {
                echo "<script>
                    alert('Gagal mengupload gambar!');
                    window.location.href = 'data-obat.php';
                </script>";
                exit;
            }
        } else {
            echo "<script>
                alert('Format file gambar tidak diizinkan! Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.');
                window.location.href = 'data-obat.php';
            </script>";
            exit;
        }
    }
    
    // Query untuk menyimpan data
    $sql = "INSERT INTO obat (nama, gambar, jumlah, status, fungsi, expired) 
            VALUES ('$nama', '$gambar', $jumlah, '$status', '$fungsi', '$expired')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Data obat berhasil ditambahkan!');
            window.location.href = 'data-obat.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: " . mysqli_error($conn) . "');
            window.location.href = 'data-obat.php';
        </script>";
    }
    
    mysqli_close($conn);
} else {
    // Redirect jika diakses langsung tanpa POST
    header("Location: data-obat.php");
    exit;
}
?>