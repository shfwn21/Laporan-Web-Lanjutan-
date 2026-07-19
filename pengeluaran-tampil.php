<?php
// =====================================================
// PENGELUARAN-TAMPIL.PHP — Halaman Pengeluaran Operasional
// Form Input + Tabel + Ringkasan
// =====================================================

$currentPage = 'pengeluaran';
$pageTitle = 'Pengeluaran';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Total pengeluaran bulan ini
$qTotalBulan = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM expenses WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$totalBulanIni = mysqli_fetch_assoc($qTotalBulan)['total'];

// Total pengeluaran hari ini
$qTotalHari = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM expenses WHERE tanggal = CURDATE()");
$totalHariIni = mysqli_fetch_assoc($qTotalHari)['total'];

// Ambil data pengeluaran
$qPengeluaran = mysqli_query($koneksi, "SELECT * FROM expenses ORDER BY tanggal DESC, created_at DESC LIMIT 50");
$totalRows = mysqli_num_rows($qPengeluaran);
?>

<!-- Flash Messages -->
<?php if (!empty($flashSuccess)): ?>
    <div class="alert-custom alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="alert-custom alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 animate-fade-in">
        <div class="stat-card card-expense">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Pengeluaran Hari Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalHariIni, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card-icon icon-expense">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 animate-fade-in">
        <div class="stat-card card-expense">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Total Bulan Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalBulanIni, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card-icon icon-expense">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Tambah Pengeluaran -->
<div class="panel-card animate-fade-in mb-4">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Catat Pengeluaran Baru
        </h5>
    </div>
    <div class="panel-card-body">
        <form method="POST" action="actions/keuangan-actions.php?aksi=tambah_pengeluaran">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label-custom">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control-custom" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Kategori</label>
                    <select name="kategori" class="form-select-custom">
                        <option value="">— Pilih —</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Bahan Baku">Bahan Baku</option>
                        <option value="Gaji">Gaji</option>
                        <option value="Perawatan">Perawatan</option>
                        <option value="Transport">Transport</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Deskripsi *</label>
                    <input type="text" name="deskripsi" class="form-control-custom" placeholder="Deskripsi pengeluaran" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Jumlah (Rp) *</label>
                    <input type="number" name="jumlah" class="form-control-custom" placeholder="0" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Pengeluaran -->
<div class="panel-card animate-fade-in">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
            Riwayat Pengeluaran
        </h5>
    </div>
    <div class="table-responsive-custom">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalRows > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($qPengeluaran)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="white-space: nowrap;"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <span class="badge-status badge-dikerjakan" style="font-size: 0.7rem; padding: 3px 10px;">
                                    <?= htmlspecialchars($row['kategori']) ?>
                                </span>
                            </td>
                            <td><strong><?= htmlspecialchars($row['deskripsi']) ?></strong></td>
                            <td class="text-money" style="color: var(--danger);">- Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn-danger-custom"
                                   onclick="confirmAction('Yakin hapus data pengeluaran ini?', 'actions/keuangan-actions.php?aksi=hapus_pengeluaran&id=<?= $row['id'] ?>', {title: 'Hapus Pengeluaran', btnText: 'Ya, Hapus'})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                <h6>Belum Ada Pengeluaran</h6>
                                <p>Catat pengeluaran operasional di form di atas</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
