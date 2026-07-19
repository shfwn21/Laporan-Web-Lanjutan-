<?php
// =====================================================
// STOK-TAMPIL.PHP — Halaman Stok Barang / Inventaris
// CRUD: Tambah, Edit, Kurangi Stok, Tambah Stok, Hapus
// Fitur: Pencarian & Filter Kategori
// =====================================================

$currentPage = 'stok';
$pageTitle = 'Stok Barang / Inventaris';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Ambil parameter pencarian
$search = trim($_GET['search'] ?? '');
$filterKategori = trim($_GET['kategori'] ?? '');

// Build query dengan filter
$sql = "SELECT * FROM inventory WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (nama_barang LIKE ? OR kategori LIKE ? OR satuan LIKE ?)";
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

if (!empty($filterKategori)) {
    $sql .= " AND kategori = ?";
    $params[] = $filterKategori;
    $types .= 's';
}

$sql .= " ORDER BY kategori, nama_barang";

// Execute with prepared statement
$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$qStok = mysqli_stmt_get_result($stmt);
$totalStok = mysqli_num_rows($qStok);

// Ambil total keseluruhan (tanpa filter) untuk info
$qTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM inventory");
$totalAll = mysqli_fetch_assoc($qTotal)['total'];

// Ambil daftar kategori unik untuk filter dropdown
$qKategori = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM inventory WHERE kategori != '' ORDER BY kategori");
$kategoriList = [];
while ($k = mysqli_fetch_assoc($qKategori)) {
    $kategoriList[] = $k['kategori'];
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

<!-- Form Tambah Barang -->
<div class="panel-card animate-fade-in mb-4">
    <div class="panel-card-header">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            Tambah Barang Baru
        </h5>
    </div>
    <div class="panel-card-body">
        <form method="POST" action="actions/stok-actions.php?aksi=tambah" id="formTambahStok">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-custom">Nama Barang *</label>
                    <input type="text" name="nama_barang" class="form-control-custom" placeholder="Nama barang" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Kategori</label>
                    <select name="kategori" class="form-select-custom">
                        <option value="">— Pilih —</option>
                        <option value="Alat Potong">Alat Potong</option>
                        <option value="Bahan Baku">Bahan Baku</option>
                        <option value="Sparepart">Sparepart</option>
                        <option value="Cairan">Cairan</option>
                        <option value="Pelumas">Pelumas</option>
                        <option value="APD">APD</option>
                        <option value="Abrasif">Abrasif</option>
                        <option value="Las">Las</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Jumlah Stok *</label>
                    <input type="number" name="jumlah_stok" class="form-control-custom" placeholder="0" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" class="form-control-custom" placeholder="0" min="0">
                </div>
                <div class="col-md-1">
                    <label class="form-label-custom">Satuan</label>
                    <input type="text" name="satuan" class="form-control-custom" placeholder="pcs" value="pcs">
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

<!-- Tabel Stok -->
<div class="panel-card animate-fade-in">
    <div class="panel-card-header" style="flex-wrap: wrap; gap: 16px;">
        <h5>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect></svg>
            Daftar Stok Barang
            <span style="color: var(--text-muted); font-weight: 400;">
                (<?= $totalStok ?><?= ($totalStok != $totalAll) ? ' / ' . $totalAll : '' ?>)
            </span>
        </h5>

        <!-- Search & Filter Bar -->
        <div class="stok-search-bar">
            <form method="GET" action="stok-tampil.php" id="formSearchStok" class="stok-search-form">
                <!-- Search Input -->
                <div class="search-box" style="max-width: 260px;">
                    <span class="search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control-custom"
                           placeholder="Cari nama barang..."
                           value="<?= htmlspecialchars($search) ?>"
                           id="inputSearchStok"
                           autocomplete="off">
                </div>

                <!-- Kategori Filter -->
                <select name="kategori" class="form-select-custom stok-filter-select" id="filterKategoriStok">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= htmlspecialchars($kat) ?>" <?= ($filterKategori === $kat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Tombol Cari -->
                <button type="submit" class="btn-primary-custom btn-search-stok">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Cari
                </button>

                <!-- Tombol Reset -->
                <?php if (!empty($search) || !empty($filterKategori)): ?>
                    <a href="stok-tampil.php" class="btn-secondary-custom btn-reset-stok" title="Reset pencarian">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Info Pencarian Aktif -->
    <?php if (!empty($search) || !empty($filterKategori)): ?>
        <div class="search-info-bar">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>
                Menampilkan <strong><?= $totalStok ?></strong> hasil
                <?php if (!empty($search)): ?>
                    untuk "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php endif; ?>
                <?php if (!empty($filterKategori)): ?>
                    di kategori <strong><?= htmlspecialchars($filterKategori) ?></strong>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="table-responsive-custom">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga Beli</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalStok > 0): ?>
                    <?php $no = 1; while ($item = mysqli_fetch_assoc($qStok)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item['nama_barang']) ?></strong>
                                <?php if ($item['jumlah_stok'] <= 5 && $item['jumlah_stok'] > 0): ?>
                                    <span class="badge-stok-rendah ms-1">Stok Rendah</span>
                                <?php elseif ($item['jumlah_stok'] == 0): ?>
                                    <span class="badge-stok-rendah ms-1">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-secondary);"><?= htmlspecialchars($item['kategori']) ?></td>
                            <td>
                                <span style="font-weight: 700; color: <?= $item['jumlah_stok'] <= 5 ? 'var(--danger)' : 'var(--text-primary)' ?>;">
                                    <?= $item['jumlah_stok'] ?>
                                </span>
                            </td>
                            <td class="text-money">Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($item['satuan']) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <!-- Tambah +1 -->
                                    <a href="javascript:void(0)"
                                       class="btn-success-custom"
                                       title="Tambah stok 1"
                                       onclick="confirmAction('Tambah stok <?= htmlspecialchars(addslashes($item['nama_barang'])) ?> sebanyak 1?', 'actions/stok-actions.php?aksi=tambah_stok&id=<?= $item['id'] ?>', {title: 'Tambah Stok', btnText: 'Ya, Tambah', type: 'warning'})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        +1
                                    </a>
                                    <!-- Kurangi -1 -->
                                    <a href="javascript:void(0)"
                                       class="btn-secondary-custom"
                                       title="Kurangi stok 1"
                                       onclick="confirmAction('Kurangi stok <?= htmlspecialchars(addslashes($item['nama_barang'])) ?> sebanyak 1?', 'actions/stok-actions.php?aksi=kurangi&id=<?= $item['id'] ?>', {title: 'Kurangi Stok', btnText: 'Ya, Kurangi', type: 'warning'})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        -1
                                    </a>
                                    <!-- Edit -->
                                    <button type="button"
                                            class="btn-edit-custom"
                                            title="Edit barang"
                                            onclick="openEditModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['nama_barang']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($item['kategori']), ENT_QUOTES) ?>', <?= (int)$item['jumlah_stok'] ?>, <?= (float)$item['harga_beli'] ?>, '<?= htmlspecialchars(addslashes($item['satuan']), ENT_QUOTES) ?>')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </button>
                                    <!-- Hapus -->
                                    <a href="javascript:void(0)"
                                       class="btn-danger-custom"
                                       onclick="confirmAction('Yakin hapus barang <?= htmlspecialchars(addslashes($item['nama_barang'])) ?>?', 'actions/stok-actions.php?aksi=hapus&id=<?= $item['id'] ?>', {title: 'Hapus Barang', btnText: 'Ya, Hapus'})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <?php if (!empty($search) || !empty($filterKategori)): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    <h6>Tidak Ditemukan</h6>
                                    <p>Tidak ada barang yang cocok dengan pencarian Anda.</p>
                                    <a href="stok-tampil.php" class="btn-secondary-custom" style="margin-top: 12px; display: inline-flex;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        Reset Pencarian
                                    </a>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect></svg>
                                    <h6>Belum Ada Barang</h6>
                                    <p>Tambahkan barang baru di form di atas</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- Modal Edit Barang                                      -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="editBarangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08); padding: 20px 24px;">
                <h5 class="modal-title" style="color: #f1f5f9; font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit Barang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="actions/stok-actions.php?aksi=edit" id="formEditStok">
                <div class="modal-body" style="padding: 24px;">
                    <input type="hidden" name="id" id="editId">

                    <div class="form-group">
                        <label class="form-label-custom">Nama Barang *</label>
                        <input type="text" name="nama_barang" id="editNamaBarang" class="form-control-custom" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Kategori</label>
                                <select name="kategori" id="editKategori" class="form-select-custom">
                                    <option value="">— Pilih —</option>
                                    <option value="Alat Potong">Alat Potong</option>
                                    <option value="Bahan Baku">Bahan Baku</option>
                                    <option value="Sparepart">Sparepart</option>
                                    <option value="Cairan">Cairan</option>
                                    <option value="Pelumas">Pelumas</option>
                                    <option value="APD">APD</option>
                                    <option value="Abrasif">Abrasif</option>
                                    <option value="Las">Las</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Jumlah Stok *</label>
                                <input type="number" name="jumlah_stok" id="editJumlahStok" class="form-control-custom" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Harga Beli (Rp)</label>
                                <input type="number" name="harga_beli" id="editHargaBeli" class="form-control-custom" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Satuan</label>
                                <input type="text" name="satuan" id="editSatuan" class="form-control-custom">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); padding: 16px 24px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; border-radius: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script: Stok Barang -->
<script src="assets/js/stok.js"></script>

<?php require_once 'includes/footer.php'; ?>
