<?php
// =============================================
// KONFIGURASI DATABASE
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Ganti dengan username MySQL Anda
define('DB_PASS', '');          // Ganti dengan password MySQL Anda
define('DB_NAME', 'food_ordering');

define('SITE_NAME', 'WarungKu');
define('SITE_URL', 'http://localhost/foodorder');
define('CURRENCY', 'Rp');

// Koneksi ke database
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
                <h2>❌ Koneksi Database Gagal</h2>
                <p>' . $conn->connect_error . '</p>
                <p>Pastikan MySQL aktif dan konfigurasi di <code>includes/config.php</code> sudah benar.</p>
            </div>');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// Format harga ke Rupiah
function formatHarga($harga) {
    return CURRENCY . ' ' . number_format($harga, 0, ',', '.');
}

// Generate kode pesanan unik
function generateKodePesanan() {
    return 'WK-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Sanitasi input
function clean($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

session_start();
