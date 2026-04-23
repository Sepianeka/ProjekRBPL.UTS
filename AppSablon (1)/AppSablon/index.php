<?php
include 'koneksi.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role == 'pelanggan') header("Location: pelanggan_home.php");
    elseif ($role == 'admin') header("Location: dashboard.php");
    elseif ($role == 'desainer') header("Location: desainer_tugas.php");
    elseif ($role == 'pekerja') header("Location: pekerja_tugas.php");
    elseif ($role == 'pemilik') header("Location: laporan.php");
    exit();
}

// Handle Login Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'pelanggan') header("Location: pelanggan_home.php");
        elseif ($user['role'] == 'admin') header("Location: dashboard.php");
        elseif ($user['role'] == 'desainer') header("Location: desainer_tugas.php");
        elseif ($user['role'] == 'pekerja') header("Location: pekerja_tugas.php");
        elseif ($user['role'] == 'pemilik') header("Location: laporan.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mobile-container">
        <div class="content-area" style="display: flex; flex-direction: column; justify-content: center; height: 100vh;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1>Gelas Sablon</h1>
                <p class="text-secondary">Sistem Manajemen Pesanan</p>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                <div style="color: green; text-align: center; margin-bottom: 16px; font-weight: bold;">Pendaftaran Berhasil! Silakan Login.</div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div style="color: red; text-align: center; margin-bottom: 16px; font-weight: bold;"><?= $error ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="Masukkan username" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                
                <div class="text-center mt-4">
                    <p>Belum punya akun? <a href="daftar.php" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">Daftar</a></p>
                </div>

                 <!-- Guide for Teacher/Tester (Optional, but very helpful) -->
                <div style="margin-top: 40px; font-size:12px; color:#999; text-align:center;">
                    <u>Test Accounts (Pass: password123)</u><br>
                    budi_user (Pelanggan)<br>
                    admin_siti (Admin)<br>
                    joko_desain (Desainer)<br>
                    agus_kerja (Pekerja)<br>
                    owner123 (Pemilik)
                </div>
            </form>
        </div>
    </div>
</body>
</html>
