<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Hapus semua data terkait pengguna
try {
    $pdo->beginTransaction();
    
    // Hapus dari tabel transactions
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE customer_name = (SELECT username FROM users WHERE id = ?)");
    $stmt->execute([$user_id]);
    
    // Hapus dari tabel users
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $result = $stmt->execute([$user_id]);
    
    if ($result) {
        $pdo->commit();
        session_destroy();
        header('Location: index.php?message=Akun%20Anda%20berhasil%20dihapus.&type=success');
        exit();
    } else {
        $pdo->rollback();
        header('Location: profile.php?message=Gagal%20menghapus%20akun.&type=error');
        exit();
    }
} catch (Exception $e) {
    $pdo->rollback();
    header('Location: profile.php?message=Terjadi%20kesalahan%20saat%20menghapus%20akun.&type=error');
    exit();
}
?>
