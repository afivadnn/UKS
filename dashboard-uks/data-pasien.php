<?php
include_once '../auth_check.php';


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien</title>
    <link rel="stylesheet" href="data-pasien.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="loading-spinner" id="loadingSpinner">
    <div class="spinner"></div>
</div>

<div class="layout">
     <aside class="sidebar">
      <div class="logo-container">
        <img src="sanitas.png" alt="Logo UKS SMECONE" class="logo-image">
        <span class="logo-text">UKS SMECONE</span>
      </div>
      <nav>
        <ul>
          <li ><a href="index.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
          <li class="active"><a href="data-pasien.php"><i class="fa-solid fa-user-group"></i> Data Pasien</a>
            <ul>
              <li class="active"><a href="data-pasien.php"><i class="fa-solid fa-list"></i> Data Rinci</a></li>
              <li><a href="#"><i class="fa-solid fa-print"></i> Print Surat Izin</a></li>
              <li><a href="#" id="btnInputPasien"><i class="fa-solid fa-plus"></i> Input Data Pasien</a></li>
            </ul>
          </li>
          <li><a href="data-obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <ul>
              <li><a href="data-obat.php"><i class="fa-solid fa-list"></i> Data Rinci</a></li>
              <li><a href="#"><i class="fa-solid fa-plus"></i> Input Data Obat</a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <div class="header-page">
        <small>Data / Data Pasien</small>
        <h1>Data Pasien</h1>
      </div>

<div class="action-top">
  <div class="filter-buttons">
    <button id="btnShowAll">🔘 Semua</button>
    <button id="btnFilterKelas" class="dropdown-button">
      🏷️ Berdasarkan Kelas
    </button>
    <button id="btnFilterGender" class="dropdown-button">
      📈 Berdasarkan Jenis Kelamin
    </button>
    <button id="btnFilterDate" class="dropdown-button">
      📅 Berdasarkan Tanggal
    </button>
     <button id="btnFilterJurusan" class="dropdown-button">
      🏫 Berdasarkan Jurusan
    </button>
  </div>
  <button class="btn-add" id="btnTambahPasien" title="Tambah Pasien">+</button>
</div>

<!-- Dropdown diletakkan langsung di bawah action-top untuk menghindari masalah nesting -->
<select id="filterKelas" class="filter-dropdown" style="display: none; position: absolute; z-index: 100;">
  <option value="">-- Pilih Kelas --</option>
  <option value="X">X</option>
  <option value="XI">XI</option>
  <option value="XII">XII</option>
</select>

<select id="filterGender" class="filter-dropdown" style="display: none; position: absolute; z-index: 100;">
  <option value="">-- Pilih Jenis Kelamin --</option>
  <option value="L">Laki-laki</option>
  <option value="P">Perempuan</option>
</select>

<select id="filterJurusan" class="filter-dropdown" style="display: none; position: absolute; z-index: 100;">
  <option value="">-- Pilih Jurusan --</option>
  <option value="PPLG 1">PPLG 1</option>
        <option value="PPLG 2">PPLG 2</option>
        <option value="AKL 1">AKL 1</option>
        <option value="AKL 2">AKL 2</option>
        <option value="AKL 3">AKL 3</option>
        <option value="AKL 4">AKL 4</option>
        <option value="AKL 5">AKL 5</option>
        <option value="MPLB 1">MPLB 1</option>
        <option value="MPLB 2">MPLB 2</option>
        <option value="MPLB 3">MPLB 3</option>
        <option value="TJKT 1 ">TJKT 1</option>
        <option value="TJKT 2">TJKT 2</option>
        <option value="DKV 1">DKV 1</option>
        <option value="DKV 2">DKV 2</option>
        <option value="PM 1">PM 1</option>
        <option value="PM 2">PM 2</option>
        <option value="PM 3">PM 3</option>
        <option value="TF 1">TF 1</option>
        <option value="TF 2">TF 2</option>
        <option value="TF 3">TF 3</option>
        <option value="TF 4">TF 4</option>
        <option value="TF 5">TF 5</option>
</select>

