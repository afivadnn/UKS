
    // Get modal elements
    const modal = document.getElementById("modalInputPasien");
    const btnTambahPasien = document.getElementById("btnTambahPasien");
    const btnInputPasien = document.getElementById("btnInputPasien");
    const closeModal = document.getElementsByClassName("close-modal")[0];
    const modalDataRinci = document.getElementById("modalDataRinci");
    const closeModalRinci = document.getElementsByClassName("close-modal-rinci")[0];
    const modalPreviewSurat = document.getElementById("modalPreviewSurat");
    const closeModalSurat = document.getElementsByClassName("close-modal-surat")[0];
    const suratPreviewFrame = document.getElementById("suratPreviewFrame");
    const modalEdit = document.getElementById("modalEditPasien");
    const closeModalEdit = document.getElementsByClassName("close-modal-edit")[0];
    // Open modal when "+" button is clicked
    btnTambahPasien.onclick = function() {
      modal.style.display = "block";
    }

    // Open modal when menu link is clicked
    btnInputPasien.onclick = function(e) {
      e.preventDefault();
      modal.style.display = "block";
    }

    // Close modal when "×" is clicked
    closeModal.onclick = function() {
      modal.style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    function getInitials(name) {
    if (!name) return "?";
    return name
      .split(' ')
      .map(word => word.charAt(0).toUpperCase())
      .slice(0, 2)
      .join('');
  }
  
document.addEventListener('DOMContentLoaded', function () {
  // Mengambil referensi ke elemen-elemen filter
  const btnShowAll = document.getElementById('btnShowAll');
  const btnFilterKelas = document.getElementById('btnFilterKelas');
  const filterKelas = document.getElementById('filterKelas');
  const btnFilterGender = document.getElementById('btnFilterGender');
  const filterGender = document.getElementById('filterGender');
  const btnFilterDate = document.getElementById('btnFilterDate');
  const filterDate = document.getElementById('filterDate');
  const btnFilterJurusan = document.getElementById('btnFilterJurusan');
  const filterJurusan = document.getElementById('filterJurusan');
  const tableRows = document.querySelectorAll('tbody tr');
  const obatSelect = document.getElementById('obat');
  const jumlahContainer = document.getElementById('jumlah_obat_container');
  const jumlahInput = document.getElementById('jumlah_obat');
  const stokInfo = document.getElementById('stok-tersedia');

  

  // Status filter aktif
  let activeFilters = {
    kelas: '',
    gender: '',
    date: '',
    jurusan: ''
  };

  // Perbaikan: Pisahkan dropdown dari tombol agar tidak menjadi child element
  // dan memindahkannya ke body untuk menghindari masalah z-index
  function moveDropdownsToBody() {
    const filterDropdowns = document.querySelectorAll('.filter-dropdown');
    filterDropdowns.forEach(dropdown => {
      if (dropdown.parentElement && dropdown.parentElement.classList.contains('dropdown-button')) {
        // Simpan referensi ke tombol parent
        dropdown.dataset.buttonId = dropdown.parentElement.id;
        // Pindahkan ke body
        document.body.appendChild(dropdown);
      }
    });
  }
  
  // Panggil fungsi untuk memindahkan dropdown ke body
  moveDropdownsToBody();

  // Tampilkan semua data dan reset filter
  btnShowAll.addEventListener('click', function () {
    // Reset semua filter
     activeFilters = {
      kelas: '',
      gender: '',
      date: '',
      jurusan: ''
    };

    // Reset tampilan dropdown
    filterKelas.value = '';
    filterGender.value = '';
    filterDate.value = '';
     filterJurusan.value = '';
    
    // Tampilkan semua baris
    tableRows.forEach(row => {
      row.style.display = '';
    });
    
    // Update status visual filter
    updateFilterButtonsStatus();
  });

  // Toggle dropdown kelas
  btnFilterKelas.addEventListener('click', function (e) {
    e.stopPropagation();
    positionAndToggleDropdown(filterKelas, btnFilterKelas);
  });

  // Filter berdasarkan kelas
  filterKelas.addEventListener('change', function () {
    activeFilters.kelas = this.value;
    applyFilters();
    updateFilterButtonsStatus();
  });

  // Toggle dropdown jenis kelamin
  btnFilterGender.addEventListener('click', function (e) {
    e.stopPropagation();
    positionAndToggleDropdown(filterGender, btnFilterGender);
  });

  // Filter berdasarkan jenis kelamin
  filterGender.addEventListener('change', function () {
    activeFilters.gender = this.value;
    applyFilters();
    updateFilterButtonsStatus();
  });

  // Toggle dropdown tanggal
  btnFilterDate.addEventListener('click', function (e) {
    e.stopPropagation();
    positionAndToggleDropdown(filterDate, btnFilterDate);
  });

  // Filter berdasarkan tanggal
  filterDate.addEventListener('change', function () {
    activeFilters.date = this.value;
    applyFilters();
    updateFilterButtonsStatus();
  });

    btnFilterJurusan.addEventListener('click', function (e) {
    e.stopPropagation();
    positionAndToggleDropdown(filterJurusan, btnFilterJurusan);
  });

  // Filter berdasarkan jurusan - Added handler
  filterJurusan.addEventListener('change', function () {
    activeFilters.jurusan = this.value;
    applyFilters();
    updateFilterButtonsStatus();
  });
  // Perbaikan: Fungsi untuk memposisikan dan menampilkan dropdown
  function positionAndToggleDropdown(dropdown, button) {
    // Dapatkan posisi tombol
    const buttonRect = button.getBoundingClientRect();
    
    // Atur posisi dropdown relatif terhadap tombol
    dropdown.style.position = 'absolute';
    dropdown.style.top = (buttonRect.bottom + window.scrollY) + 'px';
    dropdown.style.left = (buttonRect.left + window.scrollX) + 'px';
    dropdown.style.minWidth = buttonRect.width + 'px';
    
    // Toggle dropdown visibility
    if (dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
    } else {
      // Sembunyikan dropdown lain terlebih dahulu
      document.querySelectorAll('.filter-dropdown').forEach(item => {
        if (item !== dropdown) {
          item.style.display = 'none';
        }
      });
      dropdown.style.display = 'block';
    }
    
    // Pastikan dropdown tetap terbuka saat interaksi dengan dropdown
    dropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }

  // Function untuk menerapkan semua filter yang aktif
  function applyFilters() {
    tableRows.forEach(row => {
      const kelasCell = row.querySelector('td:nth-child(4)'); // Kolom Kelas
      const jurusanCell = row.querySelector('td:nth-child(6)'); // Kolom Jurusan
      const genderCell = row.querySelector('td:nth-child(7)'); // Kolom Jenis Kelamin - FIXED INDEX
      const dateCell = row.querySelector('td:nth-child(2)'); // Kolom Tanggal
      
      // Memeriksa apakah baris sesuai dengan semua filter yang aktif
      const kelasMatch = !activeFilters.kelas || 
                        (kelasCell && kelasCell.textContent === activeFilters.kelas);
      
      const genderMatch = !activeFilters.gender || 
                         (genderCell && genderCell.textContent === activeFilters.gender);
      
      const dateMatch = !activeFilters.date || 
                       (dateCell && dateCell.textContent === activeFilters.date);
                      
       const jurusanMatch = !activeFilters.jurusan || 
                         (jurusanCell && jurusanCell.textContent === activeFilters.jurusan);
      
      // Tampilkan baris jika sesuai dengan semua filter yang aktif
        if (kelasMatch && genderMatch && dateMatch && jurusanMatch) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  obatSelect.addEventListener('change', function() {
    if (this.value === '') {
      jumlahObatContainer.style.display = 'none';
    } else {
      jumlahObatContainer.style.display = 'block';
      
      // Get selected option
      const selectedOption = this.options[this.selectedIndex];
      const stokTersedia = selectedOption.getAttribute('data-stok');
      
      // Update stok info
      stokTersediaSpan.textContent = stokTersedia;
      
      // Set max value for jumlah_obat
      jumlahObatInput.max = stokTersedia;
      
      // Reset value to 1 or max available
      jumlahObatInput.value = Math.min(1, stokTersedia);
    }
  });
  
  // Validate that jumlah_obat doesn't exceed available stock
  jumlahObatInput.addEventListener('input', function() {
    const selectedOption = obatSelect.options[obatSelect.selectedIndex];
    const stokTersedia = parseInt(selectedOption.getAttribute('data-stok'));
    
    if (parseInt(this.value) > stokTersedia) {
      this.value = stokTersedia;
      alert('Jumlah obat tidak boleh melebihi stok tersedia!');
    }
    
    if (parseInt(this.value) < 1 && this.value !== '') {
      this.value = 1;
    }
  });
  
  // Form validation before submit
  document.querySelector('.modal-form').addEventListener('submit', function(e) {
    // If obat is selected but jumlah is invalid
    if (obatSelect.value !== '' && (parseInt(jumlahObatInput.value) < 1 || jumlahObatInput.value === '')) {
      e.preventDefault();
      alert('Jumlah obat yang diberikan harus minimal 1');
      return false;
    }
    
    // If obat is selected, confirm the stock reduction
    if (obatSelect.value !== '') {
      const selectedOption = obatSelect.options[obatSelect.selectedIndex];
      const obatName = selectedOption.text.split(' (Stok:')[0];
      const jumlah = jumlahObatInput.value;
      
      const confirmation = confirm(`Anda akan memberikan ${jumlah} ${obatName} kepada pasien ini. Stok obat akan berkurang. Lanjutkan?`);
      if (!confirmation) {
        e.preventDefault();
        return false;
      }
    }
  });
  

  // Fungsi untuk memperbarui tampilan visual tombol filter
function updateFilterButtonsStatus() {
    // Reset semua tombol ke status non-aktif
    btnFilterKelas.classList.remove('active-filter');
    btnFilterGender.classList.remove('active-filter');
    btnFilterDate.classList.remove('active-filter');
    btnFilterJurusan.classList.remove('active-filter');
    
    // Perbarui tampilan tombol berdasarkan filter yang aktif
    if (activeFilters.kelas) {
      btnFilterKelas.classList.add('active-filter');
      btnFilterKelas.textContent = `🏷️ Kelas: ${activeFilters.kelas}`;
    } else {
      btnFilterKelas.textContent = '🏷️ Berdasarkan Kelas';
      // Reset dropdown jika filter dihapus
      filterKelas.value = '';
    }
    
    if (activeFilters.gender) {
      btnFilterGender.classList.add('active-filter');
      const genderText = activeFilters.gender === 'L' ? 'Laki-laki' : 'Perempuan';
      btnFilterGender.textContent = `📈 Jenis Kelamin: ${genderText}`;
    } else {
      btnFilterGender.textContent = '📈 Berdasarkan Jenis Kelamin';
      // Reset dropdown jika filter dihapus
      filterGender.value = '';
    }
    
    if (activeFilters.date) {
      btnFilterDate.classList.add('active-filter');
      btnFilterDate.textContent = `📅 Tanggal: ${formatShortDate(activeFilters.date)}`;
    } else {
      btnFilterDate.textContent = '📅 Berdasarkan Tanggal';
      // Reset dropdown jika filter dihapus
      filterDate.value = '';
    }
    
    if (activeFilters.jurusan) {
      btnFilterJurusan.classList.add('active-filter');
      btnFilterJurusan.textContent = `🏫 Jurusan: ${activeFilters.jurusan}`;
    } else {
      btnFilterJurusan.textContent = '🏫 Berdasarkan Jurusan';
      // Reset dropdown jika filter dihapus
      filterJurusan.value = '';
    }
  }

  // Format tanggal ke format yang lebih singkat
  function formatShortDate(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
  }

  // Tambahkan kemampuan untuk menghapus filter individual dengan double click
  btnFilterKelas.addEventListener('dblclick', function(e) {
    e.stopPropagation();
    activeFilters.kelas = '';
    applyFilters();
    updateFilterButtonsStatus();
  });

  btnFilterGender.addEventListener('dblclick', function(e) {
    e.stopPropagation();
    activeFilters.gender = '';
    applyFilters();
    updateFilterButtonsStatus();
  });

  btnFilterDate.addEventListener('dblclick', function(e) {
    e.stopPropagation();
    activeFilters.date = '';
    applyFilters();
    updateFilterButtonsStatus();
  });

   btnFilterJurusan.addEventListener('dblclick', function(e) {
    e.stopPropagation();
    activeFilters.jurusan = '';
    applyFilters();
    updateFilterButtonsStatus();
  });
  // Sembunyikan semua dropdown jika klik di luar
  document.addEventListener('click', function () {
    document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
      dropdown.style.display = 'none';
    });
  });
document.getElementById("formEditPasien").addEventListener('submit', function(e) {
  // Validasi sederhana
  if (!this.nama.value.trim()) {
    e.preventDefault();
    alert('Nama pasien wajib diisi');
    return false;
  }
  return true;
});
});

// Consolidated showEditModal function - remove any duplicate definitions
function showEditModal(id) {
  // Show loading spinner
  document.getElementById("loadingSpinner").style.display = "flex";
  
  // Fetch data using AJAX
  fetch('get-data-rinci.php?id=' + id)
    .then(response => {
     if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      
      // Periksa content type
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        return response.text().then(text => {
          throw new Error(`Invalid content type. Received: ${contentType}. Response: ${text}`);
        });
      }
      
      return response.json();
    })
    .then(data => {
      // Populate modal form with data
      document.getElementById("edit_id").value = data.id;
      document.getElementById("edit_tanggal").value = data.tanggal;
      document.getElementById("edit_nama").value = data.nama;
      document.getElementById("edit_kelas").value = data.kelas;
      document.getElementById("edit_jurusan").value = data.jurusan; // Make sure jurusan is included
      document.getElementById("edit_jk").value = data.jk;
      document.getElementById("edit_keluhan").value = data.keluhan || "";
      document.getElementById("edit_riwayat").value = data.riwayat || "";
      document.getElementById("edit_tindakan").value = data.tindakan || "";
      
      // Hide loading spinner
      document.getElementById("loadingSpinner").style.display = "none";
      
      // Show modal
      const modalEdit = document.getElementById("modalEditPasien");
      modalEdit.style.display = "block";
    })
    .catch(error => {
      document.getElementById("loadingSpinner").style.display = "none";
      
      console.error('Fetch error:', error);
      alert('Terjadi kesalahan saat mengambil data: ' + error.message);
    });
}


document.addEventListener('DOMContentLoaded', function() {
  // Get all edit links
  const editLinks = document.querySelectorAll('td.aksi a[title="Edit"]');
  
  // Add event listener to each link
  editLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      // Extract ID from href
      const href = this.getAttribute('href');
      const id = href.split('id=')[1];
      
      // Show edit modal
      showEditModal(id);
    });
  });

  // Close modal when "×" is clicked
  if (document.querySelector(".close-modal-edit")) {
    document.querySelector(".close-modal-edit").onclick = function() {
      document.getElementById("modalEditPasien").style.display = "none";
    };
  }

  // Close modals when clicking outside
  window.onclick = function(event) {
    const modalEdit = document.getElementById("modalEditPasien");
    const modal = document.getElementById("modalInputPasien");
    const modalDataRinci = document.getElementById("modalDataRinci");
    
    if (event.target == modalEdit) {
      modalEdit.style.display = "none";
    }
    if (event.target == modal) {
      modal.style.display = "none";
    }
    if (event.target == modalDataRinci) {
      modalDataRinci.style.opacity = 0;
      setTimeout(() => {
        modalDataRinci.style.display = "none";
      }, 300);
    }
  };
});

  // Function to get data rinci and show in modal
