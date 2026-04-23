<?php
include 'koneksi.php';

$q1 = "ALTER TABLE products ADD COLUMN stok INT NOT NULL DEFAULT 100 AFTER deskripsi";
$q2 = "ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER stok";

if ($conn->query($q1)) echo "Added stok\n";
else echo "Failed stok: " . $conn->error . "\n";

if ($conn->query($q2)) echo "Added is_active\n";
else echo "Failed is_active: " . $conn->error . "\n";
?>
