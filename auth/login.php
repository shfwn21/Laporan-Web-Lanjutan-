<?php
// =====================================================
// LOGIN.PHP — Halaman Autentikasi
// Bengkel Jasa Bubut & Mesin
// =====================================================

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ../dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/koneksi.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi server-side
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        // Cek di database
        $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Login berhasil
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                header('Location: ../dashboard.php');
                exit;
            } else {
                $error = 'Password salah! Silakan coba lagi.';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }

        mysqli_stmt_close($stmt);
        mysqli_close($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login — Sistem Manajemen Bengkel Jasa Bubut & Mesin">
    <title>Login — Bengkel Jasa Bubut</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Logo -->
            <div class="login-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>

            <h2>Bengkel Bubut</h2>
            <p class="subtitle">Masuk ke Sistem Manajemen</p>

            <!-- Alert Error -->
            <?php if (!empty($error)): ?>
                <div class="alert-custom alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm" novalidate>
                <div class="form-group" style="position: relative;">
                    <label class="form-label-custom" for="username">Username</label>
                    <input
                        type="text"
                        class="form-control-custom with-icon"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        required
                        minlength="3"
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    >
                    <span class="input-icon" style="top: 68%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <div class="invalid-feedback-custom" id="usernameError" style="color: var(--danger); font-size: 0.75rem; margin-top: 4px; display: none;"></div>
                </div>

                <div class="form-group" style="position: relative;">
                    <label class="form-label-custom" for="password">Password</label>
                    <input
                        type="password"
                        class="form-control-custom with-icon"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        minlength="3"
                        autocomplete="current-password"
                    >
                    <span class="input-icon" style="top: 68%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <div class="invalid-feedback-custom" id="passwordError" style="color: var(--danger); font-size: 0.75rem; margin-top: 4px; display: none;"></div>
                </div>

                <button type="submit" class="btn-login" style="margin-top: 8px;">
                    Masuk ke Dashboard
                </button>
            </form>

            <p style="text-align: center; margin-top: 24px; font-size: 0.75rem; color: var(--text-muted);">
                &copy; <?= date('Y') ?> Bengkel Jasa Bubut & Mesin
            </p>
        </div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>
