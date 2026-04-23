<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desainer') {
    header("Location: index.php");
    exit();
}

$orders = $conn->query("
    SELECT o.*, p.nama_produk, u.nama as nama_pelanggan
    FROM orders o
    JOIN products p ON o.produk_id = p.id
    JOIN users u ON o.pelanggan_id = u.id
    WHERE o.status = 'Butuh Desain' OR o.status = 'Revisi Desain'
    ORDER BY o.tanggal_pesan ASC
");

// Handle Upload Desain via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $url_desain = $_POST['url_desain'];

    $stmt = $conn->prepare("UPDATE orders SET desain_url=?, status='Menunggu Persetujuan Desain' WHERE id=?");
    $stmt->bind_param("si", $url_desain, $order_id);
    $stmt->execute();
    
    header("Location: desainer_tugas.php?msg=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Desainer - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            <h1 style="font-size: 20px;">Daftar Pesanan Butuh Desain</h1>
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
                <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Desain berhasil dikirim!</div>
            <?php endif; ?>

            <?php if($orders->num_rows == 0): ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="smile" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Hore! Belum ada tugas desain saat ini.</p>
                </div>
            <?php else: ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Id : <?= $o['no_pesanan'] ?></span>
                        <span class="status-badge" style="color: <?= $o['status'] == 'Revisi Desain' ? 'red' : 'inherit' ?>">[<?= $o['status'] ?>]</span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Pelanggan:</span>
                        <span><?= htmlspecialchars($o['nama_pelanggan']) ?></span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Produk:</span>
                        <span><?= htmlspecialchars($o['nama_produk']) ?> (<?= $o['jumlah'] ?> pcs)</span>
                    </div>
                    <?php if(!empty($o['catatan'])): ?>
                    <div class="card-row">
                        <span class="text-secondary">Catatan/Ide:</span>
                        <span style="text-align: right; max-width: 60%; font-weight:bold;"><?= htmlspecialchars($o['catatan']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" class="mt-4" style="border-top: 1px solid #eee; padding-top: 12px;">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <div class="form-group">
                            <label class="form-label" style="font-size: 12px;">Link Desain (Google Drive / Figma)</label>
                            <input type="url" name="url_desain" class="form-input" placeholder="Masukkan Link" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 10px; font-size: 14px;">Kirim Desain ke Pelanggan</button>
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
