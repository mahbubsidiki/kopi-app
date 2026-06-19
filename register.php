<?php
require_once 'includes/header.php';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
        $error = "Email sudah terdaftar. Coba login.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $nama, $email, $hash);
        $stmt->execute();
        $success = "Akun berhasil dibuat! Silakan login.";
    }
}
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div style="text-align:center; margin-bottom:28px;">
            <div style="font-size:36px; margin-bottom:8px;">✨</div>
            <h2 class="auth-title">Buat Akun</h2>
            <p class="auth-subtitle">Bergabung dan nikmati kopi terbaik kami</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
            <a href="login.php" style="margin-left:8px; font-weight:700;">Login →</a>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Nama kamu">
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" required placeholder="kamu@email.com">
            </div>
            <div class="form-group" style="position: relative;">
                <label>Password</label>
                <input type="password" id="regPassword" name="password" required placeholder="Min. 6 karakter" minlength="6" style="padding-right: 40px;">
                <span onclick="togglePassword('regPassword')" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: var(--text-muted, #8B7355);">
                    <svg id="eyeIcon_regPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15px;">
                Buat Akun Sekarang
            </button>
        </form>

        <div class="auth-link-row">
            Sudah punya akun? <a href="login.php">Masuk →</a>
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
