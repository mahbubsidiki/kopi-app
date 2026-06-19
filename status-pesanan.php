<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$user_id  = $_SESSION['user_id'];
$kode_baru = $_GET['kode'] ?? '';

$stmt = $conn->prepare(
    "SELECT o.*, p.metode, p.status as status_bayar
     FROM orders o
     LEFT JOIN payments p ON o.id = p.order_id
     WHERE o.user_id = ?
     ORDER BY o.created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pesanan_list = $stmt->get_result();

$status_cfg = [
    'pending' => ['label' => 'Menunggu', 'class' => 'status-pending', 'icon' => '⏳'],
    'proses'  => ['label' => 'Diproses', 'class' => 'status-proses',  'icon' => '☕'],
    'selesai' => ['label' => 'Selesai',  'class' => 'status-selesai', 'icon' => '✅'],
    'batal'   => ['label' => 'Dibatal',  'class' => 'status-batal',   'icon' => '❌'],
];
?>

<div class="container" style="padding-top:40px; padding-bottom:60px; max-width:760px;">
    <div class="page-header">
        <h1 class="page-title">Pesanan Saya</h1>
        <p class="page-subtitle">Riwayat dan status pesananmu</p>
    </div>

    <?php if ($kode_baru): ?>
    <div class="alert alert-success">
        🎉 Pesanan <strong><?= htmlspecialchars($kode_baru) ?></strong> berhasil dibuat! Mohon tunggu konfirmasi kami.
    </div>
    <?php endif; ?>

    <?php if ($pesanan_list->num_rows === 0): ?>
    <div class="empty-state">
        <div class="empty-state-icon">📋</div>
        <h3>Belum ada pesanan</h3>
        <p>Yuk pesan kopi pertamamu sekarang!</p>
        <a href="menu.php" class="btn btn-primary">Lihat Menu</a>
    </div>
    <?php else: ?>

    <?php while ($p = $pesanan_list->fetch_assoc()):
        $sc = $status_cfg[$p['status']] ?? $status_cfg['pending'];
    ?>
    <div class="order-card">
        <div class="order-card-header">
            <div>
                <strong style="font-family:var(--font-display); font-size:17px; color:var(--espresso);">
                    <?= htmlspecialchars($p['kode_pesanan']) ?>
                </strong>
                <p style="color:var(--text-muted); font-size:13px; margin-top:4px;">
                    <?= date('d M Y, H:i', strtotime($p['created_at'])) ?>
                    <?php if ($p['metode']): ?>
                        · <?= ucfirst($p['metode']) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div style="text-align:right;">
                <span class="status-pill <?= $sc['class'] ?>"><?= $sc['icon'] ?> <?= $sc['label'] ?></span>
                <p style="font-family:var(--font-display); font-weight:700; color:var(--accent); font-size:18px; margin-top:8px;">
                    Rp <?= number_format($p['total'], 0, ',', '.') ?>
                </p>
            </div>
        </div>
        <?php if ($p['catatan']): ?>
        <div style="margin-top:12px; padding:10px 14px; background:var(--foam); border-radius:8px; font-size:13px; color:var(--text-muted);">
            📝 <?= htmlspecialchars($p['catatan']) ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>

    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
