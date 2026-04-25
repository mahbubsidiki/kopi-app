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
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
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

<?php require_once 'includes/footer.php'; ?>
