<?php
require_once 'includes/header.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, nama, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php'));
        exit;
    } else {
        $error = "Email atau password salah. Coba lagi.";
    }
}
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div style="text-align:center; margin-bottom:28px;">
            <div style="font-size:36px; margin-bottom:8px;">☕</div>
            <h2 class="auth-title">Selamat Datang</h2>
            <p class="auth-subtitle">Masuk untuk melanjutkan pesanan kamu</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" required placeholder="kamu@email.com" autocomplete="email">
            </div>
            <div class="form-group" style="position: relative;">
                <label>Password</label>
                <input type="password" id="loginPassword" name="password" required placeholder="••••••••" autocomplete="current-password" style="padding-right: 40px;">
                <span onclick="togglePassword('loginPassword')" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: var(--text-muted, #8B7355);">
                    <svg id="eyeIcon_loginPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15px;">
                Masuk ke Akun
            </button>
        </form>

        <div class="auth-link-row">
            Belum punya akun? <a href="register.php">Daftar gratis →</a>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById('eyeIcon_' + fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        field.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
