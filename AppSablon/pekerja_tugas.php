<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pekerja') {
    header("Location: index.php");
    exit();
}

$orders = $conn->query("
    SELECT o.*, p.nama_produk, u.nama as nama_pelanggan
    FROM orders o
    JOIN products p ON o.produk_id = p.id
    JOIN users u ON o.pelanggan_id = u.id
    WHERE o.status IN ('Siap Produksi', 'Proses Sablon')
    ORDER BY FIELD(o.status, 'Proses Sablon', 'Siap Produksi'), o.tanggal_pesan ASC
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action == 'mulai') {
        $conn->query("UPDATE orders SET status='Proses Sablon' WHERE id=$order_id");
    } elseif ($action == 'selesai') {
        $conn->query("UPDATE orders SET status='Selesai Produksi' WHERE id=$order_id");
    }
    header("Location: pekerja_tugas.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas - Pekerja</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    </style>
</head>
<body>
    <div class="mobile-container">
        
        <div class="top-header">
            <h2 class="top-header-title">Tugas Pekerja</h2>
            <a href="logout.php" class="logout-link"><i data-feather="log-out"></i>Keluar</a>
        </div>
        <div class="content-area">
            
            <?php if($orders->num_rows == 0): ?>
                <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                    <i data-feather="coffee" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Produksi selesai. Waktunya istirahat!</p>
                </div>
            <?php else: ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Id : <?= $o['no_pesanan'] ?></span>
                        <span class="status-badge">[<?= $o['status'] ?>]</span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Produk:</span>
                        <span><?= htmlspecialchars($o['nama_produk']) ?></span>
                    </div>
                    <div class="card-row">
                        <span class="text-secondary">Jumlah:</span>
                        <span style="font-weight: bold; font-size:16px;"><?= $o['jumlah'] ?> pcs</span>
                    </div>
                    <?php if(!empty($o['desain_url'])): ?>
                    <div class="card-row">
                        <span class="text-secondary">Desain:</span>
                        <a href="<?= htmlspecialchars($o['desain_url']) ?>" target="_blank" style="color: var(--primary-color); font-weight:bold;">Lihat Desain ↗</a>
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" class="mt-4">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <?php if($o['status'] == 'Siap Produksi'): ?>
                            <button type="submit" name="action" value="mulai" class="btn btn-primary" style="margin-bottom: 8px;">Mulai Proses Sablon</button>
                        <?php elseif($o['status'] == 'Proses Sablon'): ?>
                            <button type="submit" name="action" value="selesai" class="btn btn-primary" style="margin-bottom: 8px; background-color: #28a745; border-color: #28a745;">Tandai Selesai</button>
                        <?php endif; ?>
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
