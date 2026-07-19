// =====================================================
// LOGIN.JS — Client-side Form Validation
// =====================================================

// Client-side Validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');

    // Reset
    usernameError.style.display = 'none';
    passwordError.style.display = 'none';
    username.style.borderColor = '';
    password.style.borderColor = '';

    if (username.value.trim().length < 3) {
        usernameError.textContent = 'Username minimal 3 karakter!';
        usernameError.style.display = 'block';
        username.style.borderColor = 'var(--danger)';
        valid = false;
    }

    if (password.value.length < 3) {
        passwordError.textContent = 'Password minimal 3 karakter!';
        passwordError.style.display = 'block';
        password.style.borderColor = 'var(--danger)';
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }
});

// Live validation reset on input
document.querySelectorAll('.form-control-custom').forEach(function(input) {
    input.addEventListener('input', function() {
        this.style.borderColor = '';
        const errorEl = this.parentElement.querySelector('.invalid-feedback-custom');
        if (errorEl) errorEl.style.display = 'none';
    });
});
