<?php require_once 'includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-grain"></div>
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content fade-up">
                <div class="hero-label">Specialty Coffee Indonesia</div>
                <h1 class="hero-title">
                    Kopi terbaik<br>untuk <em>hari mu</em>
                </h1>
                <p class="hero-subtitle">
                    Dari biji pilihan Gayo, Flores, dan Toraja — diseduh dengan ketelitian
                    dan disajikan langsung ke mejamu.
                </p>
                <div class="hero-actions">
                    <a href="menu.php" class="btn btn-gold">
                        Lihat Menu
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-ghost" style="color:rgba(245,230,211,0.7); border-color:rgba(212,165,116,0.25);">
                            Daftar Gratis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-badge">
                <strong>100%</strong>
                <span>Arabika<br>Pilihan</span>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED MENU -->
<section class="page-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Menu Pilihan</span>
            <h2 class="section-title">Yang Paling Digemari</h2>
            <p class="section-desc">Racikan barista terbaik kami, siap menemani hari-harimu.</p>
        </div>

        <div class="menu-grid">
            <?php
            $sql = "SELECT m.*, c.nama as kategori FROM menu_items m
                    JOIN categories c ON m.category_id = c.id
                    WHERE m.tersedia = 1 LIMIT 4";
            $hasil = $conn->query($sql);
            while ($item = $hasil->fetch_assoc()):
            ?>
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
                        <a href="menu.php" class="btn btn-primary btn-sm">Pesan</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div style="text-align:center; margin-top:40px;">
            <a href="menu.php" class="btn btn-ghost">Lihat Semua Menu →</a>
        </div>
    </div>
</section>

<!-- WHY US -->
<section style="background:var(--espresso); padding:70px 0;">
    <div class="container">
        <div class="section-header" style="margin-bottom:40px;">
            <span class="section-label" style="color:var(--gold);">Mengapa Kami</span>
            <h2 class="section-title" style="color:var(--foam);">Lebih dari Sekedar Kopi</h2>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:32px;">
            <?php
            $features = [
                ['☕', 'Biji Pilihan', 'Kami hanya menggunakan single origin arabika grade A dari petani lokal terpercaya.'],
                ['⚡', 'Pesan Cepat', 'Pesan dari meja, siap dalam hitungan menit. Tanpa antri, tanpa repot.'],
                ['🎯', 'Kustomisasi', 'Sesuaikan gula, suhu, dan topping sesuai selera kamu di kolom catatan.'],
            ];
            foreach ($features as $f): ?>
            <div style="text-align:center; padding:24px;">
                <div style="font-size:40px; margin-bottom:16px;"><?= $f[0] ?></div>
                <h3 style="font-family:var(--font-display); font-size:20px; color:var(--gold); margin-bottom:10px;"><?= $f[1] ?></h3>
                <p style="color:rgba(245,230,211,0.55); font-size:14px; line-height:1.7;"><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
