<?php require_once 'includes/header.php'; ?>

<div class="container" style="padding-top:40px;">
    <div class="page-header">
        <h1 class="page-title">Menu Kopi Kami</h1>
        <p class="page-subtitle">Pilih favoritmu, kami siapkan dengan penuh cinta.</p>
    </div>

    <!-- Filter Kategori -->
    <?php $cats = $conn->query("SELECT * FROM categories"); ?>
    <div class="filter-tabs">
        <a href="menu.php" class="filter-tab <?= !isset($_GET['kategori']) ? 'active' : '' ?>">Semua</a>
        <?php
        $cats_arr = [];
        while ($c = $cats->fetch_assoc()) $cats_arr[] = $c;
        foreach ($cats_arr as $c): ?>
        <a href="menu.php?kategori=<?= $c['id'] ?>"
           class="filter-tab <?= (isset($_GET['kategori']) && $_GET['kategori'] == $c['id']) ? 'active' : '' ?>">
            <?= htmlspecialchars($c['nama']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Daftar Menu -->
    <?php
    $where = "WHERE m.tersedia = 1";
    if (isset($_GET['kategori']) && is_numeric($_GET['kategori'])) {
        $cat_id = (int)$_GET['kategori'];
        $where .= " AND m.category_id = $cat_id";
    }
    $sql = "SELECT m.*, c.nama as kategori FROM menu_items m
            JOIN categories c ON m.category_id = c.id
            $where ORDER BY c.id, m.nama";
    $menus = $conn->query($sql);
    $count = $menus->num_rows;
    ?>

    <?php if ($count === 0): ?>
    <div class="empty-state">
        <div class="empty-state-icon">☕</div>
        <h3>Menu tidak tersedia</h3>
        <p>Kategori ini sedang kosong. Coba kategori lain.</p>
        <a href="menu.php" class="btn btn-primary">Lihat Semua</a>
    </div>
    <?php else: ?>
    <div class="menu-grid" style="padding-bottom:60px;">
        <?php while ($item = $menus->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-card-img-wrap">
                <img
                    src="assets/img/<?= htmlspecialchars($item['gambar']) ?>"
                    alt="<?= htmlspecialchars($item['nama']) ?>"
                    class="product-card-img"
                    onerror="this.src='assets/img/default.jpg'">
                <span class="product-badge"><?= htmlspecialchars($item['kategori']) ?></span>
            </div>
            <div class="product-card-body">
                <h3 class="product-name"><?= htmlspecialchars($item['nama']) ?></h3>
                <p class="product-desc"><?= htmlspecialchars($item['deskripsi']) ?></p>
                <div class="product-footer">
                    <span class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></span>
                    <form method="POST" action="keranjang.php" style="margin:0;">
                        <input type="hidden" name="action" value="tambah">
                        <input type="hidden" name="menu_id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="nama" value="<?= htmlspecialchars($item['nama']) ?>">
                        <input type="hidden" name="harga" value="<?= $item['harga'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">+ Keranjang</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
