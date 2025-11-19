<?php 
include 'header.php';

include 'config.php';
include 'functions.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error_message = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak sesuai.";
    } else {
        // Cek apakah username atau email sudah digunakan
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            $error_message = "Username atau email sudah digunakan.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'customer')");
            $result = $stmt->execute([$full_name, $username, $email, $phone, $hashed_password]);

            if ($result) {
                $success_message = "Pendaftaran berhasil! Silakan login dengan akun Anda.";
            } else {
                $error_message = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

    <!-- Registration Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Daftar Akun</h2>
            </div>
            
            <div class="login-container">
                <?php if ($success_message): ?>
                    <div class="success-message" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 1rem;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 1rem;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="payment-form">
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-block">Daftar</button>
                </form>
                
                <p style="text-align: center; margin-top: 1.5rem;">
                    Sudah punya akun? <a href="login.php" style="color: var(--primary);">Masuk di sini</a>
                </p>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>