function showDataRinci(id) {
  // Show loading spinner
  document.getElementById("loadingSpinner").style.display = "flex";
  
  // Fetch data using AJAX
  fetch('get-data-rinci.php?id=' + id)
    .then(response => {
      if (!response.ok) throw new Error('Network error');
      
      const contentType = response.headers.get('content-type');
      if (!contentType.includes('application/json')) {
        return response.text().then(text => {
          throw new Error(`Invalid response: ${text.substring(0, 100)}`);
        });
      }
      return response.json();
    })
    .then(data => {
      // Pastikan data pasien ada
        if (!data || typeof data !== 'object') {
        throw new Error('Invalid response format');
      }

         if (!data.pasien) {
        if (data.error) {
          throw new Error(data.error);
        } else {
          throw new Error('Patient data not found');
        }
      }
      
      const pasien = data.pasien;
      
      // Populate modal with data
      document.getElementById("idPasien").textContent = pasien.id;
      document.getElementById("tanggalPasien").textContent = formatDate(pasien.tanggal);
      document.getElementById("namaPasien").textContent = pasien.nama;
      document.getElementById("jamPelajaranDetail").textContent = pasien.jam_pelajaran || "-";
      document.getElementById("kelasPasien").textContent = pasien.kelas;
      document.getElementById("jurusanPasien").textContent = pasien.jurusan;

      // Set gender and badge style
      const jkText = pasien.jk === 'L' ? 'Laki-laki' : 'Perempuan';
      document.getElementById("jkPasien").textContent = jkText;
      
      const jkBadge = document.getElementById("jkBadge");
      if (pasien.jk === 'L') {
        jkBadge.className = "gender-badge male";
      } else {
        jkBadge.className = "gender-badge female";
      }
      
      // Set patient initials
      document.getElementById("patientInitials").textContent = getInitials(pasien.nama);
      
      // Handle empty data with placeholders
      document.getElementById("riwayatPasien").textContent = pasien.riwayat || "-";
      document.getElementById("keluhanPasien").textContent = pasien.keluhan || "-";
      document.getElementById("tindakanPasien").textContent = pasien.tindakan || "-";
      
        const btnPrintSurat = document.getElementById("btnPrintSurat");
      if (btnPrintSurat) {
        btnPrintSurat.onclick = function() {
          window.open('cetak_surat.php?id=' + pasien.id, '_blank');
        };
      }
      // Load medication data
   if (data.obat && data.obat.length > 0) {
        displayMedicationData(data.obat);
      } else {
        document.getElementById("obatDiberikan").innerHTML = 
          '<div class="no-medication">Belum ada obat yang diberikan</div>';
      }
      
      // Hide loading spinner
      document.getElementById("loadingSpinner").style.display = "none";
      
      // Show modal with animation
      const modalDataRinci = document.getElementById("modalDataRinci");
      modalDataRinci.style.display = "block";
      setTimeout(() => {
        modalDataRinci.style.opacity = 1;
      }, 10);
    })
    .catch(error => {
       console.error('Error:', error);
      document.getElementById("obatDiberikan").innerHTML = 
        `<div class="error">Gagal memuat data obat: ${error.message}</div>`;
      document.getElementById("loadingSpinner").style.display = "none";
    });
}


