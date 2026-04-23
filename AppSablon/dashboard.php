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
        $ord = $conn->query("SELECT produk_id, jumlah FROM orders WHERE id=$order_id")->fetch_assoc();
        $conn->query("UPDATE products SET stok = stok + " . $ord['jumlah'] . " WHERE id = " . $ord['produk_id']);
        $conn->query("UPDATE orders SET status='Ditolak' WHERE id=$order_id");
        header("Location: dashboard.php?msg=refunded");
        exit();
    } elseif ($action == 'hapus') {
        $ord = $conn->query("SELECT produk_id, jumlah FROM orders WHERE id=$order_id")->fetch_assoc();
        $conn->query("UPDATE products SET stok = stok + " . $ord['jumlah'] . " WHERE id = " . $ord['produk_id']);
        $conn->query("DELETE FROM orders WHERE id=$order_id");
        header("Location: dashboard.php?msg=refunded");
        exit();
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
        .logout-btn {
            position: absolute;
            top: 24px;
            right: 24px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        
        <div class="top-header">
            <h2 class="top-header-title">Pesanan Baru</h2>
            <div>
                <a href="admin_produk.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; width: auto; text-decoration: none; margin-right: 8px;">Kelola Produk</a>
                <a href="logout.php" class="logout-link"><i data-feather="log-out"></i>Keluar</a>
            </div>
        </div>

        <div class="content-area">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'refunded'): ?>
                <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Pesanan ditolak/dihapus, stok telah dikembalikan ke katalog.</div>
            <?php endif; ?>
            
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
                        <button type="submit" name="action" value="tolak" class="btn btn-outline" style="margin-bottom: 8px;">Tolak Pesanan</button>
                        <button type="submit" name="action" value="hapus" class="btn btn-outline" style="border-color: red; color: red;" onclick="return confirm('Hapus pesanan ini secara permanen?');">Hapus</button>
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
