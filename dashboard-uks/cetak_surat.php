<?php
// Include database connection
include 'koneksi.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit();
}

// Sanitize the input
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Query to get patient data
$sql = "SELECT * FROM pasien WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

// Check if data exists
if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
} else {
    echo "Data pasien tidak ditemukan.";
    exit();
}

// Query to get medicine data for this patient - check which columns exist first
$columnsToCheck = ['satuan', 'keterangan', 'dosis'];
$availableColumns = [];

// Check if columns exist in obat_pasien table
$checkColumns = mysqli_query($conn, "DESCRIBE obat_pasien");
$existingColumns = [];
while ($col = mysqli_fetch_assoc($checkColumns)) {
    $existingColumns[] = $col['Field'];
}

// Build dynamic query based on available columns
$selectFields = "o.nama AS nama_obat, op.jumlah, op.tanggal_pemberian";
foreach ($columnsToCheck as $column) {
    if (in_array($column, $existingColumns)) {
        $selectFields .= ", op.$column";
        $availableColumns[] = $column;
    }
}

$sqlObat = "SELECT $selectFields
            FROM obat_pasien op 
            JOIN obat o ON o.id = op.obat_id 
            WHERE op.pasien_id = '$id'
            ORDER BY op.tanggal_pemberian DESC";
$resultObat = mysqli_query($conn, $sqlObat);
$dataObat = [];
while ($rowObat = mysqli_fetch_assoc($resultObat)) {
    $dataObat[] = $rowObat;
}

// Format date for the letter
function formatTanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Get current date for surat
$hariIni = date('Y-m-d');
$tanggalSurat = formatTanggal($hariIni);

// Calculate recovery duration (assume 1-3 days based on how serious the complaint is)
$keluhanLength = strlen($data['keluhan']);
$durasiIstirahat = min(3, max(1, ceil($keluhanLength / 50))); // 1-3 days based on complaint length

// Format tanggal for display in letter
$tanggalKunjungan = formatTanggal($data['tanggal']);

// Calculate end date of rest period
$endDate = date('Y-m-d', strtotime($data['tanggal'] . ' + ' . $durasiIstirahat . ' days'));
$tanggalSelesai = formatTanggal($endDate);

// Get gender text
$jenisKelamin = ($data['jk'] == 'L') ? 'Laki-laki' : 'Perempuan';

// Get officer name, default to empty if not set
$petugasNama = isset($data['petugas']) ? $data['petugas'] : 'Petugas UKS';