<select id="filterJam" class="filter-dropdown" style="display: none; position: absolute; z-index: 100;">
            <option value="">-- Pilih Jam Pelajaran --</option>
            <option value="Jam ke-1">Jam ke-1 (07:00-07:45)</option>
            <option value="Jam ke-2">Jam ke-2 (07:45-08:30)</option>
            <option value="Jam ke-3">Jam ke-3 (08:30-09:15)</option>
            <option value="Jam ke-4">Jam ke-4 (09:30-10:15)</option>
            <option value="Jam ke-5">Jam ke-5 (10:15-11:00)</option>
            <option value="Jam ke-6">Jam ke-6 (11:00-11:45)</option>
            <option value="Jam ke-7">Jam ke-7 (12:30-13:15)</option>
            <option value="Jam ke-8">Jam ke-8 (13:15-14:00)</option>
            <option value="Jam ke-9">Jam ke-9 (14:00-14:45)</option>
            <option value="Jam ke-10">Jam ke-10 (14:45-15:30)</option>
            <option value="Istirahat">Jam Istirahat</option>
        </select>

<input type="date" id="filterDate" class="filter-dropdown" style="display: none; position: absolute; z-index: 100;">

      <div class="table-container">
        <table>
          <thead>
            <tr>
                        <th>Id</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jam Pelajaran</th>
                        <th>Jurusan</th>
                        <th>JK</th>
                        <th>Keluhan</th>
                        <th>Riwayat Penyakit</th>
                        <th>Tindakan</th>
                        <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
             include 'koneksi.php';

             $sql = "SELECT * FROM pasien";
             $result = mysqli_query($conn, $sql);
         
             if (mysqli_num_rows($result) > 0) {
               while($row = mysqli_fetch_assoc($result)) {
                 echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['tanggal']}</td>
                                <td>{$row['nama']}</td>
                                <td>{$row['kelas']}</td>
                                <td>{$row['jam_pelajaran']}</td>
                                <td>{$row['jurusan']}</td>
                                <td>{$row['jk']}</td>
                                <td>{$row['keluhan']}</td>
                                <td>{$row['riwayat']}</td>
                                <td>{$row['tindakan']}</td>
                                <td class='aksi'>
                                    <a href='data-rinci.php?id={$row['id']}' title='Lihat'>🔍</a>
                                    <a href='edit-pasien.php?id={$row['id']}' title='Edit'>✏️</a>
                                  <a href='#' onclick='confirmDelete({$row['id']})' title='Hapus'>🗑️</a>
                                </td>
                            </tr>";
               }
             } else {
               echo "<tr><td colspan='9'>Data tidak ditemukan.</td></tr>";
             }
             ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Input Pasien -->
  <div id="modalInputPasien" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2>Tambah Data Pasien</h2>
      
      <form method="POST" action="process-input-pasien.php" class="modal-form">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>

       

        <label for="nama">Nama Pasien:</label>
        <input type="text" name="nama" required>

        <label for="kelas">Kelas:</label>
        <select name="kelas" required>
          <option value="">Pilih Kelas</option>
          <option value="X">X</option>
          <option value="XI">XI</option>
          <option value="XII">XII</option>
        </select>

        <label for="jam_pelajaran">Jam Pelajaran:</label>
            <select name="jam_pelajaran" required>
                <option value="">Pilih Jam Pelajaran</option>
                <option value="Jam ke-1">Jam ke-1 (07:15-08:00)</option>
                <option value="Jam ke-2">Jam ke-2 (08:00-08:45)</option>
                <option value="Jam ke-3">Jam ke-3 (08:45-09:30)</option>
                <option value="Jam ke-4">Jam ke-4 (09:30-10:15)</option>
                <option value="Jam ke-5">Jam ke-5 (10:30-11:10)</option>
                <option value="Jam ke-6">Jam ke-6 (11:10-11:45)</option>
                <option value="Jam ke-7">Jam ke-7 (12:50-13:30)</option>
                <option value="Jam ke-8">Jam ke-8 (13:30-14:10)</option>
                <option value="Jam ke-9">Jam ke-9 (14:10-14:50)</option>
                <option value="Jam ke-10">Jam ke-10 (14:50-15:30)</option>
                <option value="Istirahat">Jam Istirahat</option>
            </select>

        <label for="jurusan">Jurusan:</label>
        <select name="jurusan" required>
          <option value="">Pilih Jurusan</option>
          <option value="PPLG 1">PPLG 1</option>
          <option value="PPLG 2">PPLG 2</option>
          <option value="AKL 1">AKL 1</option>
          <option value="AKL 2">AKL 2</option>
          <option value="AKL 3">AKL 3</option>
          <option value="AKL 4">AKL 4</option>
          <option value="AKL 5">AKL 5</option>
          <option value="MPLB 1">MPLB 1</option>
          <option value="MPLB 2">MPLB 2</option>
          <option value="MPLB 3">MPLB 3</option>
          <option value="TJKT 1 ">TJKT 1</option>
          <option value="TJKT 2">TJKT 2</option>
          <option value="DKV 1">DKV 1</option>
          <option value="DKV 2">DKV 2</option>
          <option value="PM 1">PM 1</option>
          <option value="PM 2">PM 2</option>
          <option value="PM 3">PM 3</option>
          <option value="TF 1">TF 1</option>
          <option value="TF 2">TF 2</option>
          <option value="TF 3">TF 3</option>
          <option value="TF 4">TF 4</option>
          <option value="TF 5">TF 5</option>
        </select>

        


        <label for="jk">Jenis Kelamin:</label>
        <select name="jk" required>
          <option value="">Pilih Jenis Kelamin</option>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>

        <label for="keluhan">Keluhan:</label>
        <textarea name="keluhan" rows="3" required></textarea>

        <label for="riwayat">Riwayat Penyakit:</label>
        <textarea name="riwayat" rows="2"></textarea>

        <label for="tindakan">Tindakan:</label>
        <textarea name="tindakan" rows="2" required></textarea>

         <label for="petugas">Nama Petugas UKS:</label>
        <input  type="text" name="petugas" required>

        <div class="form-group">
  <label for="obat">Pemberian Obat:</label>
  <select id="obat" name="obat">
    <option value="">Tidak ada pemberian obat</option>
    <?php
    // Query untuk mendapatkan obat yang masih ada stoknya
    $obat_query = "SELECT id, nama, jumlah FROM obat WHERE jumlah > 0 AND status = 'Ada'";
    $obat_result = mysqli_query($conn, $obat_query);
    
    if (mysqli_num_rows($obat_result) > 0) {
      while ($obat = mysqli_fetch_assoc($obat_result)) {
        echo "<option value='{$obat['id']}' data-stok='{$obat['jumlah']}'>{$obat['nama']} (Stok: {$obat['jumlah']})</option>";
      }
    }
    ?>

    
  </select>

  
