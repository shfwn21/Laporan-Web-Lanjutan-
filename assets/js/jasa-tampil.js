// =====================================================
// JASA-TAMPIL.JS — AJAX Ubah Status Order + Notifikasi
// =====================================================

// AJAX ubah status
function ubahStatus(selectEl) {
    const id = selectEl.dataset.id;
    const statusBaru = selectEl.value;
    const statusLama = selectEl.dataset.original;

    // Konfirmasi jika mengubah ke Selesai
    if (statusBaru === 'Selesai') {
        confirmAction('Ubah status ke "Selesai"? Pemasukan akan otomatis ditambahkan.', function() {
            prosesUbahStatus(selectEl, id, statusBaru, statusLama);
        }, {title: 'Ubah Status', btnText: 'Ya, Ubah', type: 'warning'});
        // Revert select jika modal ditutup tanpa konfirmasi
        const modal = document.getElementById('confirmModal');
        modal.addEventListener('hidden.bs.modal', function handler() {
            if (selectEl.value === 'Selesai' && selectEl.dataset.original === statusLama) {
                selectEl.value = statusLama;
            }
            modal.removeEventListener('hidden.bs.modal', handler);
        });
        return;
    }

    prosesUbahStatus(selectEl, id, statusBaru, statusLama);
}

function prosesUbahStatus(selectEl, id, statusBaru, statusLama) {

    const formData = new FormData();
    formData.append('aksi', 'ubah_status');
    formData.append('id', id);
    formData.append('status', statusBaru);

    fetch('actions/jasa-actions.php?aksi=ubah_status', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update visual
            selectEl.dataset.original = statusBaru;
            selectEl.className = 'status-select';

            if (statusBaru === 'Antrian') selectEl.classList.add('status-antrian');
            else if (statusBaru === 'Sedang Dikerjakan') selectEl.classList.add('status-dikerjakan');
            else if (statusBaru === 'Selesai') {
                selectEl.classList.add('status-selesai');
                selectEl.disabled = true;
            }

            // Show success notification
            showNotification(data.message + (data.incomeAdded ? ' — Pemasukan otomatis ditambahkan!' : ''), 'success');
        } else {
            selectEl.value = statusLama;
            showNotification(data.message || 'Gagal mengubah status', 'error');
        }
    })
    .catch(err => {
        selectEl.value = statusLama;
        showNotification('Terjadi kesalahan jaringan', 'error');
        console.error(err);
    });
}

function showNotification(message, type) {
    const existing = document.querySelectorAll('.alert-custom');
    existing.forEach(el => el.remove());

    const iconSuccess = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
    const iconError = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-custom alert-${type === 'success' ? 'success' : 'error'}`;
    alertDiv.innerHTML = (type === 'success' ? iconSuccess : iconError) + ' ' + message;

    const pageContent = document.querySelector('.page-content');
    pageContent.insertBefore(alertDiv, pageContent.firstChild);

    setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => alertDiv.remove(), 500);
    }, 4000);
}
