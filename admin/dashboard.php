<?php
require_once 'includes/header.php';

// Ambil Statistik
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$total_pendapatan = $conn->query("SELECT SUM(total) FROM orders WHERE status = 'selesai'")->fetch_row()[0] ?? 0;
$total_menu = $conn->query("SELECT COUNT(*) FROM menu_items")->fetch_row()[0];
$pesanan_pending = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetch_row()[0];

// Ambil 5 Pesanan Terakhir
$recent_orders = $conn->query("
    SELECT o.id, o.kode_pesanan, o.total, o.status, u.nama 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");
?>

<h1 class="page-title">Dashboard</h1>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </div>
        <div class="stat-info">
            <h3>Total Pesanan</h3>
            <p><?= number_format($total_orders, 0, ',', '.') ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="color:var(--success); background:rgba(40,167,69,0.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-info">
            <h3>Total Pendapatan</h3>
            <p>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="color:var(--warning); background:rgba(255,193,7,0.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <div class="stat-info">
            <h3>Pesanan Pending</h3>
            <p><?= number_format($pesanan_pending, 0, ',', '.') ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color:#17a2b8; background:rgba(23,162,184,0.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </div>
        <div class="stat-info">
            <h3>Total Menu</h3>
            <p><?= number_format($total_menu, 0, ',', '.') ?></p>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 20px; font-family: 'Playfair Display', serif; color: var(--secondary);">Pesanan Terbaru</h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_orders->num_rows > 0): ?>
                    <?php while ($row = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['kode_pesanan']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge badge-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="orders.php?detail=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Belum ada pesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px; text-align: right;">
        <a href="orders.php" class="btn btn-sm btn-secondary">Lihat Semua Pesanan</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
