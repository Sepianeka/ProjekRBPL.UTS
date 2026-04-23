<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_q = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_q->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .profile-header {
            text-align: center;
            padding: 32px 0;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            background-color: var(--primary-color);
            color: var(--surface-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 16px auto;
        }
        .menu-list {
            list-style: none;
            padding: 0;
            margin-top: 24px;
        }
        .menu-list li {
            border-bottom: 1px solid var(--border-color);
        }
        .menu-list a {
            display: flex;
            align-items: center;
            padding: 16px 0;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: bold;
        }
        .menu-list i {
            margin-right: 16px;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="content-area">
            
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                </div>
                <h2><?= htmlspecialchars($user['nama']) ?></h2>
                <p class="text-secondary">@<?= htmlspecialchars($user['username']) ?></p>
                <span class="status-badge" style="display:inline-block; margin-top:8px;">Role: <?= ucfirst($user['role']) ?></span>
            </div>

            <div class="card" style="margin-bottom: 24px;">
                <div class="card-row">
                    <span class="text-secondary">WhatsApp:</span>
                    <span style="font-weight: bold;"><?= htmlspecialchars($user['whatsapp']) ?></span>
                </div>
            </div>

            <ul class="menu-list">
                <li>
                    <a href="pengaturan.php">
                        <i data-feather="settings"></i>
                        Pengaturan Akun
                    </a>
                </li>
                <li>
                    <a href="pesan_bantuan.php">
                        <i data-feather="help-circle"></i>
                        Pusat Bantuan
                    </a>
                </li>
                <li>
                    <a href="logout.php" style="color: red;">
                        <i data-feather="log-out" style="color: red;"></i>
                        Keluar
                    </a>
                </li>
            </ul>

        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
</body>
</html>
