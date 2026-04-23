<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$orders = $conn->query("
    SELECT o.*, p.nama_produk, u.nama as nama_pelanggan
    FROM orders o
    JOIN products p ON o.produk_id = p.id
    JOIN users u ON o.pelanggan_id = u.id
    WHERE o.status = 'Menunggu Verifikasi'
    ORDER BY o.tanggal_pesan DESC
");

// Handle Verifikasi via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action == 'verifikasi') {
        $conn->query("UPDATE orders SET status='Butuh Desain' WHERE id=$order_id");
    } elseif ($action == 'tolak') {
        $conn->begin_transaction();
        
        // Kembalikan stok produk jika ditolak
        $order_info = $conn->query("SELECT produk_id, jumlah FROM orders WHERE id=$order_id")->fetch_assoc();
        $produk_id = $order_info['produk_id'];
        $jumlah = $order_info['jumlah'];
        
        $update_stok = $conn->query("UPDATE products SET stok = stok + $jumlah WHERE id=$produk_id");
        $update_order = $conn->query("UPDATE orders SET status='Ditolak' WHERE id=$order_id");
        
        if ($update_stok && $update_order) {
            $conn->commit();
        } else {
            $conn->rollback();
        }
    }
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            <h1>Pesanan Perlu Verifikasi</h1>
            
            <?php if($orders->num_rows == 0): ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="check-circle" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Semua pesanan sudah ditangani.</p>
                </div>
            <?php else: ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Id : <?= $o['no_pesanan'] ?></span>
                        <span class="status-badge">[Menunggu]</span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Pelanggan:</span>
                        <span><?= htmlspecialchars($o['nama_pelanggan']) ?></span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Produk:</span>
                        <span><?= htmlspecialchars($o['nama_produk']) ?></span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Jumlah:</span>
                        <span><?= $o['jumlah'] ?> pcs</span>
                    </div>
                    <?php if(!empty($o['catatan'])): ?>
                    <div class="card-row">
                        <span class="text-secondary">Catatan:</span>
                        <span style="text-align: right; max-width: 60%;"><?= htmlspecialchars($o['catatan']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" class="mt-4">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <button type="submit" name="action" value="verifikasi" class="btn btn-primary" style="margin-bottom: 8px;">Verifikasi</button>
                        <button type="submit" name="action" value="tolak" class="btn btn-outline">Tolak Pesanan</button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
