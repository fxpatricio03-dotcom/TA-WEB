<?php 
include 'header.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    // Autentikasi user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        
        header('Location: index.php');
        exit();
    } else {
        $error_message = "Username atau password salah.";
    }
}
?>

    <!-- Login Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Masuk ke Akun</h2>
            </div>
            
            <div class="login-container">
                <?php if ($error_message): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="payment-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-block">Masuk</button>
                </form>
                
                <p style="text-align: center; margin-top: 1.5rem;">
                    Belum punya akun? <a href="register.php" style="color: var(--primary);">Daftar di sini</a>
                </p>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>