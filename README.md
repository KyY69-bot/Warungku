# 🍜 WarungKu - Sistem Pemesanan Makanan Online

Website pemesanan makanan berbasis **PHP Murni + MySQL** tanpa framework.

---

## 🚀 Cara Instalasi

### 1. Persyaratan
- PHP 7.4+ / 8.x
- MySQL 5.7+ / MariaDB 10+
- Web Server: Apache / Nginx / XAMPP / Laragon

### 2. Setup Database
```sql
-- Buka phpMyAdmin atau MySQL CLI, lalu jalankan:
SOURCE /path/to/foodorder/database.sql;
```
Atau import file `database.sql` melalui phpMyAdmin.

### 3. Konfigurasi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');     // Host database
define('DB_USER', 'root');          // Username MySQL
define('DB_PASS', '');              // Password MySQL
define('DB_NAME', 'food_ordering'); // Nama database
define('SITE_URL', 'http://localhost/foodorder'); // URL website
```

### 4. Letakkan di Web Server
- **XAMPP**: Taruh folder `foodorder` di `C:/xampp/htdocs/`
- **Laragon**: Taruh di `C:/laragon/www/`
- **Linux**: Taruh di `/var/www/html/`

### 5. Buka di Browser
```
http://localhost/foodorder/
```

---

## 📁 Struktur File
```
foodorder/
├── index.php              # Halaman utama (daftar menu)
├── keranjang.php           # Keranjang belanja
├── checkout.php            # Halaman checkout
├── detail-pesanan.php      # Detail & tracking pesanan
├── cek-pesanan.php         # Cek status pesanan
├── database.sql            # Schema + data awal database
├── includes/
│   ├── config.php          # Konfigurasi database & helper
│   ├── header.php          # Template header
│   └── footer.php          # Template footer
├── css/
│   └── style.css           # Stylesheet utama
├── js/
│   └── main.js             # JavaScript utama
├── admin/
│   ├── index.php           # Dashboard admin
│   ├── login.php           # Login admin
│   ├── logout.php          # Logout admin
│   ├── pesanan.php         # Kelola pesanan
│   ├── pesanan-detail.php  # Detail pesanan (admin)
│   ├── menu.php            # Kelola menu makanan
│   ├── kategori.php        # Kelola kategori
│   ├── header-admin.php    # Template header admin
│   └── footer-admin.php    # Template footer admin
└── uploads/
    └── menu/               # Upload gambar menu
```

---

## 🔑 Login Admin
- **URL**: `http://localhost/foodorder/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

> ⚠️ **PENTING**: Ubah kredensial admin di file `admin/login.php` sebelum deploy ke production!

---

## ✨ Fitur
### Pelanggan
- ✅ Lihat menu dengan kategori dan pencarian
- ✅ Tambah ke keranjang
- ✅ Checkout dengan data pengiriman
- ✅ Pilih metode pembayaran (COD/Transfer/E-Wallet)
- ✅ Tracking status pesanan

### Admin
- ✅ Dashboard dengan statistik
- ✅ Kelola pesanan & update status
- ✅ Kelola menu (CRUD)
- ✅ Kelola kategori (CRUD)
- ✅ Toggle ketersediaan menu

---

## 🗃️ Database Schema
- `kategori` - Kategori menu
- `menu` - Data menu makanan
- `pelanggan` - Data pelanggan
- `pesanan` - Data pesanan
- `detail_pesanan` - Item dalam setiap pesanan

---

## 💡 Tips Development
1. Aktifkan error reporting di development:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Untuk keamanan production, gunakan password hashing untuk admin
3. Tambahkan validasi CSRF token untuk form
4. Gunakan prepared statements (sudah diimplementasikan)

---

Dibuat dengan ❤️ menggunakan **PHP Murni + MySQL** | Tanpa framework