</div>

<div class="form-group" id="jumlah_obat_container" style="display: none;">
  <label for="jumlah_obat">Jumlah Obat yang Diberikan:</label>
  <input type="number" id="jumlah_obat" name="jumlah_obat" min="1" value="1">
  <p class="stok-info" id="stok-info">Stok tersedia: <span id="stok-tersedia">0</span></p>
</div>


        <button type="submit" name="simpan">💾 Simpan</button>
      </form>
    </div>
  </div>


<!-- detail -->
  <div id="modalDataRinci" class="modal">
  <div class="modal-content modal-rinci-content">
    <span class="close-modal-rinci">&times;</span>
    <div class="rinci-container">
      <div class="rinci-header">
        <h2>Detail Pasien</h2>
        <a id="btnPrintSurat" class="btn-print"><i class="print-icon">🖨️</i> Cetak Surat</a>
      </div>
      
      <div class="patient-profile">
        <div class="patient-info">
          <div class="patient-id-badge">
            <span id="idPasien"></span>
          </div>
          <div class="patient-name">
            <span id="namaPasien"></span>
          </div>
            <div class="patient-class">
            Kelas <span id="kelasPasien"></span> - <span id="jurusanPasien"></span>
          </div>
          
        </div>
        <div class="patient-avatar">
          <div class="avatar-circle">
            <span id="patientInitials"></span>
          </div>
          <div class="gender-badge" id="jkBadge">
            <span id="jkPasien"></span>
          </div>
        </div>
      </div>

      <div class="detail-sections">
        <div class="detail-section">
          <div class="section-title">Informasi Kunjungan</div>
          <div class="detail-grid">
            <div class="detail-item">
              <div class="detail-label">Tanggal Kunjungan</div>
              <div class="detail-value" id="tanggalPasien"></div>
            </div>
             <div class="detail-item">
              <div class="detail-label">Jam Pelajaran</div>
              <div class="detail-value schedule-badge" id="jamPelajaranDetail"></div>
            </div>
          </div>
        </div>
        
        <div class="detail-section">
          <div class="section-title">Kesehatan</div>
          <div class="detail-grid">
            <div class="detail-item full-width">
              <div class="detail-label">Keluhan</div>
              <div class="detail-value complaint-box" id="keluhanPasien"></div>
            </div>
            <div class="detail-item full-width">
              <div class="detail-label">Riwayat Penyakit</div>
              <div class="detail-value history-box" id="riwayatPasien"></div>
            </div>
          </div>
        </div>
        
        <div class="detail-section">
          <div class="section-title">Tindakan Medis</div>
          <div class="detail-grid">
            <div class="detail-item full-width">
              <div class="detail-label">Tindakan Yang Diberikan</div>
              <div class="detail-value treatment-box" id="tindakanPasien"></div>
            </div>
          </div>
        </div>

        </div>
        <div class="detail-section">
  <div class="section-title" id="obatDiberikan"></div>
