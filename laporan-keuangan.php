<?php
// =====================================================
// LAPORAN-KEUANGAN.PHP — Halaman Laporan Keuangan
// Gabungkan pemasukan & pengeluaran → Net Profit
// Filter berdasarkan bulan/tahun + Chart perbandingan
// =====================================================

$currentPage = 'laporan';
$pageTitle = 'Laporan Keuangan';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Filter bulan & tahun
$filterBulan = (int) ($_GET['bulan'] ?? date('m'));
$filterTahun = (int) ($_GET['tahun'] ?? date('Y'));

// ── QUERY PEMASUKAN BULAN INI ──
$qPemasukan = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah), 0) AS total 
    FROM daily_incomes 
    WHERE MONTH(tanggal) = $filterBulan AND YEAR(tanggal) = $filterTahun
");
$totalPemasukan = (float) mysqli_fetch_assoc($qPemasukan)['total'];

// ── QUERY PENGELUARAN BULAN INI ──
$qPengeluaran = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah), 0) AS total 
    FROM expenses 
    WHERE MONTH(tanggal) = $filterBulan AND YEAR(tanggal) = $filterTahun
");
$totalPengeluaran = (float) mysqli_fetch_assoc($qPengeluaran)['total'];

// ── NET PROFIT ──
$netProfit = $totalPemasukan - $totalPengeluaran;
$profitClass = $netProfit >= 0 ? 'profit-positive' : 'profit-negative';
$profitIcon = $netProfit >= 0 ? '↑' : '↓';

// ── DATA CHART: Pemasukan vs Pengeluaran per Hari ──
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $filterBulan, $filterTahun);
$chartLabels = [];
$chartPemasukan = [];
$chartPengeluaran = [];

for ($d = 1; $d <= $daysInMonth; $d++) {
    $tgl = sprintf('%04d-%02d-%02d', $filterTahun, $filterBulan, $d);
    $chartLabels[] = $d;

    $qI = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS t FROM daily_incomes WHERE tanggal = '$tgl'");
    $chartPemasukan[] = (float) mysqli_fetch_assoc($qI)['t'];

    $qE = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS t FROM expenses WHERE tanggal = '$tgl'");
    $chartPengeluaran[] = (float) mysqli_fetch_assoc($qE)['t'];
}

