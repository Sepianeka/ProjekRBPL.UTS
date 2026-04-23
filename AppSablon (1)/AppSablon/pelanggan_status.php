<?php
include 'koneksi.php';

// Check login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: index.php");
    exit();
}

$pelanggan_id = $_SESSION['user_id'];
$orders = $conn->query("
    SELECT o.*, p.nama_produk
    FROM orders o
    JOIN products p ON o.produk_id = p.id
    WHERE o.pelanggan_id = $pelanggan_id
    ORDER BY o.tanggal_pesan DESC
");

// Handle Setujui / Revisi Desain
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action == 'setuju') {
        $conn->query("UPDATE orders SET status='Siap Produksi' WHERE id=$order_id");
    } elseif ($action == 'revisi') {
        $catatan_revisi = $conn->real_escape_string($_POST['catatan_revisi']);
        $conn->query("UPDATE orders SET status='Revisi Desain', catatan=CONCAT(catatan, '\\n\\n[Revisi]: ', '$catatan_revisi') WHERE id=$order_id");
    }
    header("Location: pelanggan_status.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            <h1>Status Pesanan Anda</h1>
            
            <?php if($orders->num_rows == 0): ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="shopping-bag" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Anda belum memiliki pesanan aktif.</p>
                </div>
            <?php else: ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><?= $o['no_pesanan'] ?></span>
                        <span class="status-badge" style="color: <?= $o['status'] == 'Selesai Produksi' ? 'green' : 'inherit' ?>">[<?= $o['status'] ?>]</span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Produk:</span>
                        <span><?= htmlspecialchars($o['nama_produk']) ?> (<?= $o['jumlah'] ?> pcs)</span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Tanggal:</span>
                        <span><?= date('d M Y, H:i', strtotime($o['tanggal_pesan'])) ?></span>
                    </div>

                    <?php if($o['status'] == 'Menunggu Persetujuan Desain'): ?>
                        <div style="margin-top: 16px; padding: 12px; border: 1px solid #ccc; border-radius: 8px; background-color: #fafafa;">
                            <p style="font-weight: bold; margin-bottom: 8px;">Desainer telah mengirimkan hasil:</p>
                            <a href="<?= htmlspecialchars($o['desain_url']) ?>" target="_blank" class="btn btn-outline" style="margin-bottom: 12px; padding: 8px;">Lihat Desain ↗</a>
                            
                            <form action="" method="POST">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <button type="submit" name="action" value="setuju" class="btn btn-primary" style="margin-bottom: 8px;">Setujui Desain</button>
                                
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <input type="text" name="catatan_revisi" class="form-input" placeholder="Tulis letak revisi..." style="font-size: 12px;">
                                </div>
                                <button type="submit" name="action" value="revisi" class="btn btn-outline" style="border-color: red; color: red;">Minta Revisi</button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
