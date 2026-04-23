-- Database for Gelas Sablon
CREATE DATABASE IF NOT EXISTS db_sablon;
USE db_sablon;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('pelanggan', 'admin', 'desainer', 'pekerja', 'pemilik') NOT NULL DEFAULT 'pelanggan'
);

-- Initial Dummy Users for Testing Roles
INSERT IGNORE INTO users (nama, whatsapp, username, password, role) VALUES
('Budi Pelanggan', '081234567890', 'budi_user', 'password123', 'pelanggan'),
('Siti Admin', '081234567891', 'admin_siti', 'password123', 'admin'),
('Joko Desainer', '081234567892', 'joko_desain', 'password123', 'desainer'),
('Agus Pekerja', '081234567893', 'agus_kerja', 'password123', 'pekerja'),
('Pak Bos', '081234567894', 'owner123', 'password123', 'pemilik');

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT
);

INSERT IGNORE INTO products (nama_produk, harga, deskripsi) VALUES
('Gelas Kaca Doff 300ml', 5000, 'Gelas kaca tebal dengan finishing doff elegan.'),
('Gelas Plastik Cup 16oz', 1500, 'Gelas plastik praktis untuk minuman dingin.'),
('Mug Putih Polos', 15000, 'Mug keramik putih standar cocok untuk souvenir.'),
('Gelas Kaca Bening 250ml', 4500, 'Gelas kaca bening bentuk silinder.');

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_pesanan VARCHAR(20) UNIQUE NOT NULL,
    pelanggan_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    catatan TEXT,
    desain_url TEXT,
    status ENUM(
        'Menunggu Verifikasi', 
        'Ditolak', 
        'Butuh Desain', 
        'Menunggu Persetujuan Desain', 
        'Revisi Desain', 
        'Siap Produksi', 
        'Proses Sablon', 
        'Selesai Produksi'
    ) NOT NULL DEFAULT 'Menunggu Verifikasi',
    tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES users(id),
    FOREIGN KEY (produk_id) REFERENCES products(id)
);

-- Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    pesanan_id INT NOT NULL,
    isi_keluhan TEXT NOT NULL,
    tanggapan_admin TEXT,
    status ENUM('Menunggu', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Menunggu',
    tanggal_keluhan DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES users(id),
    FOREIGN KEY (pesanan_id) REFERENCES orders(id)
);