// Perbaikan 6: Fungsi helper untuk format tanggal
function formatDate(dateString) {
  if (!dateString) return "-";
  
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  };
  
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', options);
  } catch (e) {
    console.error('Error formatting date:', e);
    return dateString; // Return original if formatting fails
  }
}

// Perbaikan 7: Fungsi untuk mendapatkan inisial nama
function getInitials(name) {
  if (!name) return "?";
  return name
    .split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('');
}
  
  // Format date to more readable format
  function formatDate(dateString) {
    if (!dateString) return "-";
    
    const options = { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    };
    
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', options);
    } catch (e) {
      return dateString; // Return original if formatting fails
    }
  }
  
  // Add smooth close animation for modal
  function closeDataRinciModal() {
    modalDataRinci.style.opacity = 0;
    setTimeout(() => {
      modalDataRinci.style.display = "none";
    }, 300);
  }
  
  // Close modal when "×" is clicked
  closeModalRinci.onclick = function() {
    closeDataRinciModal();
  }
  
  // Add this to existing window.onclick function to close this modal too
  const existingWindowOnClick = window.onclick;
  window.onclick = function(event) {
    if (typeof existingWindowOnClick === 'function') {
      existingWindowOnClick(event); // Call the existing function if it exists
    }
    
    if (event.target == modalDataRinci) {
      closeDataRinciModal();
    }
  };
  
  // Add keyboard support for closing modal with ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modalDataRinci.style.display === 'block') {
      closeDataRinciModal();
    }
  });
  
  // Update the view links in the table to open modal instead
