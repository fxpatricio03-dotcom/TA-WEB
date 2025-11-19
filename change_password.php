<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $user_id = $_SESSION['user_id'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $error_message = "Semua field wajib diisi.";
    } elseif ($new_password !== $confirm_new_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } else {
        // Ambil password saat ini dari database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($current_password, $user['password'])) {
            $error_message = "Password saat ini salah.";
        } else {
            // Hash password baru
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashed_new_password, $user_id]);
            
            if ($result) {
                $success_message = "Password berhasil diubah!";
            } else {
                $error_message = "Gagal mengganti password.";
            }
        }
    }
    
    // Redirect kembali ke halaman profil dengan pesan
    $message = $success_message ? urlencode($success_message) : urlencode($error_message);
    $type = $success_message ? 'success' : 'error';
    header("Location: profile.php?message=$message&type=$type");
    exit();
}
?>
