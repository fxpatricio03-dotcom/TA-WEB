<?php
// Hanya mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya logout jika action adalah tab_closed
if (isset($_POST['action']) && $_POST['action'] === 'tab_closed') {
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
    
    // Kirim response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'logged_out']);
    exit();
}

// Jika bukan action yang benar
header('HTTP/1.1 400 Bad Request');
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid action']);
?>
