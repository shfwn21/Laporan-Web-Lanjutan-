    </div><!-- /.page-content -->

    <!-- Admin Footer -->
    <footer class="admin-footer">
        &copy; <?= date('Y') ?> <span>Bengkel Jasa Bubut & Mesin</span> — Sistem Manajemen Internal
    </footer>

</div><!-- /.main-content -->

<!-- Confirm Modal (Reusable) -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-body text-center" style="padding: 32px 24px;">
                <!-- Icon -->
                <div id="confirmModalIcon" style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <!-- Message -->
                <h6 id="confirmModalTitle" style="color: #f1f5f9; font-weight: 700; margin-bottom: 8px; font-size: 1rem;">Konfirmasi</h6>
                <p id="confirmModalMessage" style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 24px; line-height: 1.5;"></p>
                <!-- Buttons -->
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 8px; font-size: 0.85rem;">
                        Batal
                    </button>
                    <button type="button" id="confirmModalBtn" class="btn-danger-custom" style="padding: 10px 24px; border-radius: 8px; background: var(--danger); color: #fff; border: none; font-size: 0.85rem; font-weight: 600;">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Confirm Modal Script -->
<script>
/**
 * Custom confirm dialog menggunakan Bootstrap Modal
 * Menggantikan confirm() bawaan browser agar tidak muncul "localhost says"
 *
 * @param {string} message - Pesan konfirmasi
 * @param {string} onConfirmUrl - URL redirect jika dikonfirmasi (untuk link/anchor)
 * @param {object} options - Opsi tambahan { title, btnText, btnClass, type }
 */
function confirmAction(message, onConfirmUrl, options = {}) {
    const modal = document.getElementById('confirmModal');
    const modalInstance = new bootstrap.Modal(modal);
    const modalMessage = document.getElementById('confirmModalMessage');
    const modalTitle = document.getElementById('confirmModalTitle');
    const modalBtn = document.getElementById('confirmModalBtn');
    const modalIcon = document.getElementById('confirmModalIcon');

    // Set message
    modalMessage.textContent = message;
    modalTitle.textContent = options.title || 'Konfirmasi';

    // Set button text
    modalBtn.textContent = options.btnText || 'Ya, Lanjutkan';

    // Set type styling (danger / warning)
    const type = options.type || 'danger';
    if (type === 'warning') {
        modalIcon.style.background = 'rgba(245, 158, 11, 0.15)';
        modalIcon.querySelector('svg').style.color = '#f59e0b';
        modalBtn.style.background = '#f59e0b';
    } else {
        modalIcon.style.background = 'rgba(239, 68, 68, 0.15)';
        modalIcon.querySelector('svg').style.color = '#ef4444';
        modalBtn.style.background = '#ef4444';
    }

    // Remove old event listeners by cloning the button
    const newBtn = modalBtn.cloneNode(true);
    modalBtn.parentNode.replaceChild(newBtn, modalBtn);
    newBtn.id = 'confirmModalBtn';

    // Set confirm action
    newBtn.addEventListener('click', function () {
        modalInstance.hide();
        if (typeof onConfirmUrl === 'function') {
            onConfirmUrl();
        } else if (onConfirmUrl) {
            window.location.href = onConfirmUrl;
        }
    });

    modalInstance.show();
}
</script>

<!-- Footer Script -->
<script src="assets/js/footer.js"></script>

</body>
</html>