document.addEventListener('DOMContentLoaded', function() {
  // Get all view links
  const viewLinks = document.querySelectorAll('td.aksi a[title="Lihat"]');
  
  // Add event listener to each link
  viewLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      // Extract ID from href
      const href = this.getAttribute('href');
      const id = href.split('id=')[1];
      
      // Show data rinci in modal
      showDataRinci(id);
    });
  });

  // Get all edit links
  const editLinks = document.querySelectorAll('td.aksi a[title="Edit"]');
  
  // Add event listener to each link
  editLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      // Extract ID from href
      const href = this.getAttribute('href');
      const id = href.split('id=')[1];
      
      // Show edit modal
      showEditModal(id);
    });
  });
});

  // Close modal when "×" is clicked
  closeModalSurat.onclick = function() {
    closeSuratPreviewModal();
  }
  
  // Add this to window.onclick to close this modal too
  const prevWindowOnClick = window.onclick;
  window.onclick = function(event) {
    if (typeof prevWindowOnClick === 'function') {
      prevWindowOnClick(event);
    }
    
    if (event.target == modalPreviewSurat) {
      closeSuratPreviewModal();
    }
  };
  
  // Add keyboard support for closing modal with ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modalPreviewSurat.style.display === 'block') {
      closeSuratPreviewModal();
    }
  });
  
