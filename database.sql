-- =============================================
-- DATABASE: food_ordering
-- =============================================

CREATE DATABASE IF NOT EXISTS food_ordering CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_ordering;

-- Tabel Kategori Makanan
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    ikon VARCHAR(10) DEFAULT '🍽️',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Menu Makanan
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    nama VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    tersedia TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Tabel Pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    telepon VARCHAR(20),
    email VARCHAR(150),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan VARCHAR(20) UNIQUE NOT NULL,
    pelanggan_id INT,
    nama_pelanggan VARCHAR(150) NOT NULL,
    telepon VARCHAR(20),
    alamat_pengiriman TEXT,
    catatan TEXT,
    total_harga DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('menunggu','diproses','dikirim','selesai','dibatalkan') DEFAULT 'menunggu',
    metode_pembayaran ENUM('tunai','transfer','ewallet') DEFAULT 'tunai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE SET NULL
);

-- Tabel Detail Pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    menu_id INT,
    nama_menu VARCHAR(200) NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE SET NULL
);

-- Data Awal: Kategori
INSERT INTO kategori (nama, ikon) VALUES
('Makanan Utama', '🍛'),
('Minuman', '🥤'),
('Snack & Camilan', '🍟'),
('Dessert', '🍰'),
('Sarapan', '🥞');

-- Data Awal: Menu
INSERT INTO menu (kategori_id, nama, deskripsi, harga, tersedia) VALUES
(1, 'Nasi Goreng Spesial', 'Nasi goreng dengan telur, ayam, dan sayuran segar, disajikan dengan kerupuk', 25000, 1),
(1, 'Mie Ayam Bakso', 'Mie ayam dengan bakso sapi pilihan, kuah gurih, dan topping lengkap', 22000, 1),
(1, 'Ayam Bakar Madu', 'Ayam kampung pilihan dibakar dengan marinasi madu dan rempah spesial', 35000, 1),
(1, 'Soto Betawi', 'Soto khas Betawi dengan kuah santan kaya, daging sapi, dan tomat segar', 28000, 1),
(1, 'Gado-Gado', 'Sayuran segar rebus dengan bumbu kacang spesial dan lontong', 20000, 1),
(1, 'Rendang Daging', 'Rendang sapi empuk dimasak dengan rempah Minang asli, disajikan dengan nasi', 40000, 1),
(2, 'Es Teh Manis', 'Teh manis segar dengan es batu pilihan', 8000, 1),
(2, 'Jus Alpukat', 'Jus alpukat segar dengan susu kental manis', 18000, 1),
(2, 'Es Jeruk', 'Jeruk segar diperas dengan es batu dan sedikit gula', 10000, 1),
(2, 'Kopi Susu', 'Kopi robusta pilihan dengan susu full cream', 15000, 1),
(3, 'Kentang Goreng', 'Kentang goreng crispy dengan saus tomat dan mayones', 15000, 1),
(3, 'Pisang Goreng', 'Pisang kepok goreng crispy dengan meses coklat', 12000, 1),
(3, 'Tahu Tempe Goreng', 'Tahu dan tempe goreng crispy bumbu kunyit', 10000, 1),
(4, 'Es Krim Vanilla', 'Es krim vanilla lembut dengan topping coklat', 15000, 1),
(4, 'Pudding Coklat', 'Pudding coklat lembut dengan saus vla vanilla', 12000, 1),
(5, 'Bubur Ayam', 'Bubur ayam dengan topping lengkap: cakwe, bawang goreng, kerupuk', 18000, 1),
(5, 'Nasi Uduk', 'Nasi uduk pulen dengan lauk lengkap dan sambal kacang', 20000, 1);
