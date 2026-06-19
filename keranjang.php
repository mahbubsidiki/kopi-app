<?php
require_once 'includes/header.php';

$action = $_POST['action'] ?? '';

if ($action === 'tambah') {
    $id    = $_POST['menu_id'];
    $nama  = $_POST['nama'];
    $harga = $_POST['harga'];
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]['jumlah']++;
    } else {
        $_SESSION['keranjang'][$id] = ['nama' => $nama, 'harga' => $harga, 'jumlah' => 1];
    }
    header("Location: keranjang.php"); exit;
}

if ($action === 'hapus') {
    unset($_SESSION['keranjang'][$_POST['menu_id']]);
    header("Location: keranjang.php"); exit;
}

if ($action === 'kurang') {
    $id = $_POST['menu_id'];
    if ($_SESSION['keranjang'][$id]['jumlah'] > 1) {
        $_SESSION['keranjang'][$id]['jumlah']--;
    } else {
        unset($_SESSION['keranjang'][$id]);
    }
    header("Location: keranjang.php"); exit;
}

$keranjang = $_SESSION['keranjang'] ?? [];
$total = 0;
foreach ($keranjang as $item) $total += $item['harga'] * $item['jumlah'];
?>

<div class="container" style="padding-top:40px; padding-bottom:60px;">
    <div class="page-header">
        <h1 class="page-title">Keranjang Belanja</h1>
        <p class="page-subtitle"><?= count($keranjang) ?> item dalam keranjang</p>
    </div>

    <?php if (empty($keranjang)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">🛒</div>
        <h3>Keranjangmu masih kosong</h3>
        <p>Tambahkan kopi favoritmu dari menu.</p>
        <a href="menu.php" class="btn btn-primary">Lihat Menu</a>
    </div>
    <?php else: ?>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:32px; align-items:start;">
        <!-- Items -->
        <div class="card">
            <?php foreach ($keranjang as $id => $item): ?>
            <div class="cart-item">
                <div class="cart-item-info">
                    <h4><?= htmlspecialchars($item['nama']) ?></h4>
                    <p>Rp <?= number_format($item['harga'], 0, ',', '.') ?> / cup</p>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="qty-control">
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="kurang">
                            <input type="hidden" name="menu_id" value="<?= $id ?>">
                            <button type="submit" class="qty-btn">−</button>
                        </form>
                        <span class="qty-num"><?= $item['jumlah'] ?></span>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="tambah">
                            <input type="hidden" name="menu_id" value="<?= $id ?>">
                            <input type="hidden" name="nama" value="<?= htmlspecialchars($item['nama']) ?>">
                            <input type="hidden" name="harga" value="<?= $item['harga'] ?>">
                            <button type="submit" class="qty-btn">+</button>
                        </form>
                    </div>
                    <span class="cart-item-price">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></span>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="action" value="hapus">
                        <input type="hidden" name="menu_id" value="<?= $id ?>">
                        <button type="submit" class="qty-btn" style="color:#dc3545; border-color:#F8D7DA;" title="Hapus">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div style="position:sticky; top:100px;">
            <div class="checkout-summary">
                <h3>Ringkasan</h3>
                <?php foreach ($keranjang as $item): ?>
                <div class="summary-item">
                    <span><?= htmlspecialchars($item['nama']) ?> ×<?= $item['jumlah'] ?></span>
                    <span>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-total">
                    <span>Total</span>
                    <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                </div>
                <a href="checkout.php" class="btn btn-gold" style="width:100%; margin-top:24px; font-size:15px;">
                    Lanjut Checkout →
                </a>
                <a href="menu.php" class="btn btn-ghost" style="width:100%; margin-top:10px; color:rgba(245,230,211,0.6); border-color:rgba(212,165,116,0.2);">
                    Tambah Menu
                </a>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