// Perbaikan event handler untuk tombol cetak
document.getElementById('btnPrintSurat').addEventListener('click', function(e) {
  e.preventDefault();
  const patientId = document.getElementById('idPasien').textContent;
  
  // Tutup modal detail terlebih dahulu
  closeDataRinciModal();
  
  // Buka modal cetak setelah jeda singkat
  setTimeout(() => {
    showSuratPreview(patientId);
  }, 300);
});



  document.addEventListener('DOMContentLoaded', function() {
  // Fungsi untuk memuat dan menampilkan riwayat pemberian obat
  function loadObatHistory(pasienId) {
    // AJAX request ke get-obat-pasien.php
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'get-obat-pasien.php?pasien_id=' + pasienId, true);
    
    xhr.onload = function() {
      if (this.status === 200) {
        const data = JSON.parse(this.responseText);
        displayObatHistory(data);
      } else {
        console.error('Gagal memuat riwayat pengobatan');
      }
    };
    
    xhr.onerror = function() {
      console.error('Error saat memuat riwayat pengobatan');
    };
    
    xhr.send();
  }
  
  // Fungsi untuk menampilkan riwayat pengobatan di detail pasien
  function displayObatHistory(data) {
    const container = document.getElementById('obat-history-container');
    if (!container) return;
    
    // Bersihkan container
    container.innerHTML = '<h4>Informasi Pengobatan</h4>';
    
    if (data.length === 0) {
      container.innerHTML += '<p>Tidak ada riwayat pengobatan</p>';
      return;
    }
    
    // Buat tabel riwayat pengobatan
    let tableHTML = `
      <table class="detail-obat-table">
        <thead>
          <tr>
            <th>Nama Obat</th>
            <th>Jumlah</th>
            <th>Tanggal Pemberian</th>
          </tr>
        </thead>
        <tbody>
    `;
    
    // Tambahkan baris untuk setiap data
    data.forEach(item => {
      const date = new Date(item.tanggal_pemberian);
      const formattedDate = date.toLocaleDateString('id-ID') + ' ' + 
                           date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
      
      tableHTML += `
        <tr>
          <td>${item.nama_obat}</td>
          <td>${item.jumlah}</td>
          <td>${formattedDate}</td>
        </tr>
      `;
    });
    
    tableHTML += `
        </tbody>
      </table>
    `;
    
    container.innerHTML += tableHTML;
  }

  
  
  // Load riwayat obat ketika detail pasien dibuka
  const detailPasienModal = document.getElementById('detailPasienModal');
  if (detailPasienModal) {
    detailPasienModal.addEventListener('shown.bs.modal', function(event) {
      const button = event.relatedTarget;
      const pasienId = button.getAttribute('data-id');
      if (pasienId) {
        loadObatHistory(pasienId);
      }
    });
  }
  
  // Fungsi validasi jumlah obat pada form
  function setupObatValidation() {
    const obatSelect = document.getElementById('obat');
    const jumlahInput = document.getElementById('jumlah_obat');
    
    if (!obatSelect || !jumlahInput) return;
    
    jumlahInput.addEventListener('input', function() {
      const selectedOption = obatSelect.options[obatSelect.selectedIndex];
      const stokTersedia = parseInt(selectedOption.getAttribute('data-stok'));
      const value = parseInt(this.value);
      
      if (value > stokTersedia) {
        this.classList.add('invalid');
        
        // Tambahkan pesan error jika belum ada
        let errorMsg = document.getElementById('jumlah-error-msg');
        if (!errorMsg) {
          errorMsg = document.createElement('p');
          errorMsg.id = 'jumlah-error-msg';
          errorMsg.className = 'jumlah-error';
          errorMsg.textContent = `Stok tidak mencukupi. Maksimal: ${stokTersedia}`;
          this.parentNode.appendChild(errorMsg);
        }
      } else {
        this.classList.remove('invalid');
        
        // Hapus pesan error jika ada
        const errorMsg = document.getElementById('jumlah-error-msg');
        if (errorMsg) {
          errorMsg.remove();
        }
      }
    });
  }
  
  // Setup validasi obat
  setupObatValidation();
});

