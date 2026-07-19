<?php
// =====================================================
// JASA-ACTIONS.PHP — Handler CRUD untuk Order Jasa
// Aksi: tambah, ubah_status, hapus
// =====================================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/koneksi.php';

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

switch ($aksi) {

    // ── TAMBAH ORDER JASA ──
    case 'tambah':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_pelanggan'] ?? '');
            $hp = trim($_POST['no_hp'] ?? '');
            $mesin = trim($_POST['jenis_mesin'] ?? '');
            $jasa = trim($_POST['jenis_jasa'] ?? '');
            $deskripsi = trim($_POST['deskripsi_pekerjaan'] ?? '');
            $biaya = (float) ($_POST['biaya'] ?? 0);

            if (empty($nama) || empty($jasa) || $biaya <= 0) {
                $_SESSION['flash_error'] = 'Nama pelanggan, jenis jasa, dan biaya wajib diisi!';
                header('Location: ../jasa-tambah.php');
                exit;
            }

            $stmt = mysqli_prepare($koneksi, "INSERT INTO service_orders (nama_pelanggan, no_hp, jenis_mesin, jenis_jasa, deskripsi_pekerjaan, biaya) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssd", $nama, $hp, $mesin, $jasa, $deskripsi, $biaya);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Order jasa berhasil ditambahkan!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menambahkan order: ' . mysqli_error($koneksi);
            }

            mysqli_stmt_close($stmt);
            header('Location: ../jasa-tampil.php');
            exit;
        }
        break;

    // ── UBAH STATUS ORDER ──
    case 'ubah_status':
        $id = (int) ($_POST['id'] ?? 0);
        $statusBaru = $_POST['status'] ?? '';
        $validStatus = ['Antrian', 'Sedang Dikerjakan', 'Selesai'];

        if ($id <= 0 || !in_array($statusBaru, $validStatus)) {
            // Jika AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
                exit;
            }
            $_SESSION['flash_error'] = 'Data status tidak valid!';
            header('Location: ../jasa-tampil.php');
            exit;
        }

        // Cek status lama untuk mendeteksi perubahan ke "Selesai"
        $qOld = mysqli_query($koneksi, "SELECT * FROM service_orders WHERE id = $id");
        $oldData = mysqli_fetch_assoc($qOld);

        if (!$oldData) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
                exit;
            }
            header('Location: ../jasa-tampil.php');
            exit;
        }

        // Update status
        $tanggalSelesai = ($statusBaru === 'Selesai') ? "NOW()" : "NULL";
        $query = "UPDATE service_orders SET status = '$statusBaru', tanggal_selesai = $tanggalSelesai WHERE id = $id";
        $updated = mysqli_query($koneksi, $query);

        // JIKA status berubah menjadi "Selesai" DAN sebelumnya BUKAN "Selesai"
        // → Otomatis tambahkan baris ke daily_incomes
        if ($updated && $statusBaru === 'Selesai' && $oldData['status'] !== 'Selesai') {
            $sumber = "Jasa: " . $oldData['jenis_jasa'] . " - " . $oldData['nama_pelanggan'];
            $jumlah = $oldData['biaya'];
            $keterangan = "Otomatis dari order #" . $id . " selesai";
            $tanggal = date('Y-m-d');

            $stmtIncome = mysqli_prepare($koneksi, "INSERT INTO daily_incomes (tanggal, sumber, jumlah, keterangan, order_id) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtIncome, "ssdsi", $tanggal, $sumber, $jumlah, $keterangan, $id);
            mysqli_stmt_execute($stmtIncome);
            mysqli_stmt_close($stmtIncome);
        }

        // Response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $updated ? true : false,
                'message' => $updated ? 'Status berhasil diubah' : 'Gagal mengubah status',
                'incomeAdded' => ($statusBaru === 'Selesai' && $oldData['status'] !== 'Selesai')
            ]);
            exit;
        }

        $_SESSION['flash_success'] = 'Status order berhasil diubah!';
        header('Location: ../jasa-tampil.php');
        exit;

    // ── HAPUS ORDER ──
    case 'hapus':
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id > 0) {
            // Hapus pemasukan terkait dulu
            mysqli_query($koneksi, "DELETE FROM daily_incomes WHERE order_id = $id");
            // Hapus order
            $deleted = mysqli_query($koneksi, "DELETE FROM service_orders WHERE id = $id");

            if ($deleted) {
                $_SESSION['flash_success'] = 'Order berhasil dihapus!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menghapus order!';
            }
        }
        header('Location: ../jasa-tampil.php');
        exit;

    default:
        header('Location: ../jasa-tampil.php');
        exit;
}
?>
