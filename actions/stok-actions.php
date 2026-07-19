<?php
// =====================================================
// STOK-ACTIONS.PHP — Handler CRUD Stok Barang
// Aksi: tambah, kurangi, hapus
// =====================================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/koneksi.php';

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

switch ($aksi) {

    // ── TAMBAH BARANG ──
    case 'tambah':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_barang'] ?? '');
            $kategori = trim($_POST['kategori'] ?? '');
            $stok = (int) ($_POST['jumlah_stok'] ?? 0);
            $harga = (float) ($_POST['harga_beli'] ?? 0);
            $satuan = trim($_POST['satuan'] ?? 'pcs');

            if (empty($nama) || $stok < 0) {
                $_SESSION['flash_error'] = 'Nama barang wajib diisi dan stok tidak boleh negatif!';
                header('Location: ../stok-tampil.php');
                exit;
            }

            $stmt = mysqli_prepare($koneksi, "INSERT INTO inventory (nama_barang, kategori, jumlah_stok, harga_beli, satuan) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssids", $nama, $kategori, $stok, $harga, $satuan);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Barang berhasil ditambahkan!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menambahkan barang: ' . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: ../stok-tampil.php');
        exit;

    // ── EDIT BARANG ──
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $nama = trim($_POST['nama_barang'] ?? '');
            $kategori = trim($_POST['kategori'] ?? '');
            $stok = (int) ($_POST['jumlah_stok'] ?? 0);
            $harga = (float) ($_POST['harga_beli'] ?? 0);
            $satuan = trim($_POST['satuan'] ?? 'pcs');

            if ($id <= 0 || empty($nama) || $stok < 0) {
                $_SESSION['flash_error'] = 'Data tidak valid! Nama barang wajib diisi dan stok tidak boleh negatif.';
                header('Location: ../stok-tampil.php');
                exit;
            }

            $stmt = mysqli_prepare($koneksi, "UPDATE inventory SET nama_barang = ?, kategori = ?, jumlah_stok = ?, harga_beli = ?, satuan = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssidsi", $nama, $kategori, $stok, $harga, $satuan, $id);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Barang "' . $nama . '" berhasil diperbarui!';
            } else {
                $_SESSION['flash_error'] = 'Gagal memperbarui barang: ' . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: ../stok-tampil.php');
        exit;

    // ── TAMBAH STOK +1 ──
    case 'tambah_stok':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $updated = mysqli_query($koneksi, "UPDATE inventory SET jumlah_stok = jumlah_stok + 1 WHERE id = $id");
            if ($updated) {
                $_SESSION['flash_success'] = 'Stok berhasil ditambahkan!';
            }
        }
        header('Location: ../stok-tampil.php');
        exit;

    // ── KURANGI STOK ──
    case 'kurangi':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $updated = mysqli_query($koneksi, "UPDATE inventory SET jumlah_stok = GREATEST(jumlah_stok - 1, 0) WHERE id = $id");
            if ($updated) {
                $_SESSION['flash_success'] = 'Stok berhasil dikurangi!';
            }
        }
        header('Location: ../stok-tampil.php');
        exit;

    // ── HAPUS BARANG ──
    case 'hapus':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $deleted = mysqli_query($koneksi, "DELETE FROM inventory WHERE id = $id");
            if ($deleted) {
                $_SESSION['flash_success'] = 'Barang berhasil dihapus!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menghapus barang!';
            }
        }
        header('Location: ../stok-tampil.php');
        exit;

    default:
        header('Location: ../stok-tampil.php');
        exit;
}
?>