// ── DETAIL PEMASUKAN ──
$qDetailPemasukan = mysqli_query($koneksi, "
    SELECT * FROM daily_incomes 
    WHERE MONTH(tanggal) = $filterBulan AND YEAR(tanggal) = $filterTahun 
    ORDER BY tanggal DESC
");

// ── DETAIL PENGELUARAN ──
$qDetailPengeluaran = mysqli_query($koneksi, "
    SELECT * FROM expenses 
    WHERE MONTH(tanggal) = $filterBulan AND YEAR(tanggal) = $filterTahun 
    ORDER BY tanggal DESC
");

// Nama bulan Indonesia
$namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<!-- Filter Bulan/Tahun -->
<div class="panel-card animate-fade-in mb-4">
    <div class="panel-card-body" style="padding: 16px 24px;">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <label class="form-label-custom" style="margin: 0; white-space: nowrap;">Filter Periode:</label>
            <select name="bulan" class="form-select-custom" style="width: auto; padding: 8px 36px 8px 12px;">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $filterBulan ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
                <?php endfor; ?>
            </select>
            <select name="tahun" class="form-select-custom" style="width: auto; padding: 8px 36px 8px 12px;">
                <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $filterTahun ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn-primary-custom" style="padding: 8px 18px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Tampilkan
            </button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-4 col-md-6 animate-fade-in">
        <div class="stat-card card-income">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Total Pemasukan</div>
                    <div class="stat-card-value">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></div>
                    <div class="stat-card-sub"><?= $namaBulan[$filterBulan] ?> <?= $filterTahun ?></div>
                </div>
                <div class="stat-card-icon icon-income">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 animate-fade-in">
        <div class="stat-card card-expense">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Total Pengeluaran</div>
                    <div class="stat-card-value">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
                    <div class="stat-card-sub"><?= $namaBulan[$filterBulan] ?> <?= $filterTahun ?></div>
                </div>
                <div class="stat-card-icon icon-expense">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 animate-fade-in">
        <div class="stat-card" style="border-color: <?= $netProfit >= 0 ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)' ?>;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 16px 16px 0 0; background: <?= $netProfit >= 0 ? 'linear-gradient(90deg, #22c55e, #16a34a)' : 'linear-gradient(90deg, #ef4444, #dc2626)' ?>;"></div>
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Net Profit <?= $profitIcon ?></div>
                    <div class="stat-card-value <?= $profitClass ?>">
                        <?= $netProfit >= 0 ? '+' : '-' ?> Rp <?= number_format(abs($netProfit), 0, ',', '.') ?>
                    </div>
                    <div class="stat-card-sub"><?= $netProfit >= 0 ? 'Keuntungan Bersih' : 'Kerugian Bersih' ?></div>
                </div>
                <div class="stat-card-icon" style="background: <?= $netProfit >= 0 ? 'var(--success-bg)' : 'var(--danger-bg)' ?>; color: <?= $netProfit >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Perbandingan -->
<div class="panel-card animate-fade-in mb-4">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            Grafik Pemasukan vs Pengeluaran — <?= $namaBulan[$filterBulan] ?> <?= $filterTahun ?>
        </h5>
    </div>
    <div class="panel-card-body">
        <div class="chart-container" style="height: 350px;">
            <canvas id="chartLaporan"></canvas>
        </div>
    </div>
</div>

<!-- Ringkasan Keuangan -->
<div class="row g-4">
    <div class="col-lg-6 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    Detail Pemasukan
                </h5>
            </div>
            <div class="table-responsive-custom" style="max-height: 400px; overflow-y: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Sumber</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($qDetailPemasukan) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($qDetailPemasukan)): ?>
                                <tr>
                                    <td style="white-space: nowrap; font-size: 0.8rem;"><?= date('d M', strtotime($row['tanggal'])) ?></td>
                                    <td style="font-size: 0.82rem;"><?= htmlspecialchars($row['sumber']) ?></td>
                                    <td class="text-money" style="color: var(--success); white-space: nowrap;">+ Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
                    Detail Pengeluaran
                </h5>
            </div>
            <div class="table-responsive-custom" style="max-height: 400px; overflow-y: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($qDetailPengeluaran) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($qDetailPengeluaran)): ?>
                                <tr>
                                    <td style="white-space: nowrap; font-size: 0.8rem;"><?= date('d M', strtotime($row['tanggal'])) ?></td>
                                    <td style="font-size: 0.82rem;">
                                        <?= htmlspecialchars($row['deskripsi']) ?>
                                        <?php if (!empty($row['kategori'])): ?>
                                            <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['kategori']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-money" style="color: var(--danger); white-space: nowrap;">- Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Akhir -->
<div class="panel-card animate-fade-in mt-4">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
            Ringkasan — <?= $namaBulan[$filterBulan] ?> <?= $filterTahun ?>
        </h5>
    </div>
    <div class="panel-card-body">
        <div class="summary-row">
            <span style="color: #fff;">Total Pemasukan</span>
            <span class="text-money" style="color: var(--success);">+ Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></span>
        </div>
        <div class="summary-row">
            <span style="color: #fff;">Total Pengeluaran</span>
            <span class="text-money" style="color: var(--danger);">- Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></span>
        </div>
        <div class="summary-row total" style="border-top: 2px solid var(--border-light);">
            <span style="font-size: 1.05rem; color: #fff;">
                <?= $netProfit >= 0 ? 'Keuntungan Bersih (Net Profit)' : 'Kerugian Bersih (Net Loss)' ?>
            </span>
            <span class="text-money <?= $profitClass ?>" style="font-size: 1.2rem;">
                <?= $netProfit >= 0 ? '+' : '-' ?> Rp <?= number_format(abs($netProfit), 0, ',', '.') ?>
            </span>
        </div>
    </div>
</div>

<!-- Chart.js Data (from PHP) -->
<script>
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartPemasukan = <?= json_encode($chartPemasukan) ?>;
    const chartPengeluaran = <?= json_encode($chartPengeluaran) ?>;
    const namaBulanFilter = '<?= $namaBulan[$filterBulan] ?>';
</script>
<script src="assets/js/laporan-keuangan.js"></script>

<?php require_once 'includes/footer.php'; ?>