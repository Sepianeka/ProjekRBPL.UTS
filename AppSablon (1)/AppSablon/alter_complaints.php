<?php
include 'koneksi.php';

$q1 = "ALTER TABLE complaints ADD COLUMN is_read_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER status";
$q2 = "ALTER TABLE complaints ADD COLUMN is_read_pelanggan TINYINT(1) NOT NULL DEFAULT 0 AFTER is_read_admin";

if ($conn->query($q1)) echo "Added is_read_admin\n";
else echo "Failed is_read_admin: " . $conn->error . "\n";

if ($conn->query($q2)) echo "Added is_read_pelanggan\n";
else echo "Failed is_read_pelanggan: " . $conn->error . "\n";
?>
