<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$msg = '';

// Proses form tambah/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $nama = $conn->real_escape_string($_POST['nama_produk']);
        $harga = intval($_POST['harga']);
        $stok = intval($_POST['stok']);
        $desc = $conn->real_escape_string($_POST['deskripsi']);

        $conn->query("INSERT INTO products (nama_produk, harga, stok, deskripsi, is_active) VALUES ('$nama', $harga, $stok, '$desc', 1)");
        $msg = 'Produk berhasil ditambahkan.';
    } elseif (isset($_POST['edit_product'])) {
        $id = intval($_POST['product_id']);
        $nama = $conn->real_escape_string($_POST['nama_produk']);
        $harga = intval($_POST['harga']);
        $stok = intval($_POST['stok']);
        
        $conn->query("UPDATE products SET nama_produk='$nama', harga=$harga, stok=$stok WHERE id=$id");
        $msg = 'Produk berhasil diperbarui.';
    } elseif (isset($_POST['delete_product'])) {
        $id = intval($_POST['product_id']);
        // Soft delete
        $conn->query("UPDATE products SET is_active=0 WHERE id=$id");
        $msg = 'Produk berhasil dihapus (disembunyikan).';
    }
}

$products = $conn->query("SELECT * FROM products WHERE is_active=1 ORDER BY id DESC");
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
    <style>
        .form-section {
            background-color: #fafafa;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            <h1>Manajemen Produk</h1>

            <?php if(!empty($msg)): ?>
                <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;"><?= $msg ?></div>
            <?php endif; ?>

            <!-- Form Tambah Produk -->
            <div class="form-section">
                <h2 style="font-size: 16px; margin-bottom: 12px;">Tambah Produk Baru</h2>
                <form action="" method="POST">
                    <div class="form-group" style="margin-bottom: 8px;">
                        <input type="text" name="nama_produk" class="form-input" placeholder="Nama Produk" required>
                    </div>
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <input type="number" name="harga" class="form-input" placeholder="Harga /pcs" required>
                        <input type="number" name="stok" class="form-input" placeholder="Stok Awal" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 8px;">
                        <input type="text" name="deskripsi" class="form-input" placeholder="Deskripsi Singkat" required>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary" style="padding: 10px;">Simpan Produk</button>
                </form>
            </div>

            <!-- Daftar Produk -->
            <h2>Daftar Produk Aktif</h2>
            <?php if($products->num_rows == 0): ?>
                <p class="text-secondary text-center">Belum ada produk.</p>
            <?php else: ?>
                <?php while($p = $products->fetch_assoc()): ?>
                <div class="card" style="margin-bottom: 16px;">
                    <form action="" method="POST">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <div class="form-group" style="margin-bottom: 8px;">
                            <label class="form-label text-secondary" style="font-size: 10px;">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-input" value="<?= htmlspecialchars($p['nama_produk']) ?>" required>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <div style="flex: 1;">
                                <label class="form-label text-secondary" style="font-size: 10px;">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-input" value="<?= $p['harga'] ?>" required>
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label text-secondary" style="font-size: 10px;">Stok</label>
                                <input type="number" name="stok" class="form-input" value="<?= $p['stok'] ?>" required>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" name="edit_product" class="btn btn-outline" style="flex: 1; padding: 6px; font-size: 12px;">Update</button>
                            <button type="submit" name="delete_product" class="btn btn-primary" style="flex: 1; padding: 6px; font-size: 12px; background-color: #dc3545; border-color: #dc3545;" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</button>
                        </div>
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
