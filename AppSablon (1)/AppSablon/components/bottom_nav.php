<?php
// Get current page to highlight active tab
$current_page = basename($_SERVER['PHP_SELF']);

// Define home link depending on role
$home_link = 'index.php';
$pesanan_link = '#';
$unread_count = 0;

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $uid = $_SESSION['user_id'];
    
    if ($role == 'pelanggan') {
        $home_link = 'pelanggan_home.php';
        $pesanan_link = 'pelanggan_status.php';
        
        // Notifications: count messages replied by Admin
        if (isset($conn)) {
            $q = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE pelanggan_id=$uid AND is_read_pelanggan=0");
            if($q) $unread_count = $q->fetch_assoc()['c'];
        }
    } elseif ($role == 'admin') {
        $home_link = 'dashboard.php';
        
        // Notifications: count complaints waiting for Admin reply
        if (isset($conn)) {
            $q = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE is_read_admin=0");
            if($q) $unread_count = $q->fetch_assoc()['c'];
        }
    } elseif ($role == 'desainer') {
        $home_link = 'desainer_tugas.php';
    } elseif ($role == 'pekerja') {
        $home_link = 'pekerja_tugas.php';
    } elseif ($role == 'pemilik') {
        $home_link = 'laporan.php';
    }
}
?>
<nav class="bottom-nav">
    <a href="<?= $home_link ?>" class="nav-item <?= ($current_page == 'pelanggan_home.php' || $current_page == 'dashboard.php' || $current_page == 'desainer_tugas.php' || $current_page == 'pekerja_tugas.php' || $current_page == 'laporan.php') ? 'active' : '' ?>">
        <i data-feather="home" class="nav-icon"></i>
        <span>Home</span>
    </a>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <a href="kelola_produk.php" class="nav-item <?= $current_page == 'kelola_produk.php' ? 'active' : '' ?>">
        <i data-feather="box" class="nav-icon"></i>
        <span>Produk</span>
    </a>
    <?php endif; ?>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'pelanggan'): ?>
    <a href="cari.php" class="nav-item <?= $current_page == 'cari.php' ? 'active' : '' ?>">
        <i data-feather="search" class="nav-icon"></i>
        <span>Cari</span>
    </a>
    <a href="<?= $pesanan_link ?>" class="nav-item <?= ($current_page == 'pelanggan_status.php' || $current_page == 'pesan.php') ? 'active' : '' ?>">
        <i data-feather="shopping-bag" class="nav-icon"></i>
        <span>Pesanan</span>
    </a>
    <?php endif; ?>
    <a href="pesan_bantuan.php" class="nav-item <?= $current_page == 'pesan_bantuan.php' ? 'active' : '' ?>" style="position: relative;">
        <i data-feather="message-circle" class="nav-icon"></i>
        <span>Pesan</span>
        <?php if($unread_count > 0): ?>
            <span style="position: absolute; top: 4px; right: 8px; background: #EF4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; font-weight: bold; display: flex; align-items: center; justify-content: center; border: 2px solid #FFFFFF; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);"><?= $unread_count ?></span>
        <?php endif; ?>
    </a>
    <a href="profil.php" class="nav-item <?= $current_page == 'profil.php' ? 'active' : '' ?>">
        <i data-feather="user" class="nav-icon"></i>
        <span>Profil</span>
    </a>
</nav>

<script>
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
