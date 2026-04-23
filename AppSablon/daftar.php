<?php
include 'koneksi.php';

// Handle Registration Submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $whatsapp = $_POST['whatsapp'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username exists
    $cek = $conn->query("SELECT id FROM users WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (nama, whatsapp, username, password, role) VALUES ('$nama', '$whatsapp', '$username', '$password', 'pelanggan')";
        if ($conn->query($query)) {
            header("Location: index.php?msg=registered");
            exit();
        } else {
            $error = "Terjadi kesalahan sistem.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Gelas Sablon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mobile-container">
        <div class="content-area" style="display: flex; flex-direction: column; justify-content: center; height: 100vh;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1>Daftar Akun</h1>
                <p class="text-secondary">Buat akun untuk mulai memesan</p>
            </div>

            <?php if(isset($error)): ?>
                <div style="color: red; text-align: center; margin-bottom: 16px; font-weight: bold;"><?= $error ?></div>
            <?php endif; ?>

            <form action="daftar.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-input" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-input" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="Buat username" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Buat password" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
                
                <div class="text-center mt-4">
                    <p>Sudah punya akun? <a href="index.php" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