</div>



<?php if (isset($has_medication) && $has_medication): ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pemberian</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($obat_pasien = mysqli_fetch_assoc($obat_pasien_result)): ?>
                <tr>
                    <td><?php echo $obat_pasien['nama_obat']; ?></td>
                    <td><?php echo $obat_pasien['jumlah']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($obat_pasien['tanggal_pemberian'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
  
<?php endif; ?>

      </div>
    </div>
  </div>
</div>


<!-- edit pasien -->
<?php
include 'koneksi.php';
// Dapatkan ID pasien dari parameter GET
$pasien_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query_obat = "SELECT obat_pasien.id, obat.nama, obat_pasien.jumlah, obat_pasien.tanggal_pemberian 
               FROM obat_pasien 
               JOIN obat ON obat.id = obat_pasien.obat_id 
               WHERE obat_pasien.pasien_id = ?";
$stmt = $conn->prepare($query_obat);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$result_obat = $stmt->get_result();
$obat_pasien = $result_obat->fetch_all(MYSQLI_ASSOC);


?>


<div id="modalEditPasien" class="modal">
  <div class="modal-content">
    <span class="close-modal-edit">&times;</span>
    <h2>Edit Data Pasien</h2>
    
 <form method="POST" action="process-edit-pasien.php" class="modal-form" id="formEditPasien">
      <input type="hidden" name="update" value="1">
      <input type="hidden" id="edit_id" name="id" value="">
      
      <label for="edit_tanggal">Tanggal:</label>
      <input type="date" name="tanggal" id="edit_tanggal" required>

      <label for="edit_nama">Nama Pasien:</label>
      <input type="text" name="nama" id="edit_nama" required>

      <label for="edit_kelas">Kelas:</label>
      <select name="kelas" id="edit_kelas" required>
        <option value="">Pilih Kelas</option>
        <option value="X">X</option>
        <option value="XI">XI</option>
        <option value="XII">XII</option>
      </select>

       <label for="edit_jurusan">Jurusan:</label>
      <select name="jurusan" id="edit_jurusan" required>
        <option value="">Pilih Jurusan</option>
        <option value="PPLG 1">PPLG 1</option>
        <option value="PPLG 2">PPLG 2</option>
        <option value="AKL 1">AKL 1</option>
        <option value="AKL 2">AKL 2</option>
        <option value="AKL 3">AKL 3</option>
        <option value="AKL 4">AKL 4</option>
        <option value="AKL 5">AKL 5</option>
        <option value="MPLB 1">MPLB 1</option>
        <option value="MPLB 2">MPLB 2</option>
        <option value="MPLB 3">MPLB 3</option>
        <option value="TJKT 1 ">TJKT 1</option>
        <option value="TJKT 2">TJKT 2</option>
        <option value="DKV 1">DKV 1</option>
        <option value="DKV 2">DKV 2</option>
        <option value="PM 1">PM 1</option>
        <option value="PM 2">PM 2</option>
        <option value="PM 3">PM 3</option>
        <option value="TF 1">TF 1</option>
        <option value="TF 2">TF 2</option>
        <option value="TF 3">TF 3</option>
        <option value="TF 4">TF 4</option>
        <option value="TF 5">TF 5</option>
      </select>

       <label for="edit_jam_pelajaran">Jam Pelajaran:</label>
            <select name="jam_pelajaran" id="edit_jam_pelajaran" required>
                <option value="">Pilih Jam Pelajaran</option>
                <option value="Jam ke-1">Jam ke-1 (07:00-07:45)</option>
                <option value="Jam ke-2">Jam ke-2 (07:45-08:30)</option>
                <option value="Jam ke-3">Jam ke-3 (08:30-09:15)</option>
                <option value="Jam ke-4">Jam ke-4 (09:30-10:15)</option>
                <option value="Jam ke-5">Jam ke-5 (10:15-11:00)</option>
                <option value="Jam ke-6">Jam ke-6 (11:00-11:45)</option>
                <option value="Jam ke-7">Jam ke-7 (12:30-13:15)</option>
                <option value="Jam ke-8">Jam ke-8 (13:15-14:00)</option>
                <option value="Jam ke-9">Jam ke-9 (14:00-14:45)</option>
                <option value="Jam ke-10">Jam ke-10 (14:45-15:30)</option>
                <option value="Istirahat">Jam Istirahat</option>
            </select>



      <label for="edit_jk">Jenis Kelamin:</label>
      <select name="jk" id="edit_jk" required>
        <option value="">Pilih Jenis Kelamin</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
      </select>

      <label for="edit_keluhan">Keluhan:</label>
      <textarea name="keluhan" id="edit_keluhan" rows="3" required></textarea>

      <label for="edit_riwayat">Riwayat Penyakit:</label>
      <textarea name="riwayat" id="edit_riwayat" rows="2"></textarea>

      <label for="edit_tindakan">Tindakan:</label>
      <textarea name="tindakan" id="edit_tindakan" rows="2" required></textarea>
<div class="riwayat-obat">
    <h3>Obat yang Sudah Diberikan</h3>
    <div id="riwayat-obat-container">
          <!-- Data riwayat obat akan diisi oleh JavaScript -->
          <p>Memuat data riwayat obat...</p>
</div>
      <!-- FORM TAMBAH OBAT BARU -->
      <div class="tambah-obat">
        <h3>Tambah Obat Baru</h3>
        <div class="form-group">
          <label for="obat_baru">Pilih Obat:</label>
          <select name="obat_baru" id="obat_baru">
            <option value="">Pilih Obat</option>
            <?php
            $obat_query = "SELECT id, nama, jumlah FROM obat WHERE jumlah > 0 AND status = 'Ada'";
            $obat_result = mysqli_query($conn, $obat_query);
            
            while ($obat = mysqli_fetch_assoc($obat_result)) {
              echo "<option value='{$obat['id']}' data-stok='{$obat['jumlah']}'>
                      {$obat['nama']} (Stok: {$obat['jumlah']})
                    </option>";
            }
            ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="jumlah_baru">Jumlah:</label>
          <input type="number" name="jumlah_baru" id="jumlah_baru" min="1" value="1">
          <p class="stok-info">Stok tersedia: <span id="stok-tersedia">0</span></p>
        </div>
      </div>

      <button type="submit" name="update" class="btn-save">💾 Simpan Perubahan</button>
    </form>
  </div>
</div>


</body>

<script src="data-pasien.js"></script>
<script>
// Update info stok saat obat dipilih
document.getElementById('obat_baru').addEventListener('change', function() {
  const selectedOption = this.options[this.selectedIndex];
  const stokTersedia = selectedOption.getAttribute('data-stok') || 0;
  document.getElementById('stok-tersedia').textContent = stokTersedia;
  document.getElementById('jumlah_baru').max = stokTersedia;
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data pasien akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus-pasien.php?id=' + id;
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>