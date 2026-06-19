<?php
// ================================================
// KONFIGURASI DATABASE - AUTO DETECT LOCALHOST / HOSTING
// ================================================
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0) {
    // Konfigurasi untuk XAMPP Lokal
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');        // Kosongkan untuk XAMPP default
    define('DB_NAME', 'kopi_db');
} else {
    // Konfigurasi untuk Hosting Online (Sesuaikan dengan data dari InfinityFree)
    define('DB_HOST', 'sql304.infinityfree.com'); // MySQL Hostname dari InfinityFree
    define('DB_USER', 'if0_42220566');            // MySQL Username dari InfinityFree
    define('DB_PASS', 'Diqi190703');              // MySQL Password dari InfinityFree
    define('DB_NAME', 'if0_42220566_kopi_db');     // Nama Database dari InfinityFree
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:40px;background:#fff3cd;color:#856404;border:1px solid #ffc107;margin:20px;border-radius:8px;'>
        <strong>⚠ Koneksi Database Gagal</strong><br><br>
        Pesan Error: " . $conn->connect_error . "<br><br>
        <strong>Solusi:</strong><br>
        1. Pastikan XAMPP/MySQL sudah berjalan jika di lokal.<br>
        2. Jika di Hosting, pastikan konfigurasi host, user, password, dan db name di <code>config/db.php</code> sudah benar.
    </div>");
}

$conn->set_charset("utf8mb4");
?>