function showEditModal(id) {
  // Show loading spinner
  document.getElementById("loadingSpinner").style.display = "flex";
   document.getElementById("edit_id").value = id;
  // Fetch data using AJAX
  fetch('get-data-rinci.php?id=' + id)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      // Pastikan data pasien ada
      if (!data.pasien) {
        throw new Error('Data pasien tidak ditemukan');
      }
      
      const pasien = data.pasien;
      
      // Populate modal form with data
      document.getElementById("edit_tanggal").value = pasien.tanggal;
      document.getElementById("edit_nama").value = pasien.nama;
      document.getElementById("edit_kelas").value = pasien.kelas;
      document.getElementById("edit_jurusan").value = pasien.jurusan;
      document.getElementById("edit_jam_pelajaran").value = pasien.jam_pelajaran;
      document.getElementById("edit_jk").value = pasien.jk;
      document.getElementById("edit_keluhan").value = pasien.keluhan || "";
      document.getElementById("edit_riwayat").value = pasien.riwayat || "";
      document.getElementById("edit_tindakan").value = pasien.tindakan || "";
      
     

   const riwayatContainer = document.getElementById("riwayat-obat-container");
      if (data.obat && data.obat.length > 0) {
        let html = `
          <table class="tabel-riwayat">
            <thead>
              <tr>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Tanggal Pemberian</th>
                <th>Hapus</th>
              </tr>
            </thead>
            <tbody>`;
        
        data.obat.forEach(obat => {
          const tanggal = new Date(obat.tanggal_pemberian).toLocaleString('id-ID');
          html += `
            <tr>
              <td>${obat.nama_obat}</td>
              <td>${obat.jumlah}</td>
              <td>${tanggal}</td>
              <td class="text-center">
                <input type="checkbox" 
                       name="obat_dihapus[]" 
                       value="${obat.id}">
              </td>
            </tr>`;
            html += `<input type="hidden" name="all_obat_ids[]" value="${obat.id}">`;
        });
        
        html += `</tbody></table>`;
        riwayatContainer.innerHTML = html;
      } else {
        riwayatContainer.innerHTML = '<p>Belum ada obat yang diberikan</p>';
      }
      
      // Hide loading spinner
      document.getElementById("loadingSpinner").style.display = "none";
      
      // Show modal
      document.getElementById("modalEditPasien").style.display = "block";
    })
    .catch(error => {
      console.error('Error:', error);
      // Hide loading spinner
      document.getElementById("loadingSpinner").style.display = "none";
      alert('Terjadi kesalahan saat mengambil data: ' + error.message);
    });

    
}

