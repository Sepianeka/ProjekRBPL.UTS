<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: index.php");
    exit();
}

$search_query = "";
$products = null;

if (isset($_GET['q'])) {
    $search_query = $conn->real_escape_string($_GET['q']);
    $products = $conn->query("SELECT * FROM products WHERE (nama_produk LIKE '%$search_query%' OR deskripsi LIKE '%$search_query%') AND is_deleted = 0");
} else {
    // Default show all or recommended
    $products = $conn->query("SELECT * FROM products WHERE is_deleted = 0 LIMIT 10");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Produk - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .search-area {
            position: sticky;
            top: 0;
            background-color: var(--surface-color);
            padding: 16px 24px;
            z-index: 5;
            border-bottom: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="search-area">
            <h1 style="font-size: 20px; margin-bottom: 16px;">Temukan Sablon Impianmu</h1>
            <form action="cari.php" method="GET" class="form-group" style="position: relative; margin-bottom: 0;">
                <i data-feather="search" style="position: absolute; left: 12px; top: 12px; color: var(--text-secondary); width: 18px;"></i>
                <input type="text" name="q" class="form-input" placeholder="Cari gelas, mug, plastik..." value="<?= htmlspecialchars($search_query) ?>" style="padding-left: 40px; padding-right: 40px;" autofocus>
                <?php if(!empty($search_query)): ?>
                    <a href="cari.php" style="position: absolute; right: 12px; top: 12px; color: var(--text-secondary);">
                        <i data-feather="x" style="width: 18px;"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="content-area" style="padding-top: 16px;">
            <?php if(!empty($search_query)): ?>
                <p class="text-secondary" style="margin-bottom: 16px;">Hasil pencarian untuk "<strong><?= htmlspecialchars($search_query) ?></strong>"</p>
            <?php else: ?>
                <h2 style="font-size: 16px; margin-bottom: 16px;">Rekomendasi Kami</h2>
            <?php endif; ?>

            <?php if($products && $products->num_rows > 0): ?>
                <div class="grid-2">
                    <?php while($p = $products->fetch_assoc()): ?>
                    <a href="pesan.php?id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-card">
                            <div class="product-image">
                                <i data-feather="image"></i>
                            </div>
                            <div class="product-info">
                                <span class="product-title"><?= htmlspecialchars($p['nama_produk']) ?></span>
                                <span class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                                <?php if($p['stok'] > 0): ?>
                                    <div style="margin-top: 12px;"><span class="badge-stok">Stok: <?= $p['stok'] ?></span></div>
                                <?php else: ?>
                                    <div style="margin-top: 12px;"><span class="badge-stok badge-habis">Habis</span></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="frown" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Produk tidak ditemukan. Coba kata kunci lain.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
