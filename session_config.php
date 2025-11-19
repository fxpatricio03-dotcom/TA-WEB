<?php
// Set konfigurasi sesi sebelum memulai sesi
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 0); // Cookie akan hilang saat browser ditutup
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', false); // Gunakan true jika menggunakan HTTPS
ini_set('session.cookie_samesite', 'Lax');

// Mulai sesi
session_start();

// Update waktu aktivitas
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}
?>