// Generate letter number
$nomorSurat = sprintf("%03d/UKS/%s/%02d/%s", $data['id'], date('Y'), date('m'), date('Y'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Sakit - <?php echo htmlspecialchars($data['nama']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .action-buttons {
            background: #2c3e50;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-print {
            background: #3498db;
            color: white;
        }

        .btn-print:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-download {
            background: #27ae60;
            color: white;
        }

        .btn-download:hover {
            background: #219a52;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #95a5a6;
            color: white;
        }

        .btn-back:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .print-area {
            padding: 40px;
            background: white;
        }

        .letter-head {
            text-align: center;
            border-bottom: 4px double #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .school-logo {
            width: 80px;
            height: 80px;
            margin-right: 20px;
            
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .school-address {
            font-size: 13px;
            color: #7f8c8d;
            margin: 3px 0;
        }

        .letter-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 40px 0 20px;
            color: #2c3e50;
            text-decoration: underline;
            letter-spacing: 2px;
        }

        .letter-number {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #34495e;
        }

        .letter-body {
            text-align: justify;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .patient-details {
            margin: 25px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #3498db;
        }

        .patient-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-details td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 1px solid #ecf0f1;
        }

        .patient-details td:first-child {
            width: 180px;
            font-weight: 600;
            color: #2c3e50;
        }

        .medicine-section {
            margin: 25px 0;
            background: #fff8dc;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #f39c12;
        }

        .medicine-title {
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .medicine-list {
            list-style: none;
            padding: 0;
        }

        .medicine-item {
            background: white;
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            border: 1px solid #f39c12;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .medicine-name {
            font-weight: 600;
            color: #d35400;
        }

        .medicine-details {
            font-size: 13px;
            color: #7f8c8d;
        }

        .letter-closing {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .closing-left {
            flex: 1;
        }

        .closing-right {
            flex: 1;
            text-align: center;
        }

        .signature-area {
            margin-top: 80px;
        }

        .officer-name {
            font-weight: bold;
            text-decoration: underline;
            color: #2c3e50;
        }

        .letter-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            font-size: 13px;
            text-align: center;
            color: #95a5a6;
            font-style: italic;
        }

        .watermark {
            position: relative;
            overflow: hidden;
        }

        .watermark::before {
            content: 'UKS SMKN 1 PWK';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(52, 152, 219, 0.05);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                max-width: none;
                border-radius: 0;
                box-shadow: none;
            }

            .action-buttons {
                display: none;
            }

            .print-area {
                padding: 20px;
            }

            .watermark::before {
                display: block;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .print-area {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .patient-details td:first-child {
                width: 120px;
            }
        }
    </style>
</head>
    <div class="container">
        <div class="action-buttons">
            <div>
                <a href="javascript:window.close()" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak Surat
                </button>
                <button class="btn btn-download" onclick="downloadPDF()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>

        <div class="print-area watermark">
            <div class="content">
                <div class="letter-head">
                    <div class="logo-container">
                        <div class="school-logo">
                            <img src="logo.png" alt="Logo SMK Negeri 1 Purwokerto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <div class="school-name">SMK Negeri 1 Purwokerto</div>
                            <div class="school-address">Jl. DR. Soeparno No.29, Purwokerto Wetan, Kec. Purwokerto Tim.</div>
                            <div class="school-address">Kabupaten Banyumas, Jawa Tengah 53123</div>
                            <div class="school-address">Telp: (0281) 637132 | Email: info@smkn1purwokerto.sch.id</div>
                        </div>
                    </div>
                </div>

                <div class="letter-title">SURAT KETERANGAN SAKIT</div>
                <div class="letter-number">Nomor: <?= $nomorSurat ?></div>

                <div class="letter-body">
                    <p>Yang bertanda tangan di bawah ini, Petugas Unit Kesehatan Sekolah (UKS) SMK Negeri 1 Purwokerto, dengan ini menerangkan bahwa:</p>

                    <div class="patient-details">
                        <table>
                            <tr>
                                <td><i class="fas fa-user"></i> Nama Lengkap</td>
                                <td>: <?= htmlspecialchars($data['nama']) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-school"></i> Kelas/Jurusan</td>
                                <td>: <?= htmlspecialchars($data['kelas']) ?> <?= htmlspecialchars($data['jurusan']) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-venus-mars"></i> Jenis Kelamin</td>
                                <td>: <?= $jenisKelamin ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar-alt"></i> Tanggal Pemeriksaan</td>
                                <td>: <?= $tanggalKunjungan ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-stethoscope"></i> Keluhan</td>
                                <td>: <?= htmlspecialchars($data['keluhan']) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-medical-bag"></i> Tindakan Medis</td>
                                <td>: <?= htmlspecialchars($data['tindakan']) ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php if (!empty($dataObat)): ?>
                    <div class="medicine-section">
                        <div class="medicine-title">
                            <i class="fas fa-pills"></i>
                            Obat yang Diberikan
                        </div>
                        <ul class="medicine-list">
                            <?php foreach ($dataObat as $obat): ?>
                            <li class="medicine-item">
                                <div>
                                    <div class="medicine-name"><?= htmlspecialchars($obat['nama_obat']) ?></div>
                                    <div class="medicine-details">
                                        Jumlah: <?= htmlspecialchars($obat['jumlah']) ?>
                                        <?php if (isset($obat['satuan']) && !empty($obat['satuan'])): ?>
                                            <?= htmlspecialchars($obat['satuan']) ?>
                                        <?php else: ?>
                                            tablet/kapsul
                                        <?php endif; ?>
                                        
                                        <?php if (isset($obat['dosis']) && !empty($obat['dosis'])): ?>
                                            - Dosis: <?= htmlspecialchars($obat['dosis']) ?>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($obat['keterangan']) && !empty($obat['keterangan'])): ?>
                                            - <?= htmlspecialchars($obat['keterangan']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    <?= formatTanggal($obat['tanggal_pemberian']) ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <p>Berdasarkan hasil pemeriksaan yang telah dilakukan di Unit Kesehatan Sekolah (UKS) SMK Negeri 1 Purwokerto, siswa yang bersangkutan memerlukan <strong>istirahat selama <?= $durasiIstirahat ?> hari</strong> terhitung sejak tanggal <strong><?= $tanggalKunjungan ?></strong> sampai dengan <strong><?= $tanggalSelesai ?></strong>.</p>

                    <p>Demikian surat keterangan sakit ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
                </div>

                <div class="letter-closing">
                    <div class="closing-left">
                        <p style="font-style: italic; color: #7f8c8d;">
                            <i class="fas fa-info-circle"></i>
                            Catatan: Siswa diharapkan menjaga kesehatan dan segera melaporkan diri ke sekolah setelah masa istirahat berakhir.
                        </p>
                    </div>
                    <div class="closing-right">
                        <div>Purwokerto, <?= $tanggalSurat ?></div>
                        <div>Petugas UKS</div>
                        <div class="signature-area">
                            <div class="officer-name"><?= htmlspecialchars($petugasNama) ?></div>
                        </div>
                    </div>
                </div>

                <div class="letter-footer">
                    <p><i class="fas fa-shield-alt"></i> Surat keterangan ini hanya berlaku dalam lingkungan SMK Negeri 1 Purwokerto</p>
                    <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            // Create a new window for PDF generation
            const printWindow = window.open('', '_blank');
            const printContent = document.querySelector('.print-area').innerHTML;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Surat Keterangan Sakit - <?= htmlspecialchars($data['nama']) ?></title>
                    <style>
                        ${document.querySelector('style').innerHTML}
                        body { padding: 0; background: white; }
                        .container { max-width: none; border-radius: 0; box-shadow: none; }
                        .action-buttons { display: none; }
                        .print-area { padding: 20px; }
                    </style>
                </head>
                <body>
                    <div class="print-area watermark">
                        <div class="content">
                            ${printContent}
                        </div>
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        // Auto-focus print dialog when 'p' key is pressed
        document.addEventListener('keydown', function(e) {
            if (e.key === 'p' && e.ctrlKey) {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>