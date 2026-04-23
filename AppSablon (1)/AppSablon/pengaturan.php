<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = '';

// Get current user data
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $conn->real_escape_string($_POST['nama']);
    $whatsapp = $conn->real_escape_string($_POST['whatsapp']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Check if username already exists for other users
    $cek = $conn->query("SELECT id FROM users WHERE username='$username' AND id != $user_id");
    
    if ($cek->num_rows > 0) {
        $msg = '<div style="color: red; margin-bottom: 16px; font-weight: bold;">Username sudah dipakai orang lain!</div>';
    } else {
        if (!empty($password)) {
            // Update with password
            $pass_safe = $conn->real_escape_string($password);
            $query = "UPDATE users SET nama='$nama', whatsapp='$whatsapp', username='$username', password='$pass_safe' WHERE id=$user_id";
        } else {
            // Update without password
            $query = "UPDATE users SET nama='$nama', whatsapp='$whatsapp', username='$username' WHERE id=$user_id";
        }
        
        if ($conn->query($query)) {
            // Update session data
            $_SESSION['nama'] = $nama;
            $msg = '<div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px; font-weight: bold;">Profil berhasil diperbarui.</div>';
            // Refresh user variable
            $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
        } else {
            $msg = '<div style="color: red; margin-bottom: 16px; font-weight: bold;">Terjadi kesalahan sistem.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun</title>
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
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="back-nav">
            <a href="profil.php" style="color: var(--text-primary); margin-right: 16px; display:flex;">
                <i data-feather="arrow-left"></i>
            </a>
            <span style="font-size: 16px;">Pengaturan Akun</span>
        </div>

        <div class="content-area">
            <?= $msg ?>

            <div class="card" style="background-color: #fafafa;">
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px;">Nomor WhatsApp</label>
                        <input type="tel" name="whatsapp" class="form-input" value="<?= htmlspecialchars($user['whatsapp']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px;">Username Aktif</label>
                        <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-size: 12px;">Ganti Password <span style="font-weight:normal; color:#999;">(Opsional, abaikan jika tidak ingin diganti)</span></label>
                        <input type="password" name="password" class="form-input" placeholder="Masukkan password baru">
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <?php include 'components/bottom_nav.php'; ?>
    </div>
    <script> feather.replace(); </script>
</body>
</html>
