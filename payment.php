<?php 
include 'header.php';

include 'config.php';
include 'functions.php';

// Simpan cart sebelum clear
$cart_before_clear = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$customer_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Tamu';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: order.php');
    exit();
}

$total = calculate_total();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $card_number = $payment_method === 'credit_card' ? $_POST['card_number'] : null;
    $exp_date = $payment_method === 'credit_card' ? $_POST['exp_date'] : null;
    $cvv = $payment_method === 'credit_card' ? $_POST['cvv'] : null;

    if ($payment_method === 'cash') {
        $order_id = 'ORDER_' . date('dmY') . '_' . uniqid();
        $customer_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

        $transaction_id = record_transaction($order_id, $customer_name, $total, 'cash', get_cart_items());

        if ($transaction_id) {
            clear_cart();
            $success_message = "Pesanan berhasil! Silakan bayar dengan uang tunai saat pesanan siap.";
            $payment_status = 'cash';
        } else {
            $error_message = "Gagal mencatat transaksi.";
        }

    } elseif ($payment_method === 'credit_card' && $card_number && $exp_date && $cvv) {

        $order_id = 'ORDER_' . date('dmY') . '_' . uniqid();
        $customer_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

        $transaction_id = record_transaction($order_id, $customer_name, $total, 'credit_card', get_cart_items());

        if ($transaction_id) {
            clear_cart();
            $success_message = "Pesanan berhasil! Pembayaran telah diproses.";
            $payment_status = 'credit_card';
        } else {
            $error_message = "Gagal mencatat transaksi.";
        }

    } elseif ($payment_method === 'qris') {

        $order_id = 'ORDER_' . date('dmY') . '_' . uniqid();
        $customer_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

        $transaction_id = record_transaction($order_id, $customer_name, $total, 'qris', get_cart_items());

        if ($transaction_id) {
            clear_cart();
            $success_message = "Pesanan berhasil! Silakan menunggu pesanan siap.";
            $payment_status = 'qris';
        } else {
            $error_message = "Gagal mencatat transaksi.";
        }

    } else {
        $error_message = "Silakan isi semua informasi pembayaran yang diperlukan.";
    }
}
?>

    <!-- Payment Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Pembayaran</h2>
            </div>

            <div class="payment-container">
                <?php if (isset($success_message)): ?>
                    <div class="success-message"
                        style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                        <i class="fas fa-check-circle fa-2x" style="margin-bottom: 10px; display: block; color: #28a745;"></i>
                        <h3>Pembayaran Berhasil!</h3>
                        <p><?php echo $success_message; ?></p>

                        <!-- Print Receipt Button -->
                        <div style="margin-top: 20px;">
                            <button class="btn" onclick="printReceipt()" style="margin-right: 10px;">
                                <i class="fas fa-print"></i> Cetak Struk
                            </button>
                            <a href="menu.php" class="btn">Lanjut Belanja</a>
                        </div>
                    </div>

                    <!-- Hidden Receipt Content for Printing -->
                    <div id="receipt-content" style="display: none;">
                        <div style="max-width: 300px; margin: 0 auto; font-family: monospace; border: 1px solid #ccc; padding: 20px; background: white;">
                            <!-- Header -->
                            <div style="text-align: center; margin-bottom: 15px; border-bottom: 2px solid #8B4513; padding-bottom: 10px;">
                                <h3 style="margin: 5px 0; color: #d4af37; font-family: 'Playfair Display', serif; font-size: 1.6em;">KIOS KANE</h3>
                                <p style="margin: 3px 0; font-size: 0.9em;">Restoran Indonesia Jadul</p>
                                <p style="margin: 3px 0; font-size: 0.9em;">Jl. Rasa Makanan No. 123, Jakarta</p>
                                <p style="margin: 3px 0; font-size: 0.9em;">Telp: (021) 1234-5678</p>
                            </div>

                            <!-- Order Info -->
                            <div style="margin: 15px 0; font-size: 0.9em;">
                                <p style="margin: 5px 0; text-align: center; font-weight: bold;">STRUK PEMESANAN</p>
                                <p style="margin: 5px 0; text-align: center;">Tanggal: <?php echo date('d-m-Y H:i:s'); ?></p>
                                <p style="margin: 5px 0; text-align: center;">ID Pesanan: <?php echo 'ORDER_' . date('dmY') . '_' . uniqid(); ?></p>
                                <?php if (isset($_SESSION['username'])): ?>
                                    <p style="margin: 5px 0; text-align: center;">Pelanggan: <?php echo $_SESSION['username']; ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Items List -->
                            <div style="margin: 15px 0; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; padding: 10px 0;">
                                <?php
                                $cart_to_display = $cart_before_clear;
                                foreach ($cart_to_display as $item_id => $quantity):
                                    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
                                    $stmt->execute([$item_id]);
                                    $item = $stmt->fetch(PDO::FETCH_ASSOC);

                                    if ($item):
                                        ?>
                                        <div style="display: flex; justify-content: space-between; margin: 8px 0; font-size: 0.9em;">
                                            <div style="flex: 2;"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div style="flex: 1; text-align: center;">x<?php echo $quantity; ?></div>
                                            <div style="flex: 1; text-align: right;">Rp<?php echo number_format($item['price'] * $quantity * 15000, 0, ',', '.'); ?></div>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin: 3px 0; color: #777; font-size: 0.8em;">
                                            <div style="flex: 2;">Kategori: <?php echo htmlspecialchars($item['category']); ?></div>
                                            <div style="flex: 1; text-align: center;"></div>
                                            <div style="flex: 1; text-align: right;">Rp<?php echo number_format($item['price'] * 15000, 0, ',', '.'); ?></div>
                                        </div>
                                    <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>

                            <!-- Total -->
                            <div style="margin: 15px 0; font-weight: bold; font-size: 1.1em; display: flex; justify-content: space-between; padding-top: 10px; border-top: 2px solid #eee;">
                                <span>Total Pembayaran:</span>
                                <span>Rp<?php echo number_format($total * 15000, 0, ',', '.'); ?></span>
                            </div>

                            <!-- Payment Method -->
                            <div style="margin: 10px 0; text-align: center; padding: 8px; background: #f8f9fa; border-radius: 5px; font-weight: bold; font-size: 0.9em;">
                                Metode Pembayaran: <?php echo ucfirst($payment_method ?? 'Tunai'); ?>
                            </div>

                            
                            <!-- Thank You Message -->
                            <div style="margin-top: 20px; text-align: center; font-style: italic; font-size: 0.9em;">
                                <p>Terima kasih atas pesanan Anda!</p>
                                <p>Selamat menikmati hidangan kami!</p>
                                <p>Kunjungi kami lagi!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tampilkan daftar pesanan dan QRIS di halaman pembayaran setelah sukses -->
                    <div
                        style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #ddd;">
                        <h4 style="margin-bottom: 10px; color: #2c3e50;">Daftar Pesanan Anda</h4>
                        <?php
                        $cart_to_display = $cart_before_clear;
                        foreach ($cart_to_display as $item_id => $quantity):
                            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
                            $stmt->execute([$item_id]);
                            $item = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($item):
                                ?>
                                <div
                                    style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                                    <div style="flex: 1; margin-right: 10px;">
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                        <small>Kategori: <?php echo htmlspecialchars($item['category']); ?></small>
                                    </div>
                                    <div style="text-align: right;">
                                        <span><?php echo $quantity; ?> x Rp
                                            <?php echo number_format($item['price'] * 15000, 0, ',', '.'); ?></span><br>
                                        <span>Rp <?php echo number_format($item['price'] * $quantity * 15000, 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            <?php
                            endif;
                        endforeach;
                        ?>
                        <div
                            style="display: flex; justify-content: space-between; font-weight: bold; margin-top: 15px; padding-top: 15px; border-top: 2px solid #eee;">
                            <span>Total:</span>
                            <span>Rp <?php echo number_format($total * 15000, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    
                <?php else: ?>
                    <div class="order-summary">
                        <h3>Ringkasan Pesanan</h3>

                        <!-- List of Ordered Items -->
                        <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <?php
                            $cart = get_cart_items();
                            foreach ($cart as $item_id => $quantity):
                                $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
                                $stmt->execute([$item_id]);
                                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($item):
                                    ?>
                                    <div
                                        style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                            <small>Kategori: <?php echo htmlspecialchars($item['category']); ?></small>
                                        </div>
                                        <div style="text-align: right;">
                                            <span><?php echo $quantity; ?> x Rp
                                                <?php echo number_format($item['price'] * 15000, 0, ',', '.'); ?></span><br>
                                            <span>Rp
                                                <?php echo number_format($item['price'] * $quantity * 15000, 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                <?php
                                endif;
                            endforeach;
                            ?>
                        </div>

                        <!-- Total Amount -->
                        <div
                            style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; padding-top: 15px; border-top: 2px solid #eee;">
                            <span>Total:</span>
                            <span>Rp <?php echo number_format($total * 15000, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div class="error-message">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="payment-form">
                        <h3>Metode Pembayaran</h3>
                        <div class="payment-methods">
                            <div class="payment-method" onclick="selectPaymentMethod('cash')">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                                <p>Tunai</p>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod('qris')">
                                <i class="fas fa-qrcode fa-2x"></i>
                                <p>QRIS</p>
                            </div>
                        </div>

                        <div id="qris-details" style="display: none; text-align: center; padding: 20px;">
                            <h3>Pembayaran QRIS</h3>
                            <p>Pindai kode QR ini dengan aplikasi pembayaran di ponsel Anda untuk menyelesaikan pembayaran</p>
                            <div id="qris-qr-container"
                                style="background: white; padding: 15px; display: inline-block; border-radius: 10px; margin: 15px 0;">
                                <div id="qris-qr-code"
                                    style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc;">
                                    <span style="color: #777;">Menghasilkan Kode QR...</span>
                                </div>
                            </div>
                            <p>Jumlah: <strong>Rp <?php echo number_format($total * 15000, 0, ',', '.'); ?></strong></p>
                            <p style="font-size: 0.9rem; color: #666;">QRIS diterima oleh semua aplikasi pembayaran utama
                                Indonesia</p>
                        </div>

                        <input type="hidden" name="payment_method" id="payment_method" value="">
                        <button type="submit" class="btn btn-block" id="pay-btn" disabled>Lanjutkan Pembayaran</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- QR Code Library -->
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

    <script>
        function selectPaymentMethod(method) {
            // Remove selected class from all methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });

            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');

            // Show/hide payment details
            document.getElementById('qris-details').style.display = 'none';

            if (method === 'qris') {
                document.getElementById('qris-details').style.display = 'block';
                document.getElementById('pay-btn').disabled = false;

                // Generate QR code dynamically
                generateQRCode();
            } else {
                document.getElementById('pay-btn').disabled = false;
            }

            // Set hidden input value
            document.getElementById('payment_method').value = method;
        }

        function generateQRCode() {
            const qrContainer = document.getElementById('qris-qr-code');
            qrContainer.innerHTML = '';

            // Ambil nominal dari PHP
            const total = "<?php echo $total * 15000; ?>";

            // MID QRIS (Ganti jika sudah punya MID asli)
            const mid = "000000000000001";

            // Order ID unik
            const orderId = "ORD" + Date.now();

            // Format EMV Value
            function emv(id, value) {
                return id + String(value.length).padStart(2, '0') + value;
            }

            // Build QRIS Payload
            let payload =
                emv("00", "01") +                  // Format
                emv("01", "11") +                  // Dynamic
                emv("26",
                    emv("00", "05") +              // AID
                    emv("01", mid) +              // MID Merchant
                    emv("02", orderId)            // Order ID
                ) +
                emv("52", "0000") +                // Merchant Category
                emv("53", "360") +                 // Currency IDR
                emv("54", total) +                // Amount
                emv("58", "ID") +                  // Country
                emv("59", "KIOS KANE") +           // Merchant Name
                emv("60", "JAKARTA");              // City

            // CRC16 Calculation
            function crc16(str) {
                let crc = 0xFFFF;
                for (let i = 0; i < str.length; i++) {
                    crc ^= str.charCodeAt(i) << 8;
                    for (let j = 0; j < 8; j++) {
                        crc = (crc & 0x8000) ? (crc << 1) ^ 0x1021 : (crc << 1);
                        crc &= 0xFFFF;
                    }
                }
                return crc.toString(16).toUpperCase().padStart(4, '0');
            }

            payload += "6304" + crc16(payload + "6304");

            // Generate QR
            new QRCode(qrContainer, {
                text: payload,
                width: 200,
                height: 200,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        function printReceipt() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Struk</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: monospace; max-width: 300px; margin: 0 auto; padding: 10px; background: white; }');
            printWindow.document.write('h3 { text-align: center; margin: 5px 0; color: #d4af37; font-family: "Playfair Display", serif; font-size: 1.6em; }');
            printWindow.document.write('p { text-align: center; margin: 3px 0; }');
            printWindow.document.write('hr { margin: 15px 0; border: 1px dashed #ccc; }');
            printWindow.document.write('.total-row { display: flex; justify-content: space-between; font-weight: bold; margin: 10px 0; padding-top: 10px; border-top: 2px solid #eee; }');
            printWindow.document.write('.item-row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 0.9em; }');
            printWindow.document.write('.item-details { display: flex; justify-content: space-between; margin: 3px 0; color: #777; font-size: 0.8em; }');
            printWindow.document.write('.payment-method { text-align: center; margin: 10px 0; padding: 8px; background: #f8f9fa; border-radius: 5px; font-weight: bold; }');
            printWindow.document.write('.qr-section { text-align: center; margin: 15px 0; padding: 10px; border: 1px dashed #ccc; }');
            printWindow.document.write('.qr-code { width: 100px; height: 100px; background: white; border: 1px solid #ccc; margin: 10px auto; display: flex; align-items: center; justify-content: center; font-size: 0.7em; color: #777; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');

            const receiptContent = document.getElementById('receipt-content').innerHTML;
            printWindow.document.write(receiptContent);

            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Generate QR code when page loads if payment status is qris
        <?php if (isset($payment_status) && $payment_status === 'qris'): ?>
            window.addEventListener('load', function() {
                generateQRCode();
            });
        <?php endif; ?>
    </script>
<?php include 'footer.php'; ?>