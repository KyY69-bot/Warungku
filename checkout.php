<?php
require_once 'includes/config.php';

$db = getDB();
$keranjang = $_SESSION['keranjang'] ?? [];

// Redirect jika keranjang kosong
if (empty($keranjang)) {
    setFlash('error', 'Keranjang Anda kosong. Silakan pilih menu terlebih dahulu.');
    header('Location: index.php');
    exit;
}

$total_harga = 0;
foreach ($keranjang as $item) {
    $total_harga += $item['harga'] * $item['jumlah'];
}
$ongkir    = 10000;
$grand_total = $total_harga + $ongkir;

// ===== PROSES CHECKOUT =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = clean($_POST['nama'] ?? '');
    $telepon   = clean($_POST['telepon'] ?? '');
    $alamat    = clean($_POST['alamat'] ?? '');
    $catatan   = clean($_POST['catatan'] ?? '');
    $metode    = in_array($_POST['metode'] ?? '', ['tunai','transfer','ewallet']) ? $_POST['metode'] : 'tunai';

    $errors = [];
    if (!$nama)    $errors[] = 'Nama wajib diisi.';
    if (!$telepon) $errors[] = 'Nomor telepon wajib diisi.';
    if (!$alamat)  $errors[] = 'Alamat pengiriman wajib diisi.';

    if (empty($errors)) {
        $kode = generateKodePesanan();

        // Simpan pesanan
        $stmt = $db->prepare(
            "INSERT INTO pesanan (kode_pesanan, nama_pelanggan, telepon, alamat_pengiriman, catatan, total_harga, metode_pembayaran) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssssds', $kode, $nama, $telepon, $alamat, $catatan, $grand_total, $metode);
        $stmt->execute();
        $pesanan_id = $db->insert_id;

        // Simpan detail pesanan
        foreach ($keranjang as $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $stmt2    = $db->prepare(
                "INSERT INTO detail_pesanan (pesanan_id, menu_id, nama_menu, harga_satuan, jumlah, subtotal) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt2->bind_param('iisdid', $pesanan_id, $item['menu_id'], $item['nama'], $item['harga'], $item['jumlah'], $subtotal);
            $stmt2->execute();
        }

        // Kosongkan keranjang
        unset($_SESSION['keranjang']);

        setFlash('success', "✅ Pesanan berhasil! Kode pesanan Anda: $kode");
        header("Location: detail-pesanan.php?kode=$kode");
        exit;
    }
}

$pageTitle = 'Checkout';
require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section-title" style="margin-bottom:.5rem">📦 Checkout</h1>
        <p style="color:var(--text-muted);margin-bottom:2rem">Lengkapi data pengiriman Anda</p>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?><div>⚠️ <?= $e ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
        <div class="page-layout">
            <!-- Kiri: Form Data -->
            <div>
                <div class="card" style="margin-bottom:1.5rem">
                    <div class="card-title">👤 Data Pemesan</div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" 
                               placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor WhatsApp/Telepon *</label>
                        <input type="tel" name="telepon" value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>" 
                               placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Pengiriman *</label>
                        <textarea name="alamat" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kota..." required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Catatan (opsional)</label>
                        <textarea name="catatan" placeholder="Contoh: Tidak pakai pedas, tidak pakai bawang..."><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">💳 Metode Pembayaran</div>
                    <div class="payment-options">
                        <label class="payment-option <?= ($_POST['metode'] ?? 'tunai') === 'tunai' ? 'selected' : '' ?>">
                            <input type="radio" name="metode" value="tunai" <?= ($_POST['metode'] ?? 'tunai') === 'tunai' ? 'checked' : '' ?>>
                            <span class="icon">💵</span>
                            <div>
                                <div class="label">Tunai (COD)</div>
                                <div class="desc">Bayar saat pesanan tiba</div>
                            </div>
                        </label>
                        <label class="payment-option <?= ($_POST['metode'] ?? '') === 'transfer' ? 'selected' : '' ?>">
                            <input type="radio" name="metode" value="transfer" <?= ($_POST['metode'] ?? '') === 'transfer' ? 'checked' : '' ?>>
                            <span class="icon">🏦</span>
                            <div>
                                <div class="label">Transfer Bank</div>
                                <div class="desc">BCA, BRI, Mandiri, BNI</div>
                            </div>
                        </label>
                        <label class="payment-option <?= ($_POST['metode'] ?? '') === 'ewallet' ? 'selected' : '' ?>">
                            <input type="radio" name="metode" value="ewallet" <?= ($_POST['metode'] ?? '') === 'ewallet' ? 'checked' : '' ?>>
                            <span class="icon">📱</span>
                            <div>
                                <div class="label">E-Wallet</div>
                                <div class="desc">GoPay, OVO, DANA, ShopeePay</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Kanan: Summary -->
            <div>
                <div class="card" style="position:sticky;top:90px">
                    <div class="card-title">🧾 Ringkasan Pesanan</div>
                    
                    <?php foreach ($keranjang as $item): ?>
                    <div class="summary-row">
                        <span><?= htmlspecialchars($item['nama']) ?> × <?= $item['jumlah'] ?></span>
                        <span><?= formatHarga($item['harga'] * $item['jumlah']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="summary-row" style="border-top:1px solid var(--border);margin-top:.5rem;padding-top:.75rem">
                        <span>Subtotal</span>
                        <span><?= formatHarga($total_harga) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span><?= formatHarga($ongkir) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total Bayar</span>
                        <span class="price"><?= formatHarga($grand_total) ?></span>
                    </div>
                    
                    <div style="margin-top:1.5rem">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            ✅ Buat Pesanan
                        </button>
                    </div>
                    <div style="margin-top:1rem">
                        <a href="keranjang.php" class="btn btn-outline btn-block">← Kembali ke Keranjang</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
