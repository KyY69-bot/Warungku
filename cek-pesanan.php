<?php
$pageTitle = 'Cek Pesanan';
require_once 'includes/header.php';

$db = getDB();
$pesanan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = clean($_POST['kode'] ?? '');
    if ($kode) {
        $stmt = $db->prepare("SELECT * FROM pesanan WHERE kode_pesanan = ?");
        $stmt->bind_param('s', $kode);
        $stmt->execute();
        $pesanan = $stmt->get_result()->fetch_assoc();
        if (!$pesanan) {
            setFlash('error', 'Pesanan dengan kode "' . htmlspecialchars($kode) . '" tidak ditemukan.');
        }
    }
}
?>

<section class="section">
    <div class="container" style="max-width:640px">
        <div style="text-align:center;margin-bottom:3rem">
            <div style="font-size:3.5rem;margin-bottom:1rem">📦</div>
            <h1 class="section-title">Cek Status Pesanan</h1>
            <p style="color:var(--text-muted)">Masukkan kode pesanan Anda untuk melihat status terkini</p>
        </div>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Kode Pesanan</label>
                    <input type="text" name="kode" 
                           value="<?= htmlspecialchars($_POST['kode'] ?? '') ?>"
                           placeholder="Contoh: WK-A1B2C3D4" 
                           style="text-transform:uppercase;font-weight:700;font-size:1.1rem;letter-spacing:2px"
                           required>
                    <div style="font-size:.78rem;color:var(--text-muted);margin-top:.4rem">
                        Kode pesanan dikirimkan saat Anda berhasil checkout
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">🔍 Cek Pesanan</button>
            </form>
        </div>
        
        <?php if ($pesanan): ?>
        <div class="card" style="margin-top:1.5rem">
            <?php
            $statusConfig = [
                'menunggu'   => ['icon' => '⏳', 'color' => '#856404', 'bg' => '#FFF3CD', 'label' => 'Menunggu Konfirmasi'],
                'diproses'   => ['icon' => '👨‍🍳', 'color' => '#004085', 'bg' => '#CCE5FF', 'label' => 'Sedang Diproses'],
                'dikirim'    => ['icon' => '🚴', 'color' => '#0C5460', 'bg' => '#D1ECF1', 'label' => 'Sedang Dikirim'],
                'selesai'    => ['icon' => '✅', 'color' => '#155724', 'bg' => '#D4EDDA', 'label' => 'Pesanan Selesai'],
                'dibatalkan' => ['icon' => '❌', 'color' => '#721C24', 'bg' => '#F8D7DA', 'label' => 'Dibatalkan'],
            ];
            $sc = $statusConfig[$pesanan['status']];
            ?>
            <div style="text-align:center;padding:1rem 0 1.5rem">
                <div style="font-size:3rem;margin-bottom:.5rem"><?= $sc['icon'] ?></div>
                <div style="display:inline-block;background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;padding:8px 24px;border-radius:999px;font-weight:700;font-size:.95rem;margin-bottom:1rem">
                    <?= $sc['label'] ?>
                </div>
                <h3 style="font-size:1.2rem;margin-bottom:.25rem">
                    Pesanan #<?= htmlspecialchars($pesanan['kode_pesanan']) ?>
                </h3>
                <p style="color:var(--text-muted);font-size:.85rem">
                    <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?> WIB
                </p>
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:1rem 0;border-top:1px solid var(--border)">
                <div>
                    <div style="font-size:.75rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.25rem">Pemesan</div>
                    <div style="font-weight:600"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></div>
                </div>
                <div>
                    <div style="font-size:.75rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.25rem">Total</div>
                    <div style="font-weight:700;color:var(--primary);font-size:1.1rem"><?= formatHarga($pesanan['total_harga']) ?></div>
                </div>
            </div>
            
            <div style="margin-top:1rem">
                <a href="detail-pesanan.php?kode=<?= urlencode($pesanan['kode_pesanan']) ?>" class="btn btn-primary btn-block">
                    Lihat Detail Lengkap →
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
