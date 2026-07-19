<?php
// =====================================================
// JASA-TAMBAH.PHP — Form Tambah Order Jasa Baru
// =====================================================

$currentPage = 'jasa-tambah';
$pageTitle = 'Tambah Order Jasa';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// Flash messages
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>

<!-- Flash Error -->
<?php if (!empty($flashError)): ?>
    <div class="alert-custom alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="panel-card animate-fade-in">
            <div class="panel-card-header">
                <h5>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    Form Order Jasa Baru
                </h5>
                <a href="jasa-tampil.php" class="btn-secondary-custom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Kembali
                </a>
            </div>
            <div class="panel-card-body">
                <form method="POST" action="actions/jasa-actions.php?aksi=tambah" id="formTambahOrder" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom" for="nama_pelanggan">Nama Pelanggan <span style="color: var(--danger);">*</span></label>
                                <input type="text" class="form-control-custom" id="nama_pelanggan" name="nama_pelanggan" placeholder="Nama pelanggan atau perusahaan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom" for="no_hp">No. HP / Telepon</label>
                                <input type="text" class="form-control-custom" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom" for="jenis_mesin">Jenis Mesin / Perangkat</label>
                                <input type="text" class="form-control-custom" id="jenis_mesin" name="jenis_mesin" placeholder="Contoh: Mesin CNC, Pompa, Diesel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom" for="jenis_jasa">Jenis Jasa <span style="color: var(--danger);">*</span></label>
                                <select class="form-select-custom" id="jenis_jasa" name="jenis_jasa" required>
                                    <option value="">— Pilih Jenis Jasa —</option>
                                    <option value="Bubut Poros">Bubut Poros</option>
                                    <option value="Bubut Shaft">Bubut Shaft</option>
                                    <option value="Bubut Bushing">Bubut Bushing</option>
                                    <option value="Boring Silinder">Boring Silinder</option>
                                    <option value="Milling Flange">Milling Flange</option>
                                    <option value="Milling Bracket">Milling Bracket</option>
                                    <option value="Pengelasan">Pengelasan</option>
                                    <option value="Threading">Threading</option>
                                    <option value="Balancing">Balancing</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom" for="deskripsi_pekerjaan">Deskripsi Pekerjaan</label>
                        <textarea class="form-control-custom" id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" placeholder="Detail pekerjaan yang harus dikerjakan, ukuran, bahan, dll..." rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom" for="biaya">Biaya (Rp) <span style="color: var(--danger);">*</span></label>
                                <input type="number" class="form-control-custom" id="biaya" name="biaya" placeholder="0" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 12px;">
                        <button type="submit" class="btn-primary-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Simpan Order
                        </button>
                        <a href="jasa-tampil.php" class="btn-secondary-custom">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jasa-tambah.js"></script>

<?php require_once 'includes/footer.php'; ?>
