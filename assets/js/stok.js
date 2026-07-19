// =====================================================
// STOK.JS — Script untuk halaman Stok Barang
// Fitur: Edit Modal, Auto-submit Filter Kategori
// =====================================================

/**
 * Buka modal edit dan isi data barang
 * @param {number} id - ID barang
 * @param {string} nama - Nama barang
 * @param {string} kategori - Kategori barang
 * @param {number} stok - Jumlah stok
 * @param {number} harga - Harga beli
 * @param {string} satuan - Satuan barang
 */
function openEditModal(id, nama, kategori, stok, harga, satuan) {
    document.getElementById('editId').value = id;
    document.getElementById('editNamaBarang').value = nama;
    document.getElementById('editKategori').value = kategori;
    document.getElementById('editJumlahStok').value = stok;
    document.getElementById('editHargaBeli').value = harga;
    document.getElementById('editSatuan').value = satuan;

    const modal = new bootstrap.Modal(document.getElementById('editBarangModal'));
    modal.show();
}

// ── Inisialisasi saat DOM siap ──
document.addEventListener('DOMContentLoaded', function () {

    // Auto-submit filter kategori saat berubah
    const filterKategori = document.getElementById('filterKategoriStok');
    if (filterKategori) {
        filterKategori.addEventListener('change', function () {
            document.getElementById('formSearchStok').submit();
        });
    }

});
