<?php
include 'koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Proses update data jika form disubmit
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $nama = $_POST['nama'];
  $jumlah = $_POST['jumlah'];
  $fungsi = $_POST['fungsi'];
  $expired = $_POST['expired'];
  
  // Status berdasarkan jumlah stok
  $status = ($jumlah > 0) ? "Ada" : "Habis";
  
  // Cek apakah ada upload gambar baru
  $gambar_lama = $_POST['gambar_lama'];
  $gambar = $gambar_lama; // Default, gunakan gambar lama
  
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['gambar']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    // Verifikasi ekstensi file
    if (in_array(strtolower($filetype), $allowed)) {
      // Buat nama file unik
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
        
        // Hapus gambar lama jika ada dan berbeda
        if (!empty($gambar_lama) && $gambar_lama != $gambar && file_exists($target_dir . $gambar_lama)) {
          unlink($target_dir . $gambar_lama);
        }
      } else {
        echo "<script>alert('Gagal mengupload gambar!');</script>";
      }
    } else {
      echo "<script>alert('Format file gambar tidak diizinkan! Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.');</script>";
    }
  }
  

// Update data obat
$stmt = $conn->prepare("UPDATE obat SET nama=?, gambar=?, jumlah=?, status=?, fungsi=?, expired=? WHERE id=?");
$stmt->bind_param("ssisssi", $nama, $gambar, $jumlah, $status, $fungsi, $expired, $id);
  
  if ($stmt->execute()) {
    echo "<script>
      alert('Data obat berhasil diperbarui!');
      window.location.href = 'data-obat.php';
    </script>";
    exit;
  } else {
    echo "<script>alert('Gagal memperbarui data: " . $stmt->error . "');</script>";
  }
}

// Ambil data obat untuk ditampilkan di tabel
$sql = "SELECT * FROM obat";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Obat</title>
  <link rel="stylesheet" href="data-obat.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="layout">
  <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-container">
        <img src="sanitas.png" alt="Logo UKS SMECONE" class="logo-image">
        <span class="logo-text">UKS SMECONE</span>
      </div>
      <nav>
        <ul>
          <li ><a href="index.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
          <li ><a href="data-pasien.php"><i class="fa-solid fa-user-group"></i> Data Pasien</a>
            <ul>
              <li><a href="data-pasien.php"><i class="fa-solid fa-list"></i> Data Rinci</a></li>
              <li><a href="#"><i class="fa-solid fa-print"></i> Print Surat Izin</a></li>
              <li><a href="#" id="btnInputPasien"><i class="fa-solid fa-plus"></i> Input Data Pasien</a></li>
            </ul>
          </li>
          <li class="active"><a href="data-obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <ul>
              <li><a href="data-obat.php"><i class="fa-solid fa-list"></i> Data Rinci</a></li>
              <li><a href="#"  id="openModal"><i class="fa-solid fa-plus"></i> Input Data Obat</a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </aside>



  <!-- Konten utama -->
  <main class="main-content">
    <div class="container">
      <div class="header-bar">
        <h2>Data Obat</h2>
        <a href="#" id="openModalBtn" class="btn-tambah" title="Tambah Obat">+</a>
      </div>

      <div class="filter-buttons">
        <button>🔁 Semua</button>
        <button>✅ Tersedia</button>
        <button>❌ Habis</button>
        <button>📅 Berdasarkan Tanggal</button>
      </div>

      <table>
        <thead>
          <tr>
            <th>Id Obat</th>
            <th>Nama</th>
            <th>Gambar</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Fungsi</th>
            <th>Expired</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              // Periksa tanggal kadaluarsa
              $expired_date = strtotime($row['expired']);
              $today = strtotime(date('Y-m-d'));
              $warning_period = strtotime('+30 days', $today); // 30 hari sebelum expired
              
              $row_class = '';
              if ($expired_date <= $today) {
                $row_class = 'danger-expired';
              } elseif ($expired_date <= $warning_period) {
                $row_class = 'warning-expired';
              }
              
              echo "<tr class='$row_class'>
                <td>{$row['id']}</td>
                <td>{$row['nama']}</td>
                <td>" . ($row['gambar'] ? "<img src='images/obat/{$row['gambar']}' width='50' height='50' alt='{$row['nama']}'>" : "Tidak ada gambar") . "</td>
                <td>{$row['jumlah']}</td>
                <td>{$row['status']}</td>
                <td>{$row['fungsi']}</td>
                <td>{$row['expired']}</td>
                <td class='aksi'>
                  <a href='#' class='edit-btn' data-id='{$row['id']}' title='Edit'>✏️</a>
                  <a href='detail-obat.php?id={$row['id']}' title='Detail'>🔍</a>
                  <a href='hapus-obat.php?id={$row['id']}' title='Hapus' onclick='return confirm(\"Yakin ingin menghapus obat ini?\")'>🗑️</a>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='8'>Data tidak ditemukan.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Modal Input Obat (Updated) -->
