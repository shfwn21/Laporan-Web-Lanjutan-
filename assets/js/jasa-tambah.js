// =====================================================
// JASA-TAMBAH.JS — Form Validation untuk Tambah Order
// =====================================================

// Client-side Validation
document.getElementById('formTambahOrder').addEventListener('submit', function(e) {
    const nama = document.getElementById('nama_pelanggan');
    const jasa = document.getElementById('jenis_jasa');
    const biaya = document.getElementById('biaya');
    let valid = true;

    [nama, jasa, biaya].forEach(el => el.style.borderColor = '');

    if (nama.value.trim() === '') {
        nama.style.borderColor = 'var(--danger)';
        valid = false;
    }
    if (jasa.value === '') {
        jasa.style.borderColor = 'var(--danger)';
        valid = false;
    }
    if (!biaya.value || parseFloat(biaya.value) <= 0) {
        biaya.style.borderColor = 'var(--danger)';
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        alert('Harap isi semua field yang wajib (*)!');
    }
});

// Format biaya display
document.getElementById('biaya').addEventListener('blur', function() {
    if (this.value && parseFloat(this.value) > 0) {
        this.title = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.value);
    }
});
