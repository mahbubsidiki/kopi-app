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
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Min. 6 karakter" minlength="6">
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

<?php require_once 'includes/footer.php'; ?>
