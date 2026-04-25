<?php
// ================================================
// KONFIGURASI DATABASE - SESUAIKAN JIKA PERLU
// ================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Kosongkan untuk XAMPP default
define('DB_NAME', 'kopi_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:40px;background:#fff3cd;color:#856404;border:1px solid #ffc107;margin:20px;border-radius:8px;'>
        <strong>⚠ Koneksi Database Gagal</strong><br><br>
        Pesan Error: " . $conn->connect_error . "<br><br>
        <strong>Solusi:</strong><br>
        1. Pastikan XAMPP sudah berjalan (Apache & MySQL hijau)<br>
        2. Pastikan database <code>kopi_db</code> sudah dibuat di phpMyAdmin<br>
        3. Import file <code>database.sql</code> ke phpMyAdmin
    </div>");
}

$conn->set_charset("utf8mb4");
?>