<div id="inputModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Input Data Obat Baru</h3>
      <span class="close">&times;</span>
    </div>
    <form action="proses_tambah_obat.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nama">Nama Obat:</label>
        <input type="text" id="nama" name="nama" required>
      </div>
      <div class="form-group">
        <label for="gambar">Gambar Obat:</label>
        <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this, 'imagePreview')">
        <img id="imagePreview" class="img-preview" src="#" alt="Preview Gambar" style="display: none;">
      </div>
      <div class="form-group">
        <label for="jumlah">Jumlah Stok:</label>
        <input type="number" id="jumlah" name="jumlah" min="0" required>
      </div>
      <div class="form-group">
        <label for="fungsi">Fungsi Obat:</label>
        <textarea id="fungsi" name="fungsi" required></textarea>
      </div>
      <div class="form-group">
        <label for="expired">Tanggal Kadaluarsa:</label>
        <input type="date" id="expired" name="expired" required>
      </div>
      <!-- Status tidak perlu diinput karena akan otomatis diisi berdasarkan stok -->
      
      <button type="submit" class="btn-submit">Simpan Data</button>
    </form>
  </div>
</div>

<!-- Modal Edit Obat -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Data Obat</h3>
      <span class="close" id="closeEditModal">&times;</span>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" id="edit_id" name="id">
      <input type="hidden" id="gambar_lama" name="gambar_lama">
      
      <div class="form-group">
        <label for="edit_nama">Nama Obat:</label>
        <input type="text" id="edit_nama" name="nama" required>
      </div>
      <div class="form-group">
        <label for="edit_gambar">Gambar Obat:</label>
        <div id="current_image_container">
          <p>Gambar saat ini:</p>
          <img id="current_image" class="current-image" src="#" alt="Gambar saat ini">
        </div>
        <input type="file" id="edit_gambar" name="gambar" accept="image/*" onchange="previewImage(this, 'editImagePreview')">
        <img id="editImagePreview" class="img-preview" src="#" alt="Preview Gambar" style="display: none;">
      </div>
      <div class="form-group">
        <label for="edit_jumlah">Jumlah Stok:</label>
        <input type="number" id="edit_jumlah" name="jumlah" min="0" required>
      </div>
      <div class="form-group">
        <label for="edit_fungsi">Fungsi Obat:</label>
        <textarea id="edit_fungsi" name="fungsi" required></textarea>
      </div>
      <div class="form-group">
        <label for="edit_expired">Tanggal Kadaluarsa:</label>
        <input type="date" id="edit_expired" name="expired" required>
      </div>
      <!-- Status tidak perlu diinput karena akan otomatis diisi berdasarkan stok -->
      
      <button type="submit" name="update" class="btn-submit">Update Data</button>
    </form>
  </div>
</div>