// Close modal function with animation
function closeEditModal() {
  modalEdit.classList.remove("show-modal");
  setTimeout(() => {
    modalEdit.style.display = "none";
  }, 300);
}

// Close modal when "×" is clicked
closeModalEdit.onclick = function() {
  closeEditModal();
}

// Add to existing window.onclick function
window.onclick = function(event) {
  // Call any previously defined window.onclick handlers
  if (typeof prevWindowOnClick === 'function') {
    prevWindowOnClick(event);
  }
  
  if (event.target == modalEdit) {
    closeEditModal();
  }
}

// Update the edit links in the table to open modal instead
document.addEventListener('DOMContentLoaded', function() {
  // Get all edit links
  const editLinks = document.querySelectorAll('td.aksi a[title="Edit"]');
  
  // Add event listener to each link
  editLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      // Extract ID from href
      const href = this.getAttribute('href');
      const id = href.split('id=')[1];
      
      // Show edit modal
      showEditModal(id);
    });
  });
});

function loadMedicationData(patientId) {
  const medicationContainer = document.getElementById("obatDiberikan");
  if (!medicationContainer) return;
  
  medicationContainer.innerHTML = '<div class="loading">Memuat data obat...</div>';
  
  fetch(`get-obat-pasien.php?pasien_id=${patientId}`)
    .then(response => {
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    })
    .then(data => {
      if (data.length === 0) {
        medicationContainer.innerHTML = '<p>Belum ada obat yang diberikan</p>';
        return;
      }
      
      let html = '<div class="medication-list"><h4>Obat yang Diberikan</h4><ul>';
      data.forEach(med => {
        html += `
          <li>
            <strong>${med.nama_obat}</strong> - 
            ${med.jumlah} - 
            ${new Date(med.tanggal_pemberian).toLocaleString('id-ID')}
          </li>
        `;
      });
      html += '</ul></div>';
      medicationContainer.innerHTML = html;
       displayMedicationData(data.obat);
    })
    .catch(error => {
      medicationContainer.innerHTML = `<p class="error">Gagal memuat: ${error.message}</p>`;
    });
}

function formatDateTime(datetime) {
  const date = new Date(datetime);
  return date.toLocaleString('id-ID');
}


