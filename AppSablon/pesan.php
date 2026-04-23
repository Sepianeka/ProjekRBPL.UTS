<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: pelanggan_home.php");
    exit();
}

$id_produk = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id='$id_produk'");
$produk = $result->fetch_assoc();

if (!$produk || $produk['is_deleted'] == 1) {
    header("Location: pelanggan_home.php");
    exit();
}

// Proses Pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah = intval($_POST['jumlah']);
    $catatan = $_POST['catatan'];
    $pelanggan_id = $_SESSION['user_id'];
    
    if ($jumlah < 10) {
        $error = "Pemesanan minimal 10 pcs!";
    } elseif ($jumlah > $produk['stok']) {
        $error = "Stok tidak mencukupi! Sisa stok: " . $produk['stok'];
    } else {
        // Generate nomor pesanan unik
        $no_pesanan = "GS-" . time() . rand(10, 99);

        // Kurangi stok
        $conn->query("UPDATE products SET stok = stok - $jumlah WHERE id = $id_produk");

        $stmt = $conn->prepare("INSERT INTO orders (no_pesanan, pelanggan_id, produk_id, jumlah, catatan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $no_pesanan, $pelanggan_id, $id_produk, $jumlah, $catatan);
        
        if ($stmt->execute()) {
            header("Location: pelanggan_home.php?msg=success");
            exit();
        } else {
            $conn->query("UPDATE products SET stok = stok + $jumlah WHERE id = $id_produk");
            $error = "Gagal membuat pesanan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan <?= htmlspecialchars($produk['nama_produk']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .back-nav {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            background-color: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="back-nav">
            <a href="pelanggan_home.php" style="color: var(--text-primary); margin-right: 16px;">
                <i data-feather="arrow-left"></i>
            </a>
            <h2 style="margin: 0; font-size: 18px;">Detail Produk</h2>
        </div>

        <div class="content-area" style="padding-top: 16px;">
            <div style="width: 100%; height: 200px; background-color: #E0E0E0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 16px;">
                <i data-feather="image" style="width: 48px; height: 48px; color: var(--text-secondary);"></i>
            </div>

            <h1 style="margin-bottom: 8px;"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
            <div style="font-size: 20px; font-weight: bold; color: var(--primary-color); margin-bottom: 12px;">
                Rp <?= number_format($produk['harga'], 0, ',', '.') ?> /pcs
            </div>
            
            <?php if($produk['stok'] > 0): ?>
                <div style="margin-bottom: 16px;"><span class="badge-stok">Sisa Stok: <?= $produk['stok'] ?> Pcs</span></div>
            <?php else: ?>
                <div style="margin-bottom: 16px;"><span class="badge-stok badge-habis">Stok Habis</span></div>
            <?php endif; ?>
            
            <p class="text-secondary" style="margin-bottom: 24px; line-height: 1.5;">
                <?= htmlspecialchars($produk['deskripsi']) ?>
            </p>

            <?php if(isset($error)): ?>
                <div style="color: red; margin-bottom: 16px; font-weight: bold;"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Jumlah Pesanan (Pcs)</label>
                    <input type="number" name="jumlah" class="form-input" min="10" value="50" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan / Ide Desain</label>
                    <textarea name="catatan" class="form-input" rows="4" placeholder="Misal: Saya ingin ada logo perusahaan di tengah gelas"></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" <?= $produk['stok'] < 10 ? 'disabled style="background-color: #ccc; cursor: not-allowed;"' : '' ?>>Pesan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
