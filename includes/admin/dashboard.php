<?php
require_once 'header-admin.php';
require_once 'check-admin.php';

$total_pesanan = $conn->query("SELECT COUNT(*) as n FROM orders")->fetch_assoc()['n'];
$pesanan_hari  = $conn->query("SELECT COUNT(*) as n FROM orders WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['n'];
$pendapatan    = $conn->query("SELECT SUM(total) as n FROM orders WHERE status='selesai'")->fetch_assoc()['n'] ?? 0;
$pending       = $conn->query("SELECT COUNT(*) as n FROM orders WHERE status='pending'")->fetch_assoc()['n'];
$total_menu    = $conn->query("SELECT COUNT(*) as n FROM menu_items WHERE tersedia=1")->fetch_assoc()['n'];
?>

<h2 style="font-family:var(--font-display); font-size:28px; color:var(--espresso); margin-bottom:8px;">
    Selamat datang, <?= htmlspecialchars($_SESSION['user_nama']) ?> 👋
</h2>
<p style="color:var(--text-muted); margin-bottom:32px;">
    <?= date('l, d F Y') ?>
</p>

<!-- Stat Cards -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:40px;">
    <?php
    $stats = [
        ['Total Pesanan',     $total_pesanan, '#3B1F0E'],
        ['Pesanan Hari Ini',  $pesanan_hari,  '#0d6efd'],
        ['Menunggu Proses',   $pending,       '#C8622A'],
        ['Pendapatan', 'Rp ' . number_format($pendapatan, 0, ',', '.'), '#198754'],
    ];
    foreach ($stats as $s):
    ?>
    <div class="stat-card" style="border-top-color:<?= $s[2] ?>;">
        <div class="stat-label"><?= $s[0] ?></div>
        <div class="stat-value" style="color:<?= $s[2] ?>; font-size:22px;"><?= $s[1] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pesanan Terbaru -->
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="font-family:var(--font-display); font-size:20px;">Pesanan Terbaru</h3>
        <a href="kelola-pesanan.php" class="btn btn-ghost btn-sm">Lihat semua →</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="table-elegant">
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = $conn->query(
                    "SELECT o.*, u.nama FROM orders o
                     JOIN users u ON o.user_id = u.id
                     ORDER BY o.created_at DESC LIMIT 10"
                );
                $sc = [
                    'pending' => 'status-pending',
                    'proses'  => 'status-proses',
                    'selesai' => 'status-selesai',
                    'batal'   => 'status-batal',
                ];
                while ($r = $rows->fetch_assoc()):
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['kode_pesanan']) ?></strong></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
                    <td>
                        <span class="status-pill <?= $sc[$r['status']] ?>">
                            <?= ucfirst($r['status']) ?>
                        </span>
                    </td>
                    <td style="color:var(--text-muted); font-size:13px;">
                        <?= date('d M, H:i', strtotime($r['created_at'])) ?>
                    </td>
                    <td>
                        <a href="kelola-pesanan.php" style="color:var(--accent); font-size:13px; font-weight:600;">Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
