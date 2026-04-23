<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$products = $conn->query("SELECT * FROM products WHERE is_deleted = 0");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produk_id = intval($_POST['produk_id']);
    $action = $_POST['action'];

    if ($action == 'hapus') {
        $conn->query("UPDATE products SET is_deleted=1 WHERE id=$produk_id");
    } elseif ($action == 'update_stok') {
        $stok_baru = intval($_POST['stok']);
        $conn->query("UPDATE products SET stok=$stok_baru WHERE id=$produk_id");
    }
    header("Location: admin_produk.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <body>
    <div class="mobile-container">
        <div class="top-header">
            <h2 class="top-header-title">Kelola Produk</h2>
            <a href="dashboard.php" style="color: var(--text-primary); text-decoration: none; font-weight: bold; font-size: 14px;">Kembali</a>
        </div>
        <div class="content-area">
            <?php if($products->num_rows == 0): ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="box" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Semua produk sudah terhapus atau kosong.</p>
                </div>
            <?php else: ?>
                <?php while($p = $products->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-row">
                        <span style="font-weight: bold;"><?= htmlspecialchars($p['nama_produk']) ?></span>
                        <span class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                    </div>
                    <?php if(!empty($p['deskripsi'])): ?>
                    <div class="card-row" style="margin-top: 8px;">
                        <span class="text-secondary" style="font-size: 12px;"><?= htmlspecialchars($p['deskripsi']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div style="border-top: 1px solid var(--border-color); margin-top: 12px; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <form action="" method="POST" style="display: flex; gap: 8px; align-items: center;">
                            <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                            <label style="font-size: 12px; font-weight: bold;">Stok:</label>
                            <input type="number" name="stok" value="<?= $p['stok'] ?>" class="form-input" style="width: 80px; padding: 6px; font-size: 12px; min-height: unset; border-radius: 6px;">
                            <button type="submit" name="action" value="update_stok" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; width: auto;">Update</button>
                        </form>
                        
                        <form action="" method="POST">
                            <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                            <button type="submit" name="action" value="hapus" class="btn btn-outline" style="border-color: red; color: red; padding: 6px 12px; font-size: 12px; display: inline-block; width: auto;" onclick="return confirm('Sembunyikan / Hapus produk ini dari katalog pelanggan?');">
                                <i data-feather="trash-2" style="width: 14px; margin-right: 4px; vertical-align: middle;"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