function displayMedicationData(medications) {
  const container = document.getElementById("obatDiberikan");
  
  if (!medications || medications.length === 0) {
    container.innerHTML = '<div class="no-medication">Belum ada obat yang diberikan</div>';
    return;
  }

  let html = `
    <div class="medication-section">
      <h3>Obat yang Diberikan</h3>
      <table class="medication-table">
        <thead>
          <tr>
            <th>Nama Obat</th>
            <th>Jumlah</th>
            <th>Diberikan Pada</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  medications.forEach(med => {
    const date = new Date(med.tanggal_pemberian);
     const formattedDate = date.toLocaleString('id-ID', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
    html += `
      <tr>
        <td>${med.nama_obat}</td>
        <td>${med.jumlah}</td>
       <td>${formattedDate}</td>
      </tr>
    `;
  });
  
  html += `</tbody></table></div>`;
  container.innerHTML = html;
}
// Update the showDataRinci function to load medication history
const originalShowDataRinci = showDataRinci;
showDataRinci = function(id) {
  // Call the original function first
  originalShowDataRinci(id);
  
  // Then load medication history
  // Add a small delay to ensure the modal is visible
  setTimeout(() => {
    loadMedicationHistory(id);
  }, 500);
};

// Add this to the obat dropdown event listener
document.addEventListener('DOMContentLoaded', function() {
   const obatSelect = document.getElementById('obat');
  const jumlahContainer = document.getElementById('jumlah_obat_container'); // Pastikan ID ini konsisten
  const jumlahInput = document.getElementById('jumlah_obat');
  const stokInfo = document.getElementById('stok-tersedia');
  
 if (obatSelect) {
    obatSelect.addEventListener('change', function() {
      const selectedValue = this.value;
      document.getElementById('edit_obat_id').value = selectedValue; 
      
      if (selectedValue === '') {
        jumlahContainer.style.display = 'none';
      }else {
        jumlahContainer.style.display = 'block';
        
        // Get selected option
        const selectedOption = this.options[this.selectedIndex];
        const stokTersedia = selectedOption.getAttribute('data-stok');
        
        console.log('Selected option:', selectedOption.text, 'Stock:', stokTersedia); // Debug log
        
        // Update stok info
        if (stokInfo) {
          stokInfo.textContent = stokTersedia;
        }
        
        // Set max value for jumlah_obat
        if (jumlahInput) {
          jumlahInput.max = stokTersedia;
          jumlahInput.value = Math.min(1, parseInt(stokTersedia));
        }
      }
    });
  }
  
  // Validate that jumlah_obat doesn't exceed available stock
  if (jumlahInput) {
    jumlahInput.addEventListener('input', function() {
      if (obatSelect.value) {
        const selectedOption = obatSelect.options[obatSelect.selectedIndex];
        const stokTersedia = parseInt(selectedOption.getAttribute('data-stok'));
        
        if (parseInt(this.value) > stokTersedia) {
          this.value = stokTersedia;
          alert('Jumlah obat tidak boleh melebihi stok tersedia!');
        }
        
        if (parseInt(this.value) < 1 && this.value !== '') {
          this.value = 1;
        }
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const modalForm = document.querySelector('.modal-form');
  if (modalForm) {
    modalForm.addEventListener('submit', function(e) {
      const obatSelect = document.getElementById('obat');
      const jumlahInput = document.getElementById('jumlah_obat');
      
      // If obat is selected but jumlah is invalid
      if (obatSelect.value !== '' && (parseInt(jumlahInput.value) < 1 || jumlahInput.value === '')) {
        e.preventDefault();
        alert('Jumlah obat yang diberikan harus minimal 1');
        return false;
      }
      
      // If obat is selected, confirm the stock reduction
      if (obatSelect.value !== '') {
        const selectedOption = obatSelect.options[obatSelect.selectedIndex];
        const obatName = selectedOption.text.split(' (Stok:')[0];
        const jumlah = jumlahInput.value;
        
        const confirmation = confirm(`Anda akan memberikan ${jumlah} ${obatName} kepada pasien ini. Stok obat akan berkurang. Lanjutkan?`);
        if (!confirmation) {
          e.preventDefault();
          return false;
        }
      }
    });
  }
});


// Filter out medications with no stock when loading the dropdown
function updateMedicationDropdowns() {
  const obatSelects = document.querySelectorAll('select[name="obat"]');
  obatSelects.forEach(select => {
    Array.from(select.options).forEach(option => {
      if (option.value && parseInt(option.getAttribute('data-stok') || '0') <= 0) {
        option.disabled = true;
        option.text += ' (Stok Habis)';
      }
    });
  });
}

// Call this when document is loaded
document.addEventListener('DOMContentLoaded', updateMedicationDropdowns);
