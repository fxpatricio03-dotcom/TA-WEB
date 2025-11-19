<?php include 'header.php';
include 'config.php';
include 'functions.php'; ?>
    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-content">
            <h2>Cita Rasa Jadul, Pengalaman Modern</h2>
            <p>Temukan perpaduan sempurna antara resep tradisional dan seni kuliner kontemporer di Kios Kane.</p>
            <a href="menu.php" class="btn">Jelajahi Menu Kami</a>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="section" id="menu">
        <div class="container">
            <div class="old-school-title">Menu Lezat Kami</div>
            <div class="menu-container">
                <?php
                $menu_items = get_menu_items();
                $menu_by_category = [];
                
                foreach ($menu_items as $item) {
                    $menu_by_category[$item['category']][] = $item;
                }
                
                foreach ($menu_by_category as $category => $items):
                ?>
                <div class="menu-category">
                    <div class="category-header">
                        <h3><i class="fas fa-<?php echo $category === 'Pizza' ? 'pizza-slice' : ($category === 'Ramen' ? 'ramen-noodles' : 'ice-cream'); ?>"></i> <?php echo $category; ?></h3>
                    </div>
                    <div class="category-items">
                        <?php foreach ($items as $item): ?>
                        <div class="menu-item">
                            <img src="https://images.unsplash.com/photo-<?php echo $category === 'Pizza' ? '1513104890138-7c749659a591' : ($category === 'Ramen' ? '1585036156171-384164a0ae38' : '1563729784474-d77dbb933a9e'); ?>?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-price">Rp <?php echo number_format($item['price'] * 15000, 0, ',', '.'); ?></div>
                                <div class="item-desc"><?php echo htmlspecialchars($item['description']); ?></div>
                                <button class="add-to-cart" onclick="addToCart(<?php echo $item['id']; ?>)">Tambah ke Keranjang</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script>
        function addToCart(itemId) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'item_id=' + itemId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = parseInt(cartCount.textContent) + 1;
                    } else {
                        const cartIcon = document.querySelector('.cart-icon');
                        cartIcon.innerHTML += '<span class="cart-count">1</span>';
                    }
                    alert('Item ditambahkan ke keranjang!');
                } else {
                    alert('Error menambahkan item ke keranjang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error menambahkan item ke keranjang');
            });
        }
    </script>
<?php include 'footer.php'; ?>