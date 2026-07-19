<?php
// =====================================================
// PEMASUKAN-TAMPIL.PHP — Halaman Pemasukan Harian
// Grafik + Tabel + Form Input Manual
// =====================================================

$currentPage = 'pemasukan';
$pageTitle = 'Pemasukan Harian';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Ambil data pemasukan (30 hari terakhir)
$qPemasukan = mysqli_query($koneksi, "SELECT * FROM daily_incomes ORDER BY tanggal DESC, created_at DESC LIMIT 50");
$totalRows = mysqli_num_rows($qPemasukan);

// Total pemasukan hari ini
$qTotal = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM daily_incomes WHERE tanggal = CURDATE()");
$totalHariIni = mysqli_fetch_assoc($qTotal)['total'];

// Total pemasukan bulan ini
$qTotalBulan = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM daily_incomes WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$totalBulanIni = mysqli_fetch_assoc($qTotalBulan)['total'];

// Data chart: pemasukan 7 hari terakhir
$chartLabels = [];
$chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d M', strtotime($tgl));
    $qChart = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM daily_incomes WHERE tanggal = '$tgl'");
    $chartData[] = (float) mysqli_fetch_assoc($qChart)['total'];
}
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
        <div class="stat-card card-income">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Pemasukan Hari Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalHariIni, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card-icon icon-income">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 animate-fade-in">
        <div class="stat-card card-queue">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Total Bulan Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalBulanIni, 0, ',', '.') ?></div>
                </div>
                <div class="stat-card-icon icon-queue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-7 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Grafik Pemasukan 7 Hari
                </h5>
            </div>
            <div class="panel-card-body">
                <div class="chart-container">
                    <canvas id="chartPemasukan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah Pemasukan Manual -->
    <div class="col-lg-5 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    Input Manual
                </h5>
            </div>
            <div class="panel-card-body">
                <form method="POST" action="actions/keuangan-actions.php?aksi=tambah_pemasukan">
                    <div class="form-group">
                        <label class="form-label-custom">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control-custom" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label-custom">Sumber Pemasukan *</label>
                        <input type="text" name="sumber" class="form-control-custom" placeholder="Contoh: Jasa Bubut Poros Custom" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label-custom">Jumlah (Rp) *</label>
                        <input type="number" name="jumlah" class="form-control-custom" placeholder="0" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label-custom">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control-custom" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Pemasukan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pemasukan -->
<div class="panel-card animate-fade-in">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
            Riwayat Pemasukan
        </h5>
    </div>
    <div class="table-responsive-custom">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Sumber</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Tipe</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalRows > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($qPemasukan)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="white-space: nowrap;"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td><strong><?= htmlspecialchars($row['sumber']) ?></strong></td>
                            <td class="text-money" style="color: var(--success);">+ Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td style="color: var(--text-secondary); font-size: 0.82rem;"><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                                <?php if ($row['order_id']): ?>
                                    <span class="badge-auto">Auto</span>
                                <?php else: ?>
                                    <span class="badge-status badge-dikerjakan" style="font-size: 0.65rem; padding: 2px 8px;">Manual</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn-danger-custom"
                                   onclick="confirmAction('Yakin hapus data pemasukan ini?', 'actions/keuangan-actions.php?aksi=hapus_pemasukan&id=<?= $row['id'] ?>', {title: 'Hapus Pemasukan', btnText: 'Ya, Hapus'})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                <h6>Belum Ada Pemasukan</h6>
                                <p>Input pemasukan manual atau selesaikan order jasa</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Data (from PHP) -->
<script>
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartData = <?= json_encode($chartData) ?>;
</script>
<script src="assets/js/pemasukan-tampil.js"></script>

<?php require_once 'includes/footer.php'; ?>