<!-- Modal Detail Obat -->
<div id="detailModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Detail Obat</h3>
      <span class="close" id="closeDetailModal">&times;</span>
    </div>
    <div class="detail-container">
      <div class="detail-image-container">
        <img id="detail_image" src="#" alt="Gambar Obat">
        <div class="status-badge" id="detail_status_badge">Status</div>
      </div>
      <div class="detail-info">
        <div class="detail-header">
          <h2 id="detail_nama">Nama Obat</h2>
          <span class="detail-id">ID: <span id="detail_id"></span></span>
        </div>
        
        <div class="detail-section">
          <h4>Informasi Stok</h4>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Jumlah</span>
              <span class="info-value" id="detail_jumlah">0</span>
            </div>
            <div class="info-item">
              <span class="info-label">Status</span>
              <span class="info-value" id="detail_status">-</span>
            </div>
            <div class="info-item">
              <span class="info-label">Tanggal Kadaluarsa</span>
              <span class="info-value" id="detail_expired">-</span>
            </div>
            <div class="info-item">
              <span class="info-label">Sisa Waktu</span>
              <span class="info-value" id="detail_remaining_time">-</span>
            </div>
          </div>
        </div>
        
        <div class="detail-section">
          <h4>Kegunaan & Fungsi</h4>
          <p id="detail_fungsi" class="detail-description">-</p>
        </div>
      </div>
    </div>
    
    <div class="detail-footer">
      <button id="detail_edit_btn" class="btn btn-primary">Edit Data</button>
      <button id="detail_delete_btn" class="btn btn-danger">Hapus</button>
    </div>
  </div>
</div>

<script>
  // Modal functionality untuk Input
  const inputModal = document.getElementById("inputModal");
  const openModalBtn = document.getElementById("openModalBtn");
  const openModalLink = document.getElementById("openModal");
  const closeBtn = document.querySelector(".close");

  // Open input modal
  openModalBtn.onclick = function() {
    inputModal.style.display = "block";
  }
  
  openModalLink.onclick = function() {
    inputModal.style.display = "block";
    return false; // Prevent default link behavior
  }

  // Close input modal
  closeBtn.onclick = function() {
    inputModal.style.display = "none";
  }

  // Modal functionality untuk Edit
  const editModal = document.getElementById("editModal");
  const closeEditBtn = document.getElementById("closeEditModal");
  const editBtns = document.querySelectorAll(".edit-btn");

  // Close edit modal
  closeEditBtn.onclick = function() {
    editModal.style.display = "none";
  }

  // Edit buttons click handlers
  editBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-id');
      getObatData(id);
    });
  });

  // Close when clicking outside any modal
  window.onclick = function(event) {
    if (event.target == inputModal) {
      inputModal.style.display = "none";
    }
    if (event.target == editModal) {
      editModal.style.display = "none";
    }
  }
  
  // Image preview function
  function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      
      reader.readAsDataURL(input.files[0]);
    } else {
      preview.style.display = 'none';
    }
  }
  
  // Get obat data for editing
  function getObatData(id) {
    // AJAX request to get data
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'get-obat.php?id=' + id, true);
    
    xhr.onload = function() {
      if (this.status === 200) {
        const obat = JSON.parse(this.responseText);
        
        // Populate the form
        document.getElementById('edit_id').value = obat.id;
        document.getElementById('edit_nama').value = obat.nama;
        document.getElementById('edit_jumlah').value = obat.jumlah;
        document.getElementById('edit_fungsi').value = obat.fungsi;
        document.getElementById('edit_expired').value = obat.expired;
        document.getElementById('gambar_lama').value = obat.gambar;
        
        // Handle image
        const currentImage = document.getElementById('current_image');
        const imageContainer = document.getElementById('current_image_container');
        
        if (obat.gambar) {
          currentImage.src = 'images/obat/' + obat.gambar;
          imageContainer.style.display = 'block';
        } else {
          imageContainer.style.display = 'none';
        }
        
        // Show the modal
        editModal.style.display = 'block';
      } else {
        alert('Gagal mengambil data obat.');
      }
    };
    
    xhr.send();
  }

  // Add this to your existing script section
