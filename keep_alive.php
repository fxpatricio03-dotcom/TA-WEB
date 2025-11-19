<?php
// Hanya mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Update waktu aktivitas terakhir
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// Kembalikan response JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'active',
    'timestamp' => time(),
    'last_activity' => $_SESSION['last_activity'] ?? null
]);
?>
