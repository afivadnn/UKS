<?php
include 'koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID tidak valid");
}

$id = $_GET['id'];

// Tangani form update
if (isset($_POST['update'])) {
  $nama = $_POST['nama'];
  $jumlah = (int)$_POST['jumlah']; // Pastikan jumlah dikonversi ke integer
  $status = $_POST['status'];
  $fungsi = trim($_POST['fungsi']); // Pastikan tidak ada whitespace di awal atau akhir
  $expired = $_POST['expired'];

  // Debug - untuk melihat nilai yang dikirim
  echo "<div class='debug'>Nilai fungsi yang diterima: " . htmlspecialchars($fungsi) . "</div>";

  // Validasi data
  if (empty($nama) || $jumlah < 0 || empty($status) || empty($fungsi) || empty($expired)) {
    echo "<div class='error'>Semua field harus diisi dan jumlah harus >= 0!</div>";
} else {
    $nama = mysqli_real_escape_string($conn, $nama);
    $status = mysqli_real_escape_string($conn, $status);
    $fungsi = mysqli_real_escape_string($conn, $fungsi);
    $expired = mysqli_real_escape_string($conn, $expired);

    $query_debug = "UPDATE obat SET 
                    nama='$nama',
                    jumlah=$jumlah,
                    status='$status',
                    fungsi='$fungsi',
                    expired='$expired'
                    WHERE id=$id";

    if (mysqli_query($conn, $query_debug)) {
        echo "<div class='success'>Data berhasil diupdate!</div>";
        echo "<script>
                setTimeout(() => window.location.href = 'data-obat.php', 2000);
              </script>";
    } else {
        echo "<div class='error'>Gagal update: " . mysqli_error($conn) . "</div>";
        echo "<div class='debug'>Query: $query_debug</div>";
    }
  }
}

// Ambil data obat setelah proses update (untuk menampilkan data terbaru jika user tetap di halaman)
$query = mysqli_query($conn, "SELECT * FROM obat WHERE id = $id");
if (!$query || mysqli_num_rows($query) == 0) {
  die("Data tidak ditemukan");
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Obat</title>
  <link rel="stylesheet" href="edit-obat.css">
</head>
<body>
  <div class="container">
    <h2>Edit Data Obat</h2>
    <form method="POST" action="edit-obat.php?id=<?= $id ?>">
      <label for="nama">Nama Obat:</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

      <label for="jumlah">Jumlah:</label>
      <input type="number" name="jumlah" min="0" value="<?= $data['jumlah'] ?>" required>

      <label for="status">Status:</label>
      <select name="status" required>
        <option value="">-- Pilih Status --</option>
        <option value="Ada" <?= $data['status'] === 'Ada' ? 'selected' : '' ?>>Ada</option>
        <option value="Habis" <?= $data['status'] === 'Habis' ? 'selected' : '' ?>>Habis</option>
      </select>

      <label for="fungsi">Fungsi Obat:</label>
      <textarea name="fungsi" id="fungsi" rows="4" required><?= htmlspecialchars($data['fungsi']) ?></textarea>

      <label for="expired">Tanggal Expired:</label>
      <input type="date" name="expired" value="<?= $data['expired'] ?>" required>

      <button type="submit" name="update">💾 Update</button>
    </form>

    <a class="back-link" href="data-obat.php">⬅ Kembali ke Data Obat</a>
  </div>
</body>