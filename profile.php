<?php 
include 'header.php';

include 'config.php';
include 'functions.php';
require_login();

// Ambil data pengguna saat ini
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Validasi input
    if (empty($full_name) || empty($username) || empty($email)) {
        $error_message = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } else {
        // Cek apakah username atau email sudah digunakan oleh pengguna lain
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $user_id]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_user) {
            $error_message = "Username atau email sudah digunakan oleh pengguna lain.";
        } else {
            // Update data pengguna
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$full_name, $username, $email, $phone, $user_id]);
            
            if ($result) {
                $success_message = "Profil berhasil diperbarui!";
                
                // Update session jika username berubah
                if ($_SESSION['username'] !== $username) {
                    $_SESSION['username'] = $username;
                }
            } else {
                $error_message = "Gagal memperbarui profil.";
            }
        }
    }
}
?>

    <!-- Profile Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Profil Saya</h2>
            </div>
            
            <div class="profile-container">
                <?php if ($success_message): ?>
                    <div class="success-message" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 20px 0; text-align: center;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="payment-form">
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="created_at">Tanggal Registrasi</label>
                        <input type="text" id="created_at" class="form-control" value="<?php echo date('d-m-Y H:i:s', strtotime($user['created_at'])); ?>" readonly>
                    </div>
                    
                    <button type="submit" class="btn btn-block">Perbarui Profil</button>
                </form>
                
                <!-- Change Password Section -->
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3>Ganti Password</h3>
                    
                    <form method="post" action="change_password.php" class="payment-form">
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_new_password">Konfirmasi Password Baru</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-block">Ganti Password</button>
                    </form>
                </div>
                
                <!-- Delete Account Section -->
                <div style="margin-top: 30px; padding: 20px; background: #f8d9de; border-radius: 8px;">
                    <h3>Hapus Akun</h3>
                    <p>Peringatan: Aksi ini tidak dapat dibatalkan. Semua data Anda akan dihapus permanen.</p>
                    
                    <button class="btn" style="background: #dc3545;" onclick="showDeleteConfirmation()">Hapus Akun Saya</button>
                    
                    <!-- Delete Confirmation Modal -->
                    <div id="delete-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                            <h3 style="color: #dc3545; margin-bottom: 15px;">Konfirmasi Penghapusan</h3>
                            <p>Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.</p>
                            
                            <div style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 10px;">
                                <button class="btn" onclick="hideDeleteModal()" style="background: #6c757d;">Batal</button>
                                <a href="delete_account.php" class="btn" style="background: #dc3545;">Ya, Hapus Akun</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function showDeleteConfirmation() {
            document.getElementById('delete-modal').style.display = 'flex';
        }
        
        function hideDeleteModal() {
            document.getElementById('delete-modal').style.display = 'none';
        }
        
        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('delete-modal');
            if (event.target === modal) {
                hideDeleteModal();
            }
        }
    </script>
<?php include 'footer.php'; ?>
