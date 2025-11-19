<?php 
include 'header.php';
require_login();

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

function get_sales_statistics($pdo) {
    $stats = [];
    
    // Total penjualan hari ini
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_today, COUNT(*) as orders_today FROM transactions WHERE DATE(transaction_date) = CURDATE()");
    $stmt->execute();
    $stats['today'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Total penjualan minggu ini
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_week, COUNT(*) as orders_week FROM transactions WHERE YEARWEEK(transaction_date, 1) = YEARWEEK(CURDATE(), 1)");
    $stmt->execute();
    $stats['week'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Total penjualan bulan ini
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_month, COUNT(*) as orders_month FROM transactions WHERE MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())");
    $stmt->execute();
    $stats['month'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Total penjualan keseluruhan
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_all, COUNT(*) as orders_all FROM transactions");
    $stmt->execute();
    $stats['all'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $stats;
}

function get_recent_transactions($pdo, $limit = 10) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Return empty array jika ada error
        return [];
    }
}

function get_top_items($pdo, $limit = 5) {
    try {
        $stmt = $pdo->prepare("SELECT item_name, item_category, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue FROM transaction_items GROUP BY item_name, item_category ORDER BY total_quantity DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Return empty array jika ada error
        return [];
    }
}

function get_payment_statistics($pdo) {
    $stmt = $pdo->prepare("SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total FROM transactions GROUP BY payment_method");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil statistik
$stats = get_sales_statistics($pdo);
$recent_transactions = get_recent_transactions($pdo);
$top_items = get_top_items($pdo);
$payment_stats = get_payment_statistics($pdo);
?>

    <!-- Admin Dashboard Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Dashboard Admin</h2>
            </div>
            
            <div class="dashboard-container">
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                        <h3 style="margin-bottom: 10px;">Hari Ini</h3>
                        <p style="font-size: 1.2em; font-weight: bold;">Rp <?php echo $stats['today']['total_today'] ? number_format($stats['today']['total_today'], 0, ',', '.') : '0'; ?></p>
                        <p><?php echo $stats['today']['orders_today'] ?? '0'; ?> Pesanan</p>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                        <h3 style="margin-bottom: 10px;">Minggu Ini</h3>
                        <p style="font-size: 1.2em; font-weight: bold;">Rp <?php echo $stats['week']['total_week'] ? number_format($stats['week']['total_week'], 0, ',', '.') : '0'; ?></p>
                        <p><?php echo $stats['week']['orders_week'] ?? '0'; ?> Pesanan</p>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                        <h3 style="margin-bottom: 10px;">Bulan Ini</h3>
                        <p style="font-size: 1.2em; font-weight: bold;">Rp <?php echo $stats['month']['total_month'] ? number_format($stats['month']['total_month'], 0, ',', '.') : '0'; ?></p>
                        <p><?php echo $stats['month']['orders_month'] ?? '0'; ?> Pesanan</p>
                    </div>
                    
                    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px; text-align: center;">
                        <h3 style="margin-bottom: 10px;">Total</h3>
                        <p style="font-size: 1.2em; font-weight: bold;">Rp <?php echo $stats['all']['total_all'] ? number_format($stats['all']['total_all'], 0, ',', '.') : '0'; ?></p>
                        <p><?php echo $stats['all']['orders_all'] ?? '0'; ?> Pesanan</p>
                    </div>
                </div>
                
                <!-- Recent Transactions -->
                <div class="dashboard-section" style="margin-top: 30px;">
                    <h3>Transaksi Terbaru</h3>
                    <div class="transactions-table" style="overflow-x: auto; margin-top: 15px;">
                        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                            <thead>
                                <tr style="background: var(--secondary); color: white;">
                                    <th style="padding: 12px; text-align: left;">ID Pesanan</th>
                                    <th style="padding: 12px; text-align: left;">Pelanggan</th>
                                    <th style="padding: 12px; text-align: left;">Metode</th>
                                    <th style="padding: 12px; text-align: right;">Total</th>
                                    <th style="padding: 12px; text-align: center;">Status</th>
                                    <th style="padding: 12px; text-align: center;">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_transactions)): ?>
                                    <tr>
                                        <td colspan="6" style="padding: 20px; text-align: center; color: #777;">Belum ada transaksi</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px;"><?php echo htmlspecialchars($transaction['order_id']); ?></td>
                                        <td style="padding: 12px;">
                                            <?php echo htmlspecialchars($transaction['customer_name'] ?? 'Tamu'); ?>
                                            <?php if (empty($transaction['customer_name'])): ?>
                                                <span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.8em;">Non-login</span>
                                            <?php else: ?>
                                                <span style="background: #27ae60; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.8em;">Login</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px;"><?php echo htmlspecialchars(ucfirst($transaction['payment_method'])); ?></td>
                                        <td style="padding: 12px; text-align: right;">Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.9em;">
                                                <?php echo htmlspecialchars(ucfirst($transaction['payment_status'])); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px; text-align: center;"><?php echo date('d/m/Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Top Selling Items -->
                <div class="dashboard-section" style="margin-top: 30px;">
                    <h3>Item Paling Laris</h3>
                    <div class="top-items" style="margin-top: 15px;">
                        <?php if (empty($top_items)): ?>
                            <p style="text-align: center; color: #777;">Belum ada item yang terjual</p>
                        <?php else: ?>
                            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background: var(--secondary); color: white;">
                                        <th style="padding: 12px; text-align: left;">Nama Item</th>
                                        <th style="padding: 12px; text-align: left;">Kategori</th>
                                        <th style="padding: 12px; text-align: center;">Terjual</th>
                                        <th style="padding: 12px; text-align: right;">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_items as $item): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px;"><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td style="padding: 12px;"><?php echo htmlspecialchars($item['item_category']); ?></td>
                                        <td style="padding: 12px; text-align: center;"><?php echo $item['total_quantity']; ?></td>
                                        <td style="padding: 12px; text-align: right;">Rp <?php echo number_format($item['total_revenue'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Payment Method Statistics -->
                <div class="dashboard-section" style="margin-top: 30px;">
                    <h3>Statistik Metode Pembayaran</h3>
                    <div class="payment-stats" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 20px;">
                        <?php foreach ($payment_stats as $stat): ?>
                            <div style="flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <h4 style="color: var(--secondary); margin-bottom: 10px;"><?php echo htmlspecialchars(ucfirst($stat['payment_method'])); ?></h4>
                                <p style="font-size: 1.5em; font-weight: bold; color: var(--primary);"><?php echo $stat['count']; ?></p>
                                <p style="color: #777;">Transaksi</p>
                                <p style="font-size: 1.2em; font-weight: bold; margin-top: 10px;">Rp <?php echo number_format($stat['total'], 0, ',', '.'); ?></p>
                                <p style="color: #777;">Total Pendapatan</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .dashboard-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 2px solid var(--primary);
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .dashboard-section h3 {
            color: var(--secondary);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
        
        table {
            width: 100%;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: var(--secondary);
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
<?php include 'footer.php'; ?>