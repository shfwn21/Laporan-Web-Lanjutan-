<?php
// =====================================================
// DASHBOARD.PHP — Halaman Utama Dashboard
// Ringkasan: Pemasukan hari ini, pengeluaran bulan ini,
//            antrian jasa aktif, chart & tabel terbaru
// =====================================================

$currentPage = 'dashboard';
$pageTitle = 'Dashboard';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// ── Query Summary Cards ──

// 1. Total Pemasukan Hari Ini
$qPemasukan = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM daily_incomes WHERE tanggal = CURDATE()");
$totalPemasukan = mysqli_fetch_assoc($qPemasukan)['total'];

// 2. Total Pengeluaran Bulan Ini
$qPengeluaran = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM expenses WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$totalPengeluaran = mysqli_fetch_assoc($qPengeluaran)['total'];

// 3. Jumlah Antrian Aktif (status bukan Selesai)
$qAntrian = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM service_orders WHERE status != 'Selesai'");
$totalAntrian = mysqli_fetch_assoc($qAntrian)['total'];

// ── 5 Order Terbaru ──
$qOrderTerbaru = mysqli_query($koneksi, "SELECT * FROM service_orders ORDER BY tanggal_masuk DESC LIMIT 5");

// ── Data Chart: Pemasukan 7 Hari Terakhir ──
$chartLabels = [];
$chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($tgl));
    $chartLabels[] = $label;

    $qChart = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah), 0) AS total FROM daily_incomes WHERE tanggal = '$tgl'");
    $chartData[] = (float) mysqli_fetch_assoc($qChart)['total'];
}
?>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-4 col-md-6 animate-fade-in">
        <div class="stat-card card-income">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Pemasukan Hari Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></div>
                    <div class="stat-card-sub"><?= date('d M Y') ?></div>
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
                    <div class="stat-card-label">Pengeluaran Bulan Ini</div>
                    <div class="stat-card-value">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
                    <div class="stat-card-sub"><?= date('F Y') ?></div>
                </div>
                <div class="stat-card-icon icon-expense">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 animate-fade-in">
        <div class="stat-card card-queue">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-label">Antrian Jasa Aktif</div>
                    <div class="stat-card-value"><?= $totalAntrian ?> Order</div>
                    <div class="stat-card-sub">Belum selesai</div>
                </div>
                <div class="stat-card-icon icon-queue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Pemasukan 7 Hari -->
    <div class="col-lg-7 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Pemasukan 7 Hari Terakhir
                </h5>
            </div>
            <div class="panel-card-body">
                <div class="chart-container">
                    <canvas id="chartPemasukan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Order Terbaru -->
    <div class="col-lg-5 animate-fade-in">
        <div class="panel-card">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Order Terbaru
                </h5>
                <a href="jasa-tampil.php" class="btn-secondary-custom" style="font-size: 0.75rem;">
                    Lihat Semua
                </a>
            </div>
            <div class="panel-card-body" style="padding: 0;">
                <div class="table-responsive-custom">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Jasa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($qOrderTerbaru) > 0): ?>
                                <?php while ($order = mysqli_fetch_assoc($qOrderTerbaru)): ?>
                                    <?php
                                        $badgeClass = 'badge-antrian';
                                        if ($order['status'] == 'Sedang Dikerjakan') $badgeClass = 'badge-dikerjakan';
                                        if ($order['status'] == 'Selesai') $badgeClass = 'badge-selesai';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['nama_pelanggan']) ?></td>
                                        <td style="font-size: 0.8rem; color: var(--text-secondary);"><?= htmlspecialchars($order['jenis_jasa']) ?></td>
                                        <td><span class="badge-status <?= $badgeClass ?>"><?= $order['status'] ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state" style="padding: 30px;">
                                            <p>Belum ada order</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Data (from PHP) -->
<script>
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartData = <?= json_encode($chartData) ?>;
</script>
<script src="assets/js/dashboard.js"></script>

<?php require_once 'includes/footer.php'; ?>