document.addEventListener('DOMContentLoaded', function() {
  const detailModal = document.getElementById("detailModal");
  const closeDetailBtn = document.getElementById("closeDetailModal");
  
  // Add event listeners to all detail links
  document.querySelectorAll('a[href^="detail-obat.php"]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const url = new URL(this.href);
      const id = url.searchParams.get('id');
      showObatDetail(id);
    });
  });
  
  // Close detail modal
  if (closeDetailBtn) {
    closeDetailBtn.onclick = function() {
      detailModal.style.display = "none";
    }
  }
  
  // Add click event for detail modal edit button
  const detailEditBtn = document.getElementById('detail_edit_btn');
  if (detailEditBtn) {
    detailEditBtn.addEventListener('click', function() {
      const id = document.getElementById('detail_id').textContent;
      detailModal.style.display = "none";
      getObatData(id); // Reuse existing edit function
    });
  }
  
  // Add click event for detail modal delete button
  const detailDeleteBtn = document.getElementById('detail_delete_btn');
  if (detailDeleteBtn) {
    detailDeleteBtn.addEventListener('click', function() {
      const id = document.getElementById('detail_id').textContent;
      if (confirm("Yakin ingin menghapus obat ini?")) {
        window.location.href = 'hapus-obat.php?id=' + id;
      }
    });
  }
  
  // Update window click handler to include detail modal
  window.onclick = function(event) {
    if (event.target == inputModal) {
      inputModal.style.display = "none";
    }
    if (event.target == editModal) {
      editModal.style.display = "none";
    }
    if (event.target == detailModal) {
      detailModal.style.display = "none";
    }
  }
});

// Function to get and display obat details
function showObatDetail(id) {
  // AJAX request to get data
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'get-obat.php?id=' + id, true);
  
  xhr.onload = function() {
    if (this.status === 200) {
      const obat = JSON.parse(this.responseText);
      
      // Populate the detail modal
      document.getElementById('detail_id').textContent = obat.id;
      document.getElementById('detail_nama').textContent = obat.nama;
      document.getElementById('detail_jumlah').textContent = obat.jumlah;
      document.getElementById('detail_status').textContent = obat.status;
      document.getElementById('detail_fungsi').textContent = obat.fungsi;
      document.getElementById('detail_expired').textContent = formatDate(obat.expired);
      
      // Set remaining time
      const remainingDays = calculateRemainingDays(obat.expired);
      document.getElementById('detail_remaining_time').textContent = formatRemainingTime(remainingDays);
      
      // Set status badge color
      const statusBadge = document.getElementById('detail_status_badge');
      statusBadge.textContent = obat.status;
      
      if (remainingDays < 0) {
        statusBadge.className = 'status-badge status-expired';
        statusBadge.textContent = 'Expired';
      } else if (remainingDays <= 30) {
        statusBadge.className = 'status-badge status-warning';
        statusBadge.textContent = 'Hampir Expired';
      } else if (obat.status === 'Ada') {
        statusBadge.className = 'status-badge status-ada';
      } else {
        statusBadge.className = 'status-badge status-habis';
      }
      
      // Handle image
      const detailImage = document.getElementById('detail_image');
      if (obat.gambar) {
        detailImage.src = 'images/obat/' + obat.gambar;
        detailImage.alt = obat.nama;
      } else {
        detailImage.src = 'images/obat/default.jpg';
        detailImage.alt = 'Tidak ada gambar';
      }
      
      // Show the modal
      document.getElementById('detailModal').style.display = 'block';
    } else {
      alert('Gagal mengambil detail obat.');
    }
  };
  
  xhr.send();
}

// Format date to Indonesian format
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('id-ID', options);
}

// Calculate remaining days until expiry
function calculateRemainingDays(expireDate) {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  const expiry = new Date(expireDate);
  expiry.setHours(0, 0, 0, 0);
  
  const diffTime = expiry - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
}

// Format remaining time message
function formatRemainingTime(days) {
  if (days < 0) {
    return `Sudah expired ${Math.abs(days)} hari yang lalu`;
  } else if (days === 0) {
    return "Expired hari ini";
  } else if (days === 1) {
    return "Expired besok";
  } else if (days <= 30) {
    return `${days} hari lagi`;
  } else {
    const months = Math.floor(days / 30);
    const remainingDays = days % 30;
    if (remainingDays === 0) {
      return `${months} bulan lagi`;
    } else {
      return `${months} bulan ${remainingDays} hari lagi`;
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // Get filter buttons
  const filterButtons = document.querySelectorAll('.filter-buttons button');
  
  // Add event listeners to filter buttons
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all buttons
      filterButtons.forEach(btn => btn.classList.remove('active'));
      // Add active class to clicked button
      this.classList.add('active');
      
      // Get button text to determine filter type
      const filterText = this.textContent.trim();
      
      // Apply filter based on button clicked
      if (filterText.includes('Semua')) {
        filterTable('all');
      } else if (filterText.includes('Tersedia')) {
        filterTable('available');
      } else if (filterText.includes('Habis')) {
        filterTable('nonavailable');
      }else if (filterText.includes('Berdasarkan Tanggal')) {
        showDateFilter();
      } 
    });
  });
  
  // Function to filter table rows
  function filterTable(filterType, filterValue = null) {
    const tableRows = document.querySelectorAll('table tbody tr');
    
    tableRows.forEach(row => {
      // Default to showing the row
      let showRow = true;
      
      switch(filterType) {
        case 'all':
          // Show all rows
          showRow = true;
          break;
          
        case 'available':
          // Show only rows where status is "Ada"
          const status = row.querySelector('td:nth-child(5)').textContent;
          showRow = status === 'Ada';
          break;

           case 'nonavailable':
          // Show only rows where status is "Ada"
          const stat = row.querySelector('td:nth-child(5)').textContent;
          showRow = stat === 'Habis';
          break;
          
        case 'date':
          // Filter by expiry date
          if (filterValue) {
            const expiredDate = new Date(row.querySelector('td:nth-child(7)').textContent);
            const filterDate = new Date(filterValue);
            
            // Apply date filter based on option
            if (document.getElementById('dateFilterOption').value === 'before') {
              showRow = expiredDate <= filterDate;
            } else {
              showRow = expiredDate >= filterDate;
            }
          }
          break;
          
        case 'status':
          // Filter by specific status
          if (filterValue) {
            const status = row.querySelector('td:nth-child(5)').textContent;
            showRow = status === filterValue;
          }
          break;
      }
      
      // Apply visibility based on filter result
      row.style.display = showRow ? '' : 'none';
    });
    
    // Hide any filter dropdowns
    hideFilterDropdowns();
  }
  
  // Function to show date filter dropdown
  function showDateFilter() {
    // Hide other dropdowns
    hideFilterDropdowns();
    
    // Create date filter if it doesn't exist
    if (!document.getElementById('dateFilterDropdown')) {
      const filterContainer = document.createElement('div');
      filterContainer.id = 'dateFilterDropdown';
      filterContainer.className = 'filter-dropdown';
      
      filterContainer.innerHTML = `
        <div class="filter-header">Filter Berdasarkan Tanggal Expired</div>
        <div class="filter-content">
          <select id="dateFilterOption">
            <option value="before">Expired sebelum tanggal</option>
            <option value="after">Expired setelah tanggal</option>
          </select>
          <input type="date" id="dateFilterValue">
          <button id="applyDateFilter" class="btn-apply">Terapkan</button>
          <button id="cancelDateFilter" class="btn-cancel">Batal</button>
        </div>
      `;
      
      // Insert after filter buttons
      document.querySelector('.filter-buttons').after(filterContainer);
      
      // Add event listeners
      document.getElementById('applyDateFilter').addEventListener('click', function() {
        const dateValue = document.getElementById('dateFilterValue').value;
        if (dateValue) {
          filterTable('date', dateValue);
        } else {
          alert('Silakan pilih tanggal terlebih dahulu!');
        }
      });
      
      document.getElementById('cancelDateFilter').addEventListener('click', function() {
        hideFilterDropdowns();
      });
    } else {
      // Show existing dropdown
      document.getElementById('dateFilterDropdown').style.display = 'block';
    }
  }
  
  
  
  // Function to hide all filter dropdowns
  function hideFilterDropdowns() {
    const dropdowns = document.querySelectorAll('.filter-dropdown');
    dropdowns.forEach(dropdown => {
      dropdown.style.display = 'none';
    });
  }
  
  // Highlight the "Semua" button as active by default
  if (filterButtons.length > 0) {
    filterButtons[0].classList.add('active');
  }
});
</script>

</body>
</html>