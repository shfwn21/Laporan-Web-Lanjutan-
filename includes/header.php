<?php
// =====================================================
// HEADER.PHP — Sidebar Admin Panel + Topbar
// Bengkel Jasa Bubut & Mesin
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi halaman — redirect ke login jika belum login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: auth/login.php');
    exit;
}

$namaUser = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$namaLengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Administrator';

// Inisial untuk avatar
$inisial = strtoupper(substr($namaLengkap, 0, 1));
if (strpos($namaLengkap, ' ') !== false) {
    $parts = explode(' ', $namaLengkap);
    $inisial = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
}

// Tanggal sekarang (format Indonesia)
$bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$tanggalSekarang = date('d') . ' ' . $bulanIndo[(int)date('m') - 1] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Manajemen Bengkel Jasa Bubut & Mesin — Dashboard Admin">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?>Bengkel Jasa Bubut</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
    <!-- Chart.js -->
    <script src="node_modules/chart.js/dist/chart.umd.js"></script>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        </div>
        <div class="sidebar-brand-text">
            <h5>Bengkel Bubut</h5>
            <small>Jasa & Mesin</small>
        </div>
    </a>

    <nav class="sidebar-menu">
        <div class="sidebar-menu-label">Menu Utama</div>

        <a href="dashboard.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'dashboard') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Dashboard
        </a>

        <div class="sidebar-menu-label">Manajemen Jasa</div>

        <a href="jasa-tampil.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'jasa') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
            Order Jasa
        </a>

        <a href="jasa-tambah.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'jasa-tambah') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Tambah Order
        </a>

        <div class="sidebar-menu-label">Inventaris</div>

        <a href="stok-tampil.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'stok') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect></svg>
            Stok Barang
        </a>

        <div class="sidebar-menu-label">Keuangan</div>

        <a href="pemasukan-tampil.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'pemasukan') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
            Pemasukan
        </a>

        <a href="pengeluaran-tampil.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'pengeluaran') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
            Pengeluaran
        </a>

        <a href="laporan-keuangan.php" class="sidebar-link <?= (isset($currentPage) && $currentPage == 'laporan') ? 'active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
            Laporan Keuangan
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inisial ?></div>
            <div class="sidebar-user-info">
                <span><?= htmlspecialchars($namaLengkap) ?></span>
                <small>Administrator</small>
            </div>
        </div>
        <a href="auth/logout.php" class="sidebar-link logout-link" style="margin-top: 12px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
        </a>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-sidebar-toggle" onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <h1 class="topbar-title"><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h1>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <?= $tanggalSekarang ?>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
