<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$is_admin = ($_SESSION['role'] == 'admin');

// Processing new complaints from customers
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_keluhan']) && !$is_admin) {
    $pelanggan_id = $_SESSION['user_id'];
    // We expect the customer to select a completed order or active order to file a complaint on
    $pesanan_id = intval($_POST['pesanan_id']);
    $isi_keluhan = $conn->real_escape_string($_POST['isi_keluhan']);

    $conn->query("INSERT INTO complaints (pelanggan_id, pesanan_id, isi_keluhan, is_read_admin, is_read_pelanggan) VALUES ($pelanggan_id, $pesanan_id, '$isi_keluhan', 0, 1)");
    header("Location: pesan_bantuan.php?msg=terkirim");
    exit();
}

// Processing replies from admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tanggapi_keluhan']) && $is_admin) {
    $keluhan_id = intval($_POST['keluhan_id']);
    $tanggapan = $conn->real_escape_string($_POST['tanggapan']);
    
    $conn->query("UPDATE complaints SET tanggapan_admin='$tanggapan', status='Selesai', is_read_pelanggan=0, is_read_admin=1 WHERE id=$keluhan_id");
    header("Location: pesan_bantuan.php?msg=ditanggapi");
    exit();
}

// Mark as read immediately on viewing
if ($is_admin) {
    $conn->query("UPDATE complaints SET is_read_admin = 1 WHERE is_read_admin = 0");
} else {
    $uid = $_SESSION['user_id'];
    $conn->query("UPDATE complaints SET is_read_pelanggan = 1 WHERE pelanggan_id = $uid AND is_read_pelanggan = 0");
}

// Query complaints based on role
if ($is_admin) {
    $complaints = $conn->query("
        SELECT c.*, o.no_pesanan, u.nama as nama_pelanggan
        FROM complaints c
        JOIN orders o ON c.pesanan_id = o.id
        JOIN users u ON c.pelanggan_id = u.id
        ORDER BY CASE WHEN c.status = 'Menunggu' THEN 1 ELSE 2 END, c.tanggal_keluhan DESC
    ");
} else {
    $pelanggan_id = $_SESSION['user_id'];
    $complaints = $conn->query("
        SELECT c.*, o.no_pesanan
        FROM complaints c
        JOIN orders o ON c.pesanan_id = o.id
        WHERE c.pelanggan_id = $pelanggan_id
        ORDER BY c.tanggal_keluhan DESC
    ");
    
    // Get user's orders for the complaint dropdown
    $my_orders = $conn->query("SELECT id, no_pesanan, status FROM orders WHERE pelanggan_id = $pelanggan_id ORDER BY tanggal_pesan DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Bantuan & Komplain</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .chat-bubble-me {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            padding: 12px;
            border-radius: 12px 12px 0 12px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .chat-bubble-admin {
            background-color: #E3F2FD;
            color: #000;
            padding: 12px;
            border-radius: 12px 12px 12px 0;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .complaint-thread {
            margin-bottom: 24px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            <h1>Pusat Bantuan & Komplain</h1>
            
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'terkirim'): ?>
                    <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Keluhan berhasil dikirim. Tim admin akan segera membalas.</div>
                <?php elseif($_GET['msg'] == 'ditanggapi'): ?>
                    <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Keluhan berhasil ditanggapi.</div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Form Buat Keluhan (Hanya untuk Pelanggan) -->
            <?php if(!$is_admin): ?>
                <div class="card" style="background-color: #FAFAFA; border: 1px dashed var(--border-color);">
                    <h2 style="font-size: 16px; margin-bottom: 16px;">Ajukan Komplain Baru</h2>
                    <?php if($my_orders->num_rows == 0): ?>
                        <p class="text-secondary" style="font-size: 12px;">Anda harus memiliki minimal 1 pesanan untuk mengajukan keluhan.</p>
                    <?php else: ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label class="form-label" style="font-size: 12px;">Terkait Pesanan</label>
                                <select name="pesanan_id" class="form-input" required>
                                    <option value="">-- Pilih Pesanan --</option>
                                    <?php while($mo = $my_orders->fetch_assoc()): ?>
                                        <option value="<?= $mo['id'] ?>"><?= $mo['no_pesanan'] ?> (<?= ltrim($mo['status']) ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size: 12px;">Ceritakan Kendala Anda</label>
                                <textarea name="isi_keluhan" class="form-input" rows="3" placeholder="Misal: Gelas yang saya terima ada yang pecah 2 pcs..." required></textarea>
                            </div>
                            <button type="submit" name="submit_keluhan" class="btn btn-primary" style="padding: 10px; font-size: 14px;">Kirim Keluhan</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h2 style="font-size: 18px; margin-top: 32px; margin-bottom: 16px;">Riwayat Pesan</h2>
            
            <?php if($complaints->num_rows == 0): ?>
                <div style="text-align: center; padding: 20px 0; color: var(--text-secondary);">
                    <i data-feather="message-square" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                    <p>Belum ada pesan atau komplain.</p>
                </div>
            <?php else: ?>
                <?php while($c = $complaints->fetch_assoc()): ?>
                    <div class="complaint-thread">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="font-size: 12px; font-weight: bold;">Pesanan: <?= htmlspecialchars($c['no_pesanan']) ?></span>
                            <span style="font-size: 12px; color: <?= $c['status'] == 'Selesai' ? 'green' : 'orange' ?>;">[<?= $c['status'] ?>]</span>
                        </div>
                        
                        <?php if($is_admin): ?>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Dari: <?= htmlspecialchars($c['nama_pelanggan']) ?></div>
                        <?php endif; ?>

                        <!-- Customer Message -->
                        <div style="display: flex; justify-content: flex-end;">
                            <div class="chat-bubble-me">
                                <?= nl2br(htmlspecialchars($c['isi_keluhan'])) ?>
                                <div style="font-size: 10px; opacity: 0.6; text-align: right; margin-top: 4px;"><?= date('d M, H:i', strtotime($c['tanggal_keluhan'])) ?></div>
                            </div>
                        </div>

                        <!-- Admin Reply -->
                        <?php if(!empty($c['tanggapan_admin'])): ?>
                            <div style="display: flex; justify-content: flex-start; margin-top: 8px;">
                                <div class="chat-bubble-admin">
                                    <strong>Admin DeBest:</strong><br>
                                    <?= nl2br(htmlspecialchars($c['tanggapan_admin'])) ?>
                                </div>
                            </div>
                        <?php elseif($is_admin): ?>
                            <!-- Admin Reply Box (Only if not replied and user is admin) -->
                            <form action="" method="POST" style="margin-top: 12px; background-color: #f9f9f9; padding: 12px; border-radius: 8px;">
                                <input type="hidden" name="keluhan_id" value="<?= $c['id'] ?>">
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <textarea name="tanggapan" class="form-input" rows="2" placeholder="Tulis tanggapan untuk pelanggan..." required></textarea>
                                </div>
                                <button type="submit" name="tanggapi_keluhan" class="btn btn-primary" style="padding: 8px; font-size: 12px; background-color: #28a745; border-color: #28a745;">Balas Pesan</button>
                            </form>
                        <?php else: ?>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 8px;"><em>Menunggu balasan admin...</em></div>
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
