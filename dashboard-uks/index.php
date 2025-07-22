<?php
include_once '../auth_check.php';
include 'koneksi.php';

// Get current month and year
$current_month = date('m');
$current_year = date('Y');
$month_name = date('F Y');

// Get total patients count
$sql_count_patients = "SELECT COUNT(*) as total FROM pasien";
$result_count_patients = mysqli_query($conn, $sql_count_patients);
$total_patients = mysqli_fetch_assoc($result_count_patients)['total'];

// Get current month's patients count
$sql_month_patients = "SELECT COUNT(*) as month_total FROM pasien 
                      WHERE MONTH(tanggal) = '$current_month' 
                      AND YEAR(tanggal) = '$current_year'";
$result_month_patients = mysqli_query($conn, $sql_month_patients);
$month_patients = mysqli_fetch_assoc($result_month_patients)['month_total'];

// Get total medications count
$sql_count_obat = "SELECT COUNT(*) as total FROM obat";
$result_count_obat = mysqli_query($conn, $sql_count_obat);
$total_obat = mysqli_fetch_assoc($result_count_obat)['total'] ?? 0;

// Get patients by class
$sql_by_class = "SELECT kelas, COUNT(*) as count FROM pasien GROUP BY kelas ORDER BY kelas";
$result_by_class = mysqli_query($conn, $sql_by_class);
$class_data = [];
while($row = mysqli_fetch_assoc($result_by_class)) {
    $class_data[] = $row;
}

// Get recent patients
$sql_recent = "SELECT id, tanggal, nama, kelas, keluhan FROM pasien ORDER BY tanggal DESC, id DESC LIMIT 5";
$result_recent = mysqli_query($conn, $sql_recent);

// Get gender distribution
$sql_gender = "SELECT jk, COUNT(*) as count FROM pasien GROUP BY jk";
$result_gender = mysqli_query($conn, $sql_gender);
$gender_data = [];
$total_gender = 0;
while($row = mysqli_fetch_assoc($result_gender)) {
    $gender_data[$row['jk']] = $row['count'];
    $total_gender += $row['count'];
}
$male_percent = isset($gender_data['L']) ? round(($gender_data['L'] / $total_gender) * 100) : 0;
$female_percent = isset($gender_data['P']) ? round(($gender_data['P'] / $total_gender) * 100) : 0;

// PERBAIKAN 1: Get student with most visits
$sql_top_student = "SELECT nama, COUNT(*) as kunjungan 
                    FROM pasien 
                    GROUP BY nama 
                    ORDER BY kunjungan DESC 
                    LIMIT 1";
