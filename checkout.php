<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$keranjang = $_SESSION['keranjang'] ?? [];
if (empty($keranjang)) {
    header("Location: keranjang.php"); exit;
}

$total = 0;
foreach ($keranjang as $item) $total += $item['harga'] * $item['jumlah'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catatan = trim($_POST['catatan']);
    $metode  = $_POST['metode'];
    $kode    = 'KP-' . date('Ymd') . '-' . rand(100, 999);

    $stmt = $conn->prepare("INSERT INTO orders (user_id, kode_pesanan, total, catatan) VALUES (?,?,?,?)");
    $stmt->bind_param("isds", $_SESSION['user_id'], $kode, $total, $catatan);
    $stmt->execute();
    $order_id = $conn->insert_id;

    foreach ($keranjang as $menu_id => $item) {
        $subtotal = $item['harga'] * $item['jumlah'];
        $st2 = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, jumlah, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
        $st2->bind_param("iiidd", $order_id, $menu_id, $item['jumlah'], $item['harga'], $subtotal);
        $st2->execute();
    }

    $stp = $conn->prepare("INSERT INTO payments (order_id, metode, jumlah_bayar) VALUES (?,?,?)");
    $stp->bind_param("isd", $order_id, $metode, $total);
    $stp->execute();

    unset($_SESSION['keranjang']);
    header("Location: status-pesanan.php?kode=" . $kode); exit;
}
?>

<div class="container" style="padding-top:40px; padding-bottom:60px;">
    <div class="page-header">
        <h1 class="page-title">Checkout</h1>
        <p class="page-subtitle">Satu langkah lagi menuju kopi favoritmu ☕</p>
    </div>

    <div class="checkout-grid">
        <!-- Form -->
        <div>
            <div class="card">
                <h3 style="font-family:var(--font-display); font-size:18px; color:var(--espresso); margin-bottom:24px;">
                    Detail Pembayaran
                </h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode">
                            <option value="tunai">💵 Tunai / Cash</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                            <option value="qris">📱 QRIS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan Pesanan <span style="font-weight:300;text-transform:none;">(opsional)</span></label>
                        <textarea name="catatan" rows="3"
                            placeholder="Misal: less sugar, extra ice, tanpa topping..."></textarea>
                    </div>
                    <div style="background:var(--foam); border-radius:var(--radius-sm); padding:16px; margin-bottom:24px; font-size:14px; color:var(--text-muted);">
                        👤 Memesan sebagai: <strong style="color:var(--espresso);"><?= htmlspecialchars($_SESSION['user_nama']) ?></strong>
                    </div>
                    <button type="submit" class="btn btn-gold" style="width:100%; padding:16px; font-size:16px;">
                        Konfirmasi Pesanan ✓
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="checkout-summary">
            <h3>Pesanan Kamu</h3>
            <?php foreach ($keranjang as $item): ?>
            <div class="summary-item">
                <span><?= htmlspecialchars($item['nama']) ?> ×<?= $item['jumlah'] ?></span>
                <span>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
            <div class="summary-total">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <a href="keranjang.php" style="display:block; text-align:center; color:rgba(245,230,211,0.4); font-size:13px; margin-top:20px; text-decoration:none;">
                ← Kembali ke Keranjang
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
