<?php
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        $success = "Status pesanan berhasil diupdate.";
    } else {
        $error = "Gagal mengupdate status pesanan.";
    }
}

$detail_id = isset($_GET['detail']) ? (int)$_GET['detail'] : 0;

?>

<h1 class="page-title">Manajemen Pesanan</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($detail_id > 0): ?>
    <?php
    $stmt = $conn->prepare("
        SELECT o.*, u.nama, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->bind_param("i", $detail_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        die("Pesanan tidak ditemukan.");
    }
    
    $items = $conn->query("
        SELECT oi.*, m.nama as menu_nama 
        FROM order_items oi 
        JOIN menu_items m ON oi.menu_item_id = m.id 
        WHERE oi.order_id = $detail_id
    ");
    
    $payment = $conn->query("SELECT * FROM payments WHERE order_id = $detail_id")->fetch_assoc();
    ?>
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="font-family:'Playfair Display',serif; color:var(--secondary);">
            Detail Pesanan: <?= htmlspecialchars($order['kode_pesanan']) ?>
        </h2>
        <a href="orders.php" class="btn btn-secondary">Kembali</a>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div class="card" style="margin-bottom:0;">
            <h3 style="margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:10px;">Informasi Pelanggan</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($order['nama']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong>Waktu Pesan:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
            <p><strong>Catatan:</strong> <?= nl2br(htmlspecialchars($order['catatan'] ?: '-')) ?></p>
        </div>
        <div class="card" style="margin-bottom:0;">
            <h3 style="margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:10px;">Informasi Pembayaran</h3>
            <p><strong>Metode:</strong> <?= ucfirst($payment['metode'] ?? 'N/A') ?></p>
            <p>
                <strong>Status Pembayaran:</strong> 
                <?php if (($payment['status'] ?? '') == 'lunas'): ?>
                    <span style="color:var(--success); font-weight:bold;">Lunas</span>
                <?php else: ?>
                    <span style="color:var(--danger); font-weight:bold;">Pending</span>
                <?php endif; ?>
            </p>
            <p><strong>Jumlah Bayar:</strong> Rp <?= number_format($payment['jumlah_bayar'] ?? $order['total'], 0, ',', '.') ?></p>
            
            <form method="POST" style="margin-top:20px; padding-top:20px; border-top:1px solid var(--border);">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Update Status Pesanan</label>
                    <select name="status" class="form-control" style="width:auto; display:inline-block; margin-right:10px;">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="proses" <?= $order['status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                        <option value="selesai" <?= $order['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="batal" <?= $order['status'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom:15px;">Item Pesanan</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['menu_nama']) ?></td>
                        <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td><strong>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; font-size:18px; font-weight:bold;">TOTAL</td>
                    <td style="font-size:18px; font-weight:bold; color:var(--primary);">
                        Rp <?= number_format($order['total'], 0, ',', '.') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

<?php else: ?>
    <?php
    $orders = $conn->query("
        SELECT o.*, u.nama 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
    ?>
    <div class="card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['kode_pesanan']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-<?= $row['status'] ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="orders.php?detail=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Lihat</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Belum ada pesanan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
