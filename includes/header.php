<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$jumlah_keranjang = 0;
if (isset($_SESSION['keranjang'])) {
    $jumlah_keranjang = array_sum(array_column($_SESSION['keranjang'], 'jumlah'));
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi Nusantara Pasuruan — Specialty Coffee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/kopi-app/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-inner">
        <a href="/kopi-app/" class="logo">
            <span class="logo-icon">☕</span>
            <span class="logo-text">Kopi <em>Nusantara Pasuruan</em></span>
        </a>
        <ul class="nav-links">
            <li><a href="/kopi-app/" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="/kopi-app/menu.php" class="<?= $current_page === 'menu.php' ? 'active' : '' ?>">Menu</a></li>
            <li>
                <a href="/kopi-app/keranjang.php" class="nav-cart <?= $current_page === 'keranjang.php' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Keranjang
                    <?php if ($jumlah_keranjang > 0): ?>
                        <span class="badge"><?= $jumlah_keranjang ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="/kopi-app/status-pesanan.php" class="<?= $current_page === 'status-pesanan.php' ? 'active' : '' ?>">Pesanan Saya</a></li>
                <li>
                    <a href="/kopi-app/logout.php" class="btn-logout">
                        Logout
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </li>
            <?php else: ?>
                <li><a href="/kopi-app/login.php" class="btn-nav-login">Masuk</a></li>
            <?php endif; ?>
        </ul>
        <button class="hamburger" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<div class="main-content">
