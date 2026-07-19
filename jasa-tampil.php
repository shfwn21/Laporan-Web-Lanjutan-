<?php
// =====================================================
// JASA-TAMPIL.PHP — Manajemen Order Jasa (Read, Update Status, Delete)
// Tabel order dengan dropdown status & AJAX update
// =====================================================

$currentPage = 'jasa';
$pageTitle = 'Manajemen Order Jasa';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Ambil semua order
$search = trim($_GET['search'] ?? '');
$filterStatus = $_GET['status'] ?? '';

$where = "1=1";
if (!empty($search)) {
    $searchEsc = mysqli_real_escape_string($koneksi, $search);
    $where .= " AND (nama_pelanggan LIKE '%$searchEsc%' OR jenis_jasa LIKE '%$searchEsc%' OR jenis_mesin LIKE '%$searchEsc%')";
}
if (!empty($filterStatus) && in_array($filterStatus, ['Antrian', 'Sedang Dikerjakan', 'Selesai'])) {
    $where .= " AND status = '" . mysqli_real_escape_string($koneksi, $filterStatus) . "'";
}

$qOrders = mysqli_query($koneksi, "SELECT * FROM service_orders WHERE $where ORDER BY 
    CASE status 
        WHEN 'Antrian' THEN 1 
        WHEN 'Sedang Dikerjakan' THEN 2 
        WHEN 'Selesai' THEN 3 
    END, 
    tanggal_masuk DESC");
$totalRows = mysqli_num_rows($qOrders);
?>

<!-- Flash Messages -->
<?php if (!empty($flashSuccess)): ?>
    <div class="alert-custom alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="alert-custom alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<div class="panel-card animate-fade-in">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
            Daftar Order Jasa <span style="color: var(--text-muted); font-weight: 400;">(<?= $totalRows ?>)</span>
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Search -->
            <form method="GET" class="search-box" style="margin: 0;">
                <?php if (!empty($filterStatus)): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control-custom" placeholder="Cari pelanggan / jasa..." value="<?= htmlspecialchars($search) ?>" style="padding-left: 38px; height: 38px; font-size: 0.8rem;">
                <span class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
            </form>
            <!-- Filter Status -->
            <select class="form-select-custom" style="width: auto; height: 38px; font-size: 0.8rem; padding: 5px 36px 5px 12px;" onchange="window.location.href='jasa-tampil.php?status='+this.value+'&search=<?= urlencode($search) ?>'">
                <option value="">Semua Status</option>
                <option value="Antrian" <?= $filterStatus == 'Antrian' ? 'selected' : '' ?>>Antrian</option>
                <option value="Sedang Dikerjakan" <?= $filterStatus == 'Sedang Dikerjakan' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                <option value="Selesai" <?= $filterStatus == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
            <!-- Tambah -->
            <a href="jasa-tambah.php" class="btn-primary-custom" style="height: 38px; font-size: 0.8rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Order
            </a>
        </div>
    </div>

    <div class="table-responsive-custom">
        <table class="table-custom" id="tableOrders">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pelanggan</th>
                    <th>No. HP</th>
                    <th>Mesin</th>
                    <th>Jenis Jasa</th>
                    <th>Biaya</th>
                    <th>Status</th>
                    <th>Tgl Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalRows > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($qOrders)): ?>
                        <?php
                            $statusClass = 'status-antrian';
                            if ($row['status'] == 'Sedang Dikerjakan') $statusClass = 'status-dikerjakan';
                            if ($row['status'] == 'Selesai') $statusClass = 'status-selesai';
                        ?>
                        <tr id="row-<?= $row['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_pelanggan']) ?></strong>
                                <?php if (!empty($row['deskripsi_pekerjaan'])): ?>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars(mb_strimwidth($row['deskripsi_pekerjaan'], 0, 50, '...')) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['no_hp']) ?></td>
                            <td style="font-size: 0.82rem;"><?= htmlspecialchars($row['jenis_mesin']) ?></td>
                            <td><strong style="color: var(--primary);"><?= htmlspecialchars($row['jenis_jasa']) ?></strong></td>
                            <td class="text-money">Rp <?= number_format($row['biaya'], 0, ',', '.') ?></td>
                            <td>
                                <select class="status-select <?= $statusClass ?>"
                                        data-id="<?= $row['id'] ?>"
                                        data-original="<?= $row['status'] ?>"
                                        onchange="ubahStatus(this)"
                                        <?= $row['status'] == 'Selesai' ? 'disabled' : '' ?>>
                                    <option value="Antrian" <?= $row['status'] == 'Antrian' ? 'selected' : '' ?>>Antrian</option>
                                    <option value="Sedang Dikerjakan" <?= $row['status'] == 'Sedang Dikerjakan' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                                    <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                <?= date('d M Y', strtotime($row['tanggal_masuk'])) ?>
                                <br><small style="color: var(--text-muted);"><?= date('H:i', strtotime($row['tanggal_masuk'])) ?></small>
                            </td>
                            <td>
                                <a href="javascript:void(0)"
                                   class="btn-danger-custom"
                                   onclick="confirmAction('Yakin hapus order dari <?= htmlspecialchars(addslashes($row['nama_pelanggan'])) ?>?', 'actions/jasa-actions.php?aksi=hapus&id=<?= $row['id'] ?>', {title: 'Hapus Order', btnText: 'Ya, Hapus'})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                                <h6>Belum Ada Order</h6>
                                <p>Klik "Tambah Order" untuk membuat order jasa baru</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/jasa-tampil.js"></script>

<?php require_once 'includes/footer.php'; ?>
