<?php
require_once 'config/db.php';

// Script untuk mengubah password default admin@kopi.com menjadi aman

$email = 'admin@kopi.com';
$password_baru = 'admin123'; // Ganti jika ingin password lain
$hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'admin'");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "<h2>Sukses!</h2>";
    echo "<p>Password untuk akun <strong>admin@kopi.com</strong> berhasil diupdate menjadi: <strong>$password_baru</strong></p>";
    echo "<p><a href='login.php'>Klik di sini untuk login</a></p>";
} else {
    echo "<h2>Gagal!</h2>";
    echo "Error: " . $conn->error;
}
?>
