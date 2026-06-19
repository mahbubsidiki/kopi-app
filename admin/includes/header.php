<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /kopi-app/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kopi Nusantara</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/kopi-app/assets/css/admin.css?v=<?= time() ?>">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <span>☕</span> Kopi Nusantara
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    Kategori Menu
                </a>
            </li>
            <li>
                <a href="menu_items.php" class="<?= $current_page == 'menu_items.php' ? 'active' : '' ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Menu Items
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Pesanan
                </a>
            </li>
            <li>
                <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Users
                </a>
            </li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div></div>
            <div class="user-info">
                <span>Halo, <?= htmlspecialchars($_SESSION['user_nama']) ?></span>
                <a href="/kopi-app/logout.php" class="btn btn-sm btn-secondary" style="margin-left:15px;">Logout</a>
            </div>
        </header>

        <div class="admin-content">
