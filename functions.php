<?php
require_once 'config.php';

// Hanya definisikan fungsi jika belum didefinisikan
if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

if (!function_exists('get_menu_items')) {
    function get_menu_items($category = null) {
        global $pdo;
        
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? ORDER BY name");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, name");
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('get_cart_items')) {
    function get_cart_items() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }
}

if (!function_exists('add_to_cart')) {
    function add_to_cart($item_id, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id] += $quantity;
        } else {
            $_SESSION['cart'][$item_id] = $quantity;
        }
    }
}

if (!function_exists('remove_from_cart')) {
    function remove_from_cart($item_id) {
        if (isset($_SESSION['cart'][$item_id])) {
            unset($_SESSION['cart'][$item_id]);
        }
    }
}

if (!function_exists('clear_cart')) {
    function clear_cart() {
        $_SESSION['cart'] = [];
    }
}

if (!function_exists('calculate_total')) {
    function calculate_total() {
        global $pdo;
        $cart = get_cart_items();
        $total = 0;
        
        foreach ($cart as $item_id => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item) {
                $total += $item['price'] * $quantity;
            }
        }
        
        return $total;
    }
}

if (!function_exists('record_transaction')) {
    function record_transaction($order_id, $customer_name, $total_amount, $payment_method, $cart) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Insert transaction record
        $stmt = $pdo->prepare("INSERT INTO transactions (order_id, customer_name, total_amount, payment_method, payment_status) VALUES (?, ?, ?, ?, 'completed')");
        $stmt->execute([$order_id, $customer_name, $total_amount, $payment_method]);
        $transaction_id = $pdo->lastInsertId();
        
        // Insert transaction items
        foreach ($cart as $item_id => $quantity) {
            $stmt = $pdo->prepare("SELECT name, category, price FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item) {
                $item_total = $item['price'] * $quantity;
                $stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, item_id, item_name, item_category, item_price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$transaction_id, $item_id, $item['name'], $item['category'], $item['price'], $quantity, $item_total]);
            }
        }
        
        $pdo->commit();
        return $transaction_id;
    } catch (Exception $e) {
        $pdo->rollback();
        return false;
    }
}
}

if (!function_exists('authenticate_user')) {
    function authenticate_user($username, $password) {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            return true;
        }
        
        return false;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            header('Location: login.php');
            exit();
        }
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}
?>