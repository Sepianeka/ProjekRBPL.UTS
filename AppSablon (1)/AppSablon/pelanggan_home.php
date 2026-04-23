<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: index.php");
    exit();
}

// Fetch Products from Database with optional Category Filter
$filter = isset($_GET['kategori']) ? $conn->real_escape_string($_GET['kategori']) : '';

if ($filter == 'kaca') {
    $products = $conn->query("SELECT * FROM products WHERE is_active=1 AND nama_produk LIKE '%kaca%'");
} elseif ($filter == 'plastik') {
    $products = $conn->query("SELECT * FROM products WHERE is_active=1 AND (nama_produk LIKE '%plastik%' OR nama_produk LIKE '%cup%')");
} elseif ($filter == 'mug') {
    $products = $conn->query("SELECT * FROM products WHERE is_active=1 AND nama_produk LIKE '%mug%'");
} else {
    $products = $conn->query("SELECT * FROM products WHERE is_active=1");
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h1 style="margin-bottom: 4px;">Halo, <?= explode(' ', $_SESSION['nama'])[0] ?>!</h1>
                    <p class="text-secondary">Mau sablon gelas apa hari ini?</p>
                </div>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
                <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Pesanan berhasil dibuat!</div>
            <?php endif; ?>

            <!-- Kategori -->
            <h2 class="mt-4">Kategori</h2>
            <div style="display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 16px;">
                <a href="pelanggan_home.php" class="btn <?= $filter == '' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 8px 16px; width: auto; font-size: 14px; text-decoration: none;">Semua</a>
                <a href="pelanggan_home.php?kategori=kaca" class="btn <?= $filter == 'kaca' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 8px 16px; width: auto; font-size: 14px; white-space: nowrap; text-decoration: none;">Gelas Kaca</a>
                <a href="pelanggan_home.php?kategori=plastik" class="btn <?= $filter == 'plastik' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 8px 16px; width: auto; font-size: 14px; white-space: nowrap; text-decoration: none;">Gelas Plastik</a>
                <a href="pelanggan_home.php?kategori=mug" class="btn <?= $filter == 'mug' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 8px 16px; width: auto; font-size: 14px; white-space: nowrap; text-decoration: none;">Mug Keramik</a>
            </div>

            <!-- Produk Terpopuler -->
            <h2 class="mt-4"><?= $filter == '' ? 'Katalog Produk' : 'Hasil Filter' ?></h2>
            <div class="grid-2">
                <?php if($products->num_rows == 0): ?>
                    <div style="grid-column: span 2; text-align: center; padding: 20px; color: var(--text-secondary);">Tidak ada produk di kategori ini.</div>
                <?php else: ?>
                    <?php while($p = $products->fetch_assoc()): ?>
                    <a href="pesan.php?id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-card">
                            <div class="product-image">
                                <i data-feather="image"></i>
                            </div>
                            <div class="product-info">
                            <span class="product-title"><?= htmlspecialchars($p['nama_produk']) ?></span>
                            <span class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?> /pcs</span>
                            <span style="font-size: 10px; color: <?= $p['stok'] > 0 ? 'var(--text-secondary)' : '#dc3545' ?>;">
                                <?= $p['stok'] > 0 ? "Stok: " . $p['stok'] : "Stok Habis" ?>
                            </span>
                        </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
</body>
</html>
