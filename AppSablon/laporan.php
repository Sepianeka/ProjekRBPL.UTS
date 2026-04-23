<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pemilik') {
    header("Location: index.php");
    exit();
}

// Get Revenue Data
$total_revenue_q = $conn->query("SELECT SUM(o.jumlah * p.harga) as revenue FROM orders o JOIN products p ON o.produk_id = p.id WHERE o.status = 'Selesai Produksi'");
$total_revenue = $total_revenue_q->fetch_assoc()['revenue'] ?? 0;

$total_orders_q = $conn->query("SELECT COUNT(*) as total FROM orders");
$total_orders = $total_orders_q->fetch_assoc()['total'];

$completed_orders_q = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Selesai Produksi'");
$completed_orders = $completed_orders_q->fetch_assoc()['total'];

// Top Products
$top_products = $conn->query("
    SELECT p.nama_produk, SUM(o.jumlah) as terjual
    FROM orders o
    JOIN products p ON o.produk_id = p.id
    WHERE o.status = 'Selesai Produksi'
    GROUP BY p.id
    ORDER BY terjual DESC
    LIMIT 5
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .report-metric {
            background-color: var(--primary-color);
            color: var(--surface-color);
            padding: 24px 16px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 16px;
        }
        .metric-title {
            font-size: 14px;
            margin-bottom: 8px;
            opacity: 0.8;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
        }
</style>
</head>
<body>
    <div class="mobile-container">
        
        <div class="top-header">
            <h2 class="top-header-title">Laporan Penjualan</h2>
            <a href="logout.php" class="logout-link"><i data-feather="log-out"></i>Keluar</a>
        </div>
        
        <div class="content-area">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <span style="font-weight: bold;">Seluruh Waktu</span>
            </div>

            <div class="report-metric">
                <div class="metric-title">Total Pendapatan</div>
                <div class="metric-value">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
            </div>

            <div class="grid-2">
                <div class="card text-center" style="margin-bottom: 0;">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Total Pesanan</div>
                    <div style="font-size: 20px; font-weight: bold;"><?= $total_orders ?></div>
                </div>
                <div class="card text-center" style="margin-bottom: 0;">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Selesai</div>
                    <div style="font-size: 20px; font-weight: bold;"><?= $completed_orders ?></div>
                </div>
            </div>

            <h2 class="mt-4">Top Produk Terjual</h2>
            <div class="card">
                <div class="card-row" style="font-weight: bold; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                    <span>Produk</span>
                    <span>Terjual</span>
                </div>
                <?php if($top_products->num_rows == 0): ?>
                    <div style="text-align: center; padding: 16px 0; color: #999; font-size: 12px;">Belum ada pesanan selesai.</div>
                <?php else: ?>
                    <?php while($tp = $top_products->fetch_assoc()): ?>
                    <div class="card-row mt-4">
                        <span><?= htmlspecialchars($tp['nama_produk']) ?></span>
                        <span><?= $tp['terjual'] ?> pcs</span>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
