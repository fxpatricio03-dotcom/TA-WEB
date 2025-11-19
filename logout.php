<?php
// Hanya mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua data sesi
session_destroy();

// Hapus cookie sesi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ke halaman utama
header('Location: index.php');
exit();
?>