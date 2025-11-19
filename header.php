<?php
ob_start(); // Start output buffering immediately
include 'config.php';
include 'functions.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Kane - Restoran Indonesia Jadul</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #d4af37;
            --secondary: #8B4513;
            --dark: #2c3e50;
            --light: #f8f9fa;
            --accent: #e74c3c;
            --old-school-bg: #f0e6d2;
            --old-school-text: #5c4033;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://www.transparenttextures.com/patterns/paper.png'), linear-gradient(135deg, #f0e6d2 0%, #e6d9c3 100%);
            color: var(--old-school-text);
            line-height: 1.6;
            background-attachment: fixed;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--secondary), #5d2906);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid var(--primary);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .profile-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 2px solid var(--primary);
        }

        .profile-container h3 {
            color: var(--secondary);
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-left: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .logo h1 span {
            color: var(--primary);
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 2rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
            padding: 5px 10px;
            border-radius: 5px;
        }

        nav ul li a:hover {
            color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
        }

        nav ul li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        nav ul li a:hover::after {
            width: 100%;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .cart-icon {
            position: relative;
            margin-left: 20px;
            font-size: 1.5rem;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/old-paper.png');
            opacity: 0.1;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            border: 2px solid #b8942e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .btn:hover {
            background: #b8942e;
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }

        .btn-secondary {
            background: var(--secondary);
            border-color: #6b360d;
        }

        .btn-secondary:hover {
            background: #6b360d;
        }

        /* Menu Section */
        .section {
            padding: 5rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--old-school-text);
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .menu-category {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
            border: 2px solid #d4af37;
            position: relative;
        }

        .menu-category::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d4af37, #8B4513, #d4af37);
        }

        .menu-category:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .category-header {
            background: var(--secondary);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .category-header h3 {
            font-size: 1.8rem;
            font-family: 'Playfair Display', serif;
        }

        .category-items {
            padding: 1.5rem;
        }

        .menu-item {
            display: flex;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed #ccc;
        }

        .menu-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            flex-shrink: 0;
            border: 2px solid var(--primary);
        }

        .item-details {
            flex-grow: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .item-price {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-desc {
            font-size: 0.9rem;
            color: #777;
        }

        .add-to-cart {
            background: var(--primary);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s;
            border: 1px solid #b8942e;
        }

        .add-to-cart:hover {
            background: #b8942e;
        }

        /* Order Section */
        .order-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 2px solid #d4af37;
        }

        .order-summary {
            background: rgba(248, 249, 250, 0.8);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px dashed #ccc;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 1.2rem;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity-btn {
            background: var(--light);
            border: 1px solid #ddd;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            margin: 0 5px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
        }

        /* Payment Section */
        .payment-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 2px solid #d4af37;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .payment-method {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .payment-method:hover,
        .payment-method.selected {
            border-color: var(--primary);
            background: rgba(212, 175, 55, 0.1);
            transform: translateY(-3px);
        }

        .payment-form {
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Login Section */
        .login-container {
            max-width: 500px;
            margin: 5rem auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 2px solid #d4af37;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-family: 'Playfair Display', serif;
            color: var(--old-school-text);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--secondary), #5d2906);
            color: white;
            padding: 3rem 0 1.5rem;
            border-top: 3px solid var(--primary);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
        }

        .footer-column p,
        .footer-column li {
            margin-bottom: 0.8rem;
            opacity: 0.8;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--primary);
        }

        .social-links {
            display: flex;
            margin-top: 1rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
        }

        .copyright {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.7;
        }

        /* Old School Decorative Elements */
        .old-school-border {
            border: 3px double var(--primary);
            padding: 20px;
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
        }

        .old-school-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--secondary);
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .old-school-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--primary);
            margin: 10px auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }

            nav ul li {
                margin: 0 1rem;
            }

            .hero h2 {
                font-size: 2.5rem;
            }

            .menu-item {
                flex-direction: column;
            }

            .item-image {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<?php
// Check session timeout
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        // Session expired after 1 hour
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
}
?>

<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <i class="fas fa-utensils fa-2x"></i>
                <h1>Kios <span>Kane</span></h1>
            </div>
            <nav>
    <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="order.php">Pesan</a></li>
        <?php if (is_logged_in()): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
            <?php endif; ?>
            <li><a href="profile.php">Profil Saya</a></li>
            <li><a href="logout.php">Keluar</a></li>
        <?php else: ?>
            <li><a href="login.php">Masuk</a></li>
            <li><a href="register.php">Daftar</a></li>
        <?php endif; ?>
    </ul>
</nav>
            <div class="user-info">
                <a href="order.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $cart = get_cart_items();
                    $count = array_sum($cart);
                    if ($count > 0): ?>
                        <span class="cart-count"><?php echo $count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

        
<script>
        // Fungsi untuk logout otomatis saat tab ditutup
        window.addEventListener('beforeunload', function(e) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'auto_logout.php', false);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=tab_closed');
        });
    </script>
    
