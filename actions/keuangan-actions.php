<?php
// =====================================================
// KEUANGAN-ACTIONS.PHP — Handler CRUD Pemasukan & Pengeluaran
// Aksi: tambah_pemasukan, hapus_pemasukan,
//       tambah_pengeluaran, hapus_pengeluaran
// =====================================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/koneksi.php';

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

switch ($aksi) {

    // ── TAMBAH PEMASUKAN ──
    case 'tambah_pemasukan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $sumber = trim($_POST['sumber'] ?? '');
            $jumlah = (float) ($_POST['jumlah'] ?? 0);
            $keterangan = trim($_POST['keterangan'] ?? '');

            if (empty($sumber) || $jumlah <= 0) {
                $_SESSION['flash_error'] = 'Sumber dan jumlah pemasukan wajib diisi!';
                header('Location: ../pemasukan-tampil.php');
                exit;
            }

            $stmt = mysqli_prepare($koneksi, "INSERT INTO daily_incomes (tanggal, sumber, jumlah, keterangan) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssds", $tanggal, $sumber, $jumlah, $keterangan);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Pemasukan berhasil ditambahkan!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menambahkan pemasukan!';
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: ../pemasukan-tampil.php');
        exit;

    // ── HAPUS PEMASUKAN ──
    case 'hapus_pemasukan':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $deleted = mysqli_query($koneksi, "DELETE FROM daily_incomes WHERE id = $id");
            if ($deleted) {
                $_SESSION['flash_success'] = 'Data pemasukan berhasil dihapus!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menghapus data pemasukan!';
            }
        }
        header('Location: ../pemasukan-tampil.php');
        exit;

    // ── TAMBAH PENGELUARAN ──
    case 'tambah_pengeluaran':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $kategori = trim($_POST['kategori'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $jumlah = (float) ($_POST['jumlah'] ?? 0);

            if (empty($deskripsi) || $jumlah <= 0) {
                $_SESSION['flash_error'] = 'Deskripsi dan jumlah pengeluaran wajib diisi!';
                header('Location: ../pengeluaran-tampil.php');
                exit;
            }

            $stmt = mysqli_prepare($koneksi, "INSERT INTO expenses (tanggal, kategori, deskripsi, jumlah) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssd", $tanggal, $kategori, $deskripsi, $jumlah);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Pengeluaran berhasil ditambahkan!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menambahkan pengeluaran!';
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: ../pengeluaran-tampil.php');
        exit;

    // ── HAPUS PENGELUARAN ──
    case 'hapus_pengeluaran':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $deleted = mysqli_query($koneksi, "DELETE FROM expenses WHERE id = $id");
            if ($deleted) {
                $_SESSION['flash_success'] = 'Data pengeluaran berhasil dihapus!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menghapus data pengeluaran!';
            }
        }
        header('Location: ../pengeluaran-tampil.php');
        exit;

    default:
        header('Location: ../dashboard.php');
        exit;
}
?>
