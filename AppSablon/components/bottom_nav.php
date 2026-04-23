<?php
// Get current page to highlight active tab
$current_page = basename($_SERVER['PHP_SELF']);

// Define home link depending on role
$home_link = 'index.php';
$pesanan_link = '#';

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role == 'pelanggan') {
        $home_link = 'pelanggan_home.php';
        $pesanan_link = 'pelanggan_status.php';
    } elseif ($role == 'admin') {
        $home_link = 'dashboard.php';
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
    <a href="pesan_bantuan.php" class="nav-item <?= $current_page == 'pesan_bantuan.php' ? 'active' : '' ?>">
        <i data-feather="message-circle" class="nav-icon"></i>
        <span>Pesan</span>
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