$result_top_student = mysqli_query($conn, $sql_top_student);
$top_student = mysqli_fetch_assoc($result_top_student);
$top_student_name = $top_student['nama'] ?? 'Belum ada data';
$top_student_visits = $top_student['kunjungan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UKS SMECONE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Global Styles */
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4895ef;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --danger-color: #f87171;
            --dark-color: #1e293b;
            --gray-900: #0f172a;
            --gray-800: #1e293b;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748b;
            --gray-400: #94a3b8;
            --gray-300: #cbd5e1;
            --gray-200: #e2e8f0;
            --gray-100: #f1f5f9;
            --gray-50: #f8fafc;
            --border-radius: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--gray-100);
            color: var(--gray-800);
            min-height: 100vh;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

      .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .logo-image {
            height: 4rem;
            width: auto;
            object-fit: contain;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
            transition: all 0.3s ease;
        }

        nav ul {
            list-style: none;
            padding: 0 1.5rem;
        }

        nav ul li {
            margin-bottom: 0.5rem;
            position: relative;
        }

        nav ul li a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            transition: var(--transition);
            font-weight: 500;
        }

        nav ul li a i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        nav ul li a:hover, 
        nav ul li.active > a {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        nav ul li ul {
            padding: 0.5rem 0 0 2.5rem;
            display: none;
        }

        nav ul li.active ul {
            display: block;
        }

        nav ul li ul li a {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            margin-left: 260px;
            transition: var(--transition);
        }

        /* PERBAIKAN 1: Modal scrollable */
        .modal-rinci-content {
            max-width: 700px;
            width: 90%;
            max-height: 90vh; /* Batasi tinggi maksimal */
            overflow-y: auto; /* Tambahkan scroll jika konten tinggi */
        }

        /* PERBAIKAN 1: Tambahkan scroll untuk modal surat */
        .modal-surat-content {
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .surat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .rinci-container {
            padding: 0;
        }

        .rinci-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .rinci-header h2 {
            margin: 0;
            font-size: 1.4rem;
            color: var(--gray-800);
            padding-bottom: 0;
            border-bottom: none;
        }

        .patient-profile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            margin: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .patient-profile::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .patient-info {
            z-index: 1;
        }

        .patient-id-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .patient-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .patient-class {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .patient-avatar {
            position: relative;
            z-index: 1;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .gender-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .gender-badge.male {
            background: linear-gradient(135deg, var(--accent-color), #3a86ff);
        }

        .gender-badge.female {
            background: linear-gradient(135deg, #f72585, #b5179e);
        }

        .detail-sections {
            padding: 0 1.5rem 1.5rem;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .detail-item {
            background-color: var(--gray-50);
            border-radius: 12px;
            padding: 1rem;
        }

        .detail-item.full-width {
            grid-column: 1 / span 2;
        }

        .detail-label {
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 1rem;
            color: var(--gray-800);
            font-weight: 500;
        }

        .complaint-box,
        .history-box,
        .treatment-box {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
            min-height: 60px;
        }

        .surat-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .surat-header h2 {
            margin: 0;
            font-size: 1.4rem;
            color: var(--gray-800);
            padding-bottom: 0;
            border-bottom: none;
        }

        .btn-print-surat {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .btn-print-surat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        .surat-preview {
            flex: 1;
            padding: 1.5rem;
        }

        .surat-frame {
            width: 100%;
            height: 70vh;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            background-color: white;
        }

        .header-page {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header-info {
            display: flex;
            flex-direction: column;
        }

        .header-info small {
            font-size: 0.9rem;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .header-info small i {
            margin-right: 0.5rem;
        }

        .header-info h1 {
            margin: 0;
            font-size: 1.8rem;
            color: var(--gray-900);
            font-weight: 700;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-logout {
            background: linear-gradient(135deg, var(--danger-color), #e53e3e);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(248, 113, 113, 0.3);
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(248, 113, 113, 0.4);
        }

        /* Dashboard Stats Styles */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, white, var(--gray-50));
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-radius: 0 0 0 80px;
            background-color: rgba(67, 97, 238, 0.05);
        }

        .stat-card:nth-child(1) {
            border-top: 4px solid var(--primary-color);
        }

        .stat-card:nth-child(2) {
            border-top: 4px solid var(--success-color);
        }

        .stat-card:nth-child(3) {
            border-top: 4px solid var(--warning-color);
        }

        .stat-card:nth-child(4) {
            border-top: 4px solid var(--danger-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 1.8rem;
            margin-right: 1.2rem;
            color: white;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            flex-shrink: 0;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-card:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        .stat-card:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, var(--success-color), #22c55e);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 0.95rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-details {
            font-size: 0.85rem;
            margin-top: 0.75rem;
            color: var(--gray-600);
        }

        .stat-details span {
            display: block;
        }

        .gender-distribution {
            display: flex;
            align-items: center;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .gender-type {
            width: 100px;
            font-size: 0.9rem;
        }

        .gender-bar-container {
            flex: 1;
            margin: 0 1rem;
        }

        .gender-bar {
            height: 10px;
            border-radius: 5px;
            background-color: var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .gender-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 5px;
            transition: width 1s ease-in-out;
        }

        .gender-count {
            width: 60px;
            text-align: right;
            font-weight: 600;
            color: var(--gray-700);
        }

        /* Dashboard Charts Section */
        .dashboard-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1200px) {
            .dashboard-sections {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
        }

        .chart-container h2 {
            margin-top: 0;
            font-size: 1.2rem;
            color: var(--gray-800);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
        }

        .chart-container h2 i {
            margin-right: 0.75rem;
            color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.1);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        /* Class Distribution Chart */
        .class-distribution {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 200px;
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .class-bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .class-bar {
            width: 50px;
            border-radius: 8px 8px 0 0;
            background: linear-gradient(to top, var(--primary-color), var(--accent-color));
            position: relative;
            transition: height 1s ease-in-out;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .class-count {
            position: absolute;
            top: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-weight: 600;
            color: var(--gray-800);
        }

        .class-label {
            margin-top: 10px;
            font-weight: 600;
            text-align: center;
            color: var(--gray-700);
            font-size: 0.9rem;
        }

        /* Info Text */
        .info-text {
            padding: 0.5rem;
        }

        .info-text p {
            margin: 0.8rem 0;
            color: var(--gray-700);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .highlight {
            font-weight: 600;
            color: var(--gray-800);
            background-color: rgba(67, 97, 238, 0.08);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
        }

        .highlight-success {
            background-color: rgba(74, 222, 128, 0.1);
            color: var(--success-color);
        }

        .highlight-warning {
            background-color: rgba(251, 191, 36, 0.1);
            color: var(--warning-color);
        }

        /* Recent Patients Section */
        .recent-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .recent-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--gray-800);
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .recent-header h2 i {
            margin-right: 0.75rem;
            color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.1);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .view-all-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
            transition: var(--transition);
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .view-all-link a:hover {
            background-color: rgba(67, 97, 238, 0.1);
        }

        .view-all-link a i {
            margin-left: 0.5rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .view-all-link a:hover i {
            transform: translateX(3px);
        }

        /* Table Styles */
        .recent-table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        table thead tr {
            background-color: var(--gray-100);
        }

        table th {
            padding: 1rem;
            font-weight: 600;
            text-align: left;
            color: var(--gray-700);
            font-size: 0.9rem;
            border-bottom: 1px solid var(--gray-200);
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.95rem;
            color: var(--gray-700);
        }

        table tbody tr {
            transition: var(--transition);
        }

        table tbody tr:hover {
            background-color: var(--gray-50);
        }

        table td.aksi {
            text-align: right;
        }

        table td.aksi a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            margin-left: 0.5rem;
            border-radius: 10px;
            color: white;
            transition: var(--transition);
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table td.aksi a:first-child {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        table td.aksi a:last-child {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }

        table td.aksi a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--gray-200);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
            transform: translateY(20px);
            animation: modalSlideIn 0.3s forwards;
        }

        @keyframes modalSlideIn {
            to {
                transform: translateY(0);
            }
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.8rem;
            color: var(--gray-500);
            cursor: pointer;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            color: var(--danger-color);
            background-color: rgba(248, 113, 113, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .logo,
            .sidebar nav ul li a span,
            .sidebar nav ul li ul {
                display: none;
            }
            .sidebar nav ul li a {
                justify-content: center;
                padding: 1rem;
            }
            .sidebar nav ul li a i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            .main-content {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            .header-page {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .user-section {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
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
          <li class="active"><a href="index.php"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a></li>
          <li><a href="data-pasien.php"><i class="fa-solid fa-user-group"></i> <span>Data Pasien</span></a>
            <ul>
              <li><a href="data-pasien.php"><i class="fa-solid fa-list"></i> Data Rinci</a></li>
              <li><a href="#"><i class="fa-solid fa-print"></i> Print Surat Izin</a></li>
              <li><a href="#" id="btnInputPasien"><i class="fa-solid fa-plus"></i> Input Data Pasien</a></li>
            </ul>
          </li>
          <li><a href="data-obat.php"><i class="fa-solid fa-pills"></i> <span>Data Obat</span></a>
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
        <div class="header-info">
          <small><i class="fa-solid fa-home"></i> Dashboard</small>
          <h1>Dashboard UKS SMECONE</h1>
        </div>
        
        <div class="user-section">
          <div class="user-avatar">A</div>
          <button class="btn-logout" onclick="logout()">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </button>
        </div>
      </div>

      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
          <div class="stat-info">
            <h3>Total Pasien</h3>
            <div class="stat-number"><?php echo number_format($total_patients); ?></div>
            <div class="stat-details">
              <span>Data Keseluruhan</span>
            </div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
          <div class="stat-info">
            <h3>Pasien Bulan Ini</h3>
            <div class="stat-number"><?php echo number_format($month_patients); ?></div>
            <div class="stat-details">
              <span><?php echo $month_name; ?></span>
            </div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon"><i class="fa-solid fa-capsules"></i></div>
          <div class="stat-info">
            <h3>Total Obat</h3>
            <div class="stat-number"><?php echo number_format($total_obat); ?></div>
            <div class="stat-details">
              <span>Jenis obat tersedia</span>
            </div>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon"><i class="fa-solid fa-venus-mars"></i></div>
          <div class="stat-info">
            <h3>Distribusi Gender</h3>
            <div class="gender-distribution">
                <div class="gender-type">
                    <i class="fa-solid fa-mars"></i> Laki-laki
                </div>
                <div class="gender-bar-container">
                    <div class="gender-bar">
                        <div class="gender-progress" id="maleProgress" style="width: 0%"></div>
                    </div>
                </div>
                <div class="gender-count"><?php echo isset($gender_data['L']) ? number_format($gender_data['L']) : 0; ?></div>
            </div>
            
            <div class="gender-distribution">
                <div class="gender-type">
                    <i class="fa-solid fa-venus"></i> Perempuan
                </div>
                <div class="gender-bar-container">
                    <div class="gender-bar">
                        <div class="gender-progress" id="femaleProgress" style="width: 0%"></div>
                    </div>
                </div>
                <div class="gender-count"><?php echo isset($gender_data['P']) ? number_format($gender_data['P']) : 0; ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-sections">
        <div class="chart-container">
          <h2><i class="fa-solid fa-chart-column"></i> Distribusi Pasien per Kelas</h2>
          <div class="class-distribution" id="classDistribution">
            <?php
            if (count($class_data) > 0) {
              $max_count = 0;
              foreach ($class_data as $class) {
                $max_count = max($max_count, $class['count']);
              }
              
              foreach ($class_data as $class) {
                echo "<div class='class-bar-container'>
                  <div class='class-bar' data-height='" . ($class['count'] / $max_count) * 150 . "' style='height:0;'>
                    <span class='class-count'>" . number_format($class['count']) . "</span>
                  </div>
                  <div class='class-label'>Kelas " . $class['kelas'] . "</div>
                </div>";
              }
            } else {
              echo "<p>Tidak ada data kelas.</p>";
            }
            ?>
          </div>
        </div>
        
        <div class="chart-container">
          <h2><i class="fa-solid fa-chart-pie"></i> Statistik Tambahan</h2>
          <div class="info-text">
            <!-- PERBAIKAN 2: Ganti dengan siswa paling banyak mengunjungi UKS -->
            <p>Siswa dengan kunjungan terbanyak ke UKS:</p>
            <p><span class="highlight"><?php echo $top_student_name; ?></span> dengan total <span class="highlight"><?php echo $top_student_visits; ?></span> kunjungan.</p>
            <p>Distribusi gender menunjukkan <span class="highlight highlight-success"><?php echo $male_percent; ?>%</span> laki-laki dan <span class="highlight highlight-warning"><?php echo $female_percent; ?>%</span> perempuan dari total <span class="highlight"><?php echo $total_patients; ?></span> pasien.</p>
            <p>Total jenis obat yang tersedia di UKS saat ini adalah <span class="highlight"><?php echo $total_obat; ?></span> jenis.</p>
          </div>
        </div>
      </div>

      <div class="recent-container">
        <div class="recent-header">
          <h2><i class="fa-solid fa-clock-rotate-left"></i> Kunjungan Terbaru</h2>
          <div class="view-all-link">
            <a href="data-pasien.php">Lihat Semua Data <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        
        <div class="recent-table-container">
          <table>
            <thead>
              <tr>
                <th><i class="fa-solid fa-calendar"></i> Tanggal</th>
                <th><i class="fa-solid fa-user"></i> Nama</th>
                <th><i class="fa-solid fa-graduation-cap"></i> Kelas</th>
                <th><i class="fa-solid fa-stethoscope"></i> Keluhan</th>
                <th><i class="fa-solid fa-gears"></i> Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (mysqli_num_rows($result_recent) > 0) {
                while($row = mysqli_fetch_assoc($result_recent)) {
                  $formatted_date = date('d/m/Y', strtotime($row['tanggal']));
                  echo "<tr>
                    <td>{$formatted_date}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['kelas']}</td>
                    <td>" . substr($row['keluhan'], 0, 50) . (strlen($row['keluhan']) > 50 ? "..." : "") . "</td>
                    <td class='aksi'>
                      <a href='#' onclick='showDataRinci({$row['id']})' title='Lihat'><i class='fa-solid fa-eye'></i></a>
                      <a href='#' onclick='showSuratPreview({$row['id']})' title='Print'><i class='fa-solid fa-print'></i></a>
                    </td>
                  </tr>";
                }
              } else {
                echo "<tr><td colspan='5'>Tidak ada data pasien.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Input Pasien -->
  <div id="modalInputPasien" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2><i class="fa-solid fa-user-plus"></i> Tambah Data Pasien</h2>
      
      <form method="POST" action="process-input-pasien.php" class="modal-form">
        <div class="form-group">
          <label for="tanggal"><i class="fa-solid fa-calendar"></i> Tanggal:</label>
          <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
          <label for="nama"><i class="fa-solid fa-user"></i> Nama Pasien:</label>
          <input type="text" name="nama" required placeholder="Masukkan nama lengkap">
        </div>

        <div class="form-group">
          <label for="kelas"><i class="fa-solid fa-graduation-cap"></i> Kelas:</label>
          <select name="kelas" required>
            <option value="">Pilih Kelas</option>
            <option value="X">X</option>
            <option value="XI">XI</option>
            <option value="XII">XII</option>
          </select>
        </div>

        <div class="form-group">
          <label for="jk"><i class="fa-solid fa-venus-mars"></i> Jenis Kelamin:</label>
          <select name="jk" required>
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>

        <div class="form-group">
          <label for="keluhan"><i class="fa-solid fa-stethoscope"></i> Keluhan:</label>
          <textarea name="keluhan" rows="3" required placeholder="Deskripsikan keluhan pasien"></textarea>
        </div>

        <div class="form-group">
          <label for="riwayat"><i class="fa-solid fa-clipboard-list"></i> Riwayat Penyakit:</label>
          <textarea name="riwayat" rows="2" placeholder="Riwayat penyakit (jika ada)"></textarea>
        </div>

        <div class="form-group">
          <label for="tindakan"><i class="fa-solid fa-kit-medical"></i> Tindakan:</label>
          <textarea name="tindakan" rows="2" required placeholder="Tindakan yang diberikan"></textarea>
        </div>

        <button type="submit" name="simpan"><i class="fa-solid fa-save"></i> Simpan</button>
      </form>
    </div>
  </div>

  <!-- Modal Data Rinci -->
<div id="modalDataRinci" class="modal">
    <div class="modal-content modal-rinci-content">
        <span class="close-modal-rinci">&times;</span>
        <div class="rinci-container">
            <div class="rinci-header">
                <h2><i class="fa-solid fa-user"></i> Detail Pasien</h2>
            </div>
            
            <div class="patient-profile">
                <div class="patient-info">
                    <div class="patient-id-badge">
                        ID: <span id="idPasien"></span>
                    </div>
                    <div class="patient-name">
                        <span id="namaPasien"></span>
                    </div>
                    <div class="patient-class">
                        Kelas <span id="kelasPasien"></span>
                    </div>
                </div>
                <div class="patient-avatar">
                    <div class="avatar-circle">
                        <span id="patientInitials"></span>
                    </div>
                    <div class="gender-badge" id="jkBadge">
                        <i id="jkIcon"></i>
                    </div>
                </div>
            </div>

            <div class="detail-sections">
                <div class="detail-section">
                    <div class="section-title"><i class="fa-solid fa-calendar-check"></i> Informasi Kunjungan</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Tanggal Kunjungan</div>
                            <div class="detail-value" id="tanggalPasien"></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Jenis Kelamin</div>
                            <div class="detail-value" id="jkPasien"></div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="section-title"><i class="fa-solid fa-heartbeat"></i> Kesehatan</div>
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
                    <div class="section-title"><i class="fa-solid fa-notes-medical"></i> Tindakan Medis</div>
                    <div class="detail-grid">
                        <div class="detail-item full-width">
                            <div class="detail-label">Tindakan Yang Diberikan</div>
                            <div class="detail-value treatment-box" id="tindakanPasien"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Modal Preview Surat -->
  <div id="modalPreviewSurat" class="modal">
    <div class="modal-content modal-surat-content">
      <span class="close-modal-surat">&times;</span>
      <div class="surat-container">
        <div class="surat-header">
          <h2><i class="fa-solid fa-file-prescription"></i> Preview Surat Keterangan Sakit</h2>
          <div class="surat-actions">
            <button id="btnPrintSuratReal" class="btn-print-surat"><i class="fa-solid fa-print"></i> Cetak Surat</button>
          </div>
        </div>
        
        <div class="surat-preview">
          <iframe id="suratPreviewFrame" class="surat-frame"></iframe>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Get modal elements
    const modal = document.getElementById("modalInputPasien");
    const btnInputPasien = document.getElementById("btnInputPasien");
    const closeModal = document.getElementsByClassName("close-modal")[0];
    const modalDataRinci = document.getElementById("modalDataRinci");
    const closeModalRinci = document.getElementsByClassName("close-modal-rinci")[0];
    const modalPreviewSurat = document.getElementById("modalPreviewSurat");
    const closeModalSurat = document.getElementsByClassName("close-modal-surat")[0];
    const suratPreviewFrame = document.getElementById("suratPreviewFrame");
    const btnPrintSuratReal = document.getElementById("btnPrintSuratReal");

    // Logout function
    function logout() {
      window.location.href = '../logout.php';
    }

    // Animate progress bars and charts on page load
    document.addEventListener("DOMContentLoaded", function() {
      // Animate gender distribution bars
      setTimeout(() => {
        document.getElementById("maleProgress").style.width = "<?php echo $male_percent; ?>%";
        document.getElementById("femaleProgress").style.width = "<?php echo $female_percent; ?>%";
      }, 300);
      
      // Animate class bars
      setTimeout(() => {
        const classBars = document.querySelectorAll('.class-bar');
        classBars.forEach(bar => {
          const height = bar.getAttribute('data-height');
          bar.style.height = height + 'px';
        });
      }, 500);
    });

    // Open modal when menu link is clicked
    btnInputPasien.onclick = function(e) {
      e.preventDefault();
      modal.style.display = "block";
      modal.style.opacity = 1;
    }

    // Close modal when "×" is clicked
    closeModal.onclick = function() {
      modal.style.opacity = 0;
      setTimeout(() => {
        modal.style.display = "none";
      }, 300);
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.opacity = 0;
        setTimeout(() => {
          modal.style.display = "none";
        }, 300);
      }
      if (event.target == modalDataRinci) {
        closeDataRinciModal();
      }
      if (event.target == modalPreviewSurat) {
        closeSuratPreviewModal();
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
    
    // Function to get data rinci and show in modal
 function showDataRinci(id) {
    document.getElementById("loadingSpinner").style.display = "flex";
    
    fetch('get-data-rinci.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        // Populate patient data
        document.getElementById("idPasien").textContent = data.pasien.id;
        document.getElementById("tanggalPasien").textContent = formatDate(data.pasien.tanggal);
        document.getElementById("namaPasien").textContent = data.pasien.nama;
        document.getElementById("kelasPasien").textContent = data.pasien.kelas;
        
        // Set gender
        const jkIcon = document.getElementById("jkIcon");
        const jkBadge = document.getElementById("jkBadge");
        
        if (data.pasien.jk === 'L') {
            jkIcon.className = "fa-solid fa-mars";
            jkBadge.className = "gender-badge male";
        } else {
            jkIcon.className = "fa-solid fa-venus";
            jkBadge.className = "gender-badge female";
        }
        
        // Set initials
        document.getElementById("patientInitials").textContent = getInitials(data.pasien.nama);
        
        // Handle empty data
        document.getElementById("riwayatPasien").textContent = data.pasien.riwayat || "Tidak ada riwayat penyakit";
        document.getElementById("keluhanPasien").textContent = data.pasien.keluhan || "-";
        document.getElementById("tindakanPasien").textContent = data.pasien.tindakan || "-";
        
        // Hide loading spinner
        document.getElementById("loadingSpinner").style.display = "none";
        
        // Show modal
        modalDataRinci.style.display = "block";
        setTimeout(() => {
            modalDataRinci.style.opacity = 1;
        }, 10);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById("loadingSpinner").style.display = "none";
        alert('Terjadi kesalahan saat mengambil data.');
    });
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
    
    function showSuratPreview(id) {
      // Show loading spinner
      document.getElementById("loadingSpinner").style.display = "flex";
      
      // Display modal first
      modalPreviewSurat.style.display = "block";
      setTimeout(() => {
        modalPreviewSurat.style.opacity = 1;
      }, 10);
      
      // Set iframe source
      suratPreviewFrame.src = 'cetak_surat.php?id=' + id;
      
      // Set up print button
      btnPrintSuratReal.onclick = function() {
        try {
          const iframeWindow = suratPreviewFrame.contentWindow;
          iframeWindow.focus();
          iframeWindow.print();
        } catch (e) {
          console.error('Print error:', e);
          alert('Terjadi kesalahan saat mencetak. Silahkan coba lagi.');
        }
      };
      
      // Listen for iframe load completion
      suratPreviewFrame.onload = function() {
        // Hide loading spinner
        document.getElementById("loadingSpinner").style.display = "none";
        
        // Make buttons in the iframe invisible
        try {
          const iframeDoc = suratPreviewFrame.contentDocument || suratPreviewFrame.contentWindow.document;
          const buttons = iframeDoc.querySelectorAll('.print-button, .back-button');
          buttons.forEach(button => {
            button.style.display = 'none';
          });
        } catch (e) {
          console.error('Could not modify iframe content:', e);
        }
      };
    }
    
    // Close modal function with animation
    function closeSuratPreviewModal() {
      modalPreviewSurat.style.opacity = 0;
      setTimeout(() => {
        modalPreviewSurat.style.display = "none";
        // Clear iframe src when closing
        suratPreviewFrame.src = '';
      }, 300);
    }
    
    // Close modal when "×" is clicked
    closeModalSurat.onclick = function() {
      closeSuratPreviewModal();
    }
    
    // Toggle dropdown menu
    const navItems = document.querySelectorAll('nav ul li');
    navItems.forEach(item => {
      const link = item.querySelector('a');
      const dropdown = item.querySelector('ul');
      
      if (dropdown) {
        link.addEventListener('click', function(e) {
          if (!link.getAttribute('href') || link.getAttribute('href') === '#') {
            e.preventDefault();
            item.classList.toggle('active');
          }
        });
      }
    });
    
    // Add keyboard support for closing modal with ESC key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        if (modalDataRinci.style.display === 'block') {
          closeDataRinciModal();
        }
        if (modalPreviewSurat.style.display === 'block') {
          closeSuratPreviewModal();
        }
        if (modal.style.display === 'block') {
          modal.style.opacity = 0;
          setTimeout(() => {
            modal.style.display = "none";
          }, 300);
        }
      }
    });
  </script>
</body>
</html>