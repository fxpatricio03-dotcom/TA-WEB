<?php 
ob_start(); // Start output buffering immediately
include 'header.php';

include 'config.php';
include 'functions.php';

$cart = get_cart_items();

if (isset($_POST['update_quantity'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $_SESSION['cart'][$item_id] = $quantity;
    } else {
        remove_from_cart($item_id);
    }
    
    ob_end_clean(); // Clean the buffer
    header('Location: order.php');
    exit();
}

if (isset($_POST['remove_item'])) {
    $item_id = (int)$_POST['item_id'];
    remove_from_cart($item_id);
    
    ob_end_clean(); // Clean the buffer
    header('Location: order.php');
    exit();
}

if (isset($_POST['clear_cart'])) {
    clear_cart();
    
    ob_end_clean(); // Clean the buffer
    header('Location: order.php');
    exit();
}

if (isset($_POST['proceed_to_payment'])) {
    ob_end_clean(); // Clean the buffer
    header('Location: payment.php');
    exit();
}

ob_end_flush(); // Send all output
?>

    <!-- Order Section -->
    <section class="section">
        <div class="container">
            <div class="old-school-title">Pesanan Anda</div>
            
            <?php if (empty($cart)): ?>
                <div class="order-container">
                    <h3>Keranjang Anda kosong</h3>
                    <p>Jelajahi <a href="menu.php">menu</a> kami untuk menambahkan item ke keranjang.</p>
                </div>
            <?php else: ?>
                <div class="order-container">
                    <div class="order-summary">
                        <?php 
                        $total = 0;
                        foreach ($cart as $item_id => $quantity):
                            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
                            $stmt->execute([$item_id]);
                            $item = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($item):
                                $item_total = $item['price'] * $quantity;
                                $total += $item_total;
                        ?>
                        <div class="order-item">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                <small>Kategori: <?php echo htmlspecialchars($item['category']); ?></small>
                            </div>
                            <div class="item-controls">
                                <div class="quantity-controls">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                        <button type="submit" name="update_quantity" class="quantity-btn" onclick="this.form.quantity.value=parseInt(this.form.quantity.value)-1 || 1; this.form.submit();">-</button>
                                        <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" class="quantity-input" onchange="this.form.submit();">
                                        <button type="submit" name="update_quantity" class="quantity-btn" onclick="this.form.quantity.value=parseInt(this.form.quantity.value)+1; this.form.submit();">+</button>
                                    </form>
                                </div>
                                <div style="margin-top: 10px;">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                        <button type="submit" name="remove_item" class="btn" style="padding: 5px 10px; font-size: 0.9rem;">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                Rp <?php echo number_format($item_total * 15000, 0, ',', '.'); ?>
                            </div>
                        </div>
                        <?php 
                            endif;
                        endforeach;
                        ?>
                        <div class="order-total">
                            <span>Total:</span>
                            <span>Rp <?php echo number_format($total * 15000, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <form method="post">
                            <button type="submit" name="clear_cart" class="btn" style="background: #95a5a6;">Kosongkan Keranjang</button>
                        </form>
                        <form method="post">
                            <button type="submit" name="proceed_to_payment" class="btn">Lanjut ke Pembayaran</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php include 'footer.php'; ?>