<?php
require_once 'includes/config.php';

$db = getDB();
$kode = clean($_GET['kode'] ?? '');

if (!$kode) {
    header('Location: cek-pesanan.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM pesanan WHERE kode_pesanan = ?");
$stmt->bind_param('s', $kode);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: cek-pesanan.php');
    exit;
}

// Ambil detail pesanan
$stmt2 = $db->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ?");
$stmt2->bind_param('i', $pesanan['id']);
$stmt2->execute();
$details = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$statusLabel = [
    'menunggu'   => ['label' => 'Menunggu Konfirmasi', 'icon' => '⏳'],
    'diproses'   => ['label' => 'Sedang Diproses', 'icon' => '👨‍🍳'],
    'dikirim'    => ['label' => 'Sedang Dikirim', 'icon' => '🚴'],
    'selesai'    => ['label' => 'Pesanan Selesai', 'icon' => '✅'],
    'dibatalkan' => ['label' => 'Dibatalkan', 'icon' => '❌'],
];

$pageTitle = 'Detail Pesanan ' . $kode;
require_once 'includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width:800px">
        <div class="order-header">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem">
                <div>
                    <h2>Pesanan #<?= htmlspecialchars($pesanan['kode_pesanan']) ?></h2>
                    <div class="order-meta">
                        <div class="order-meta-item">
                            <strong>Tanggal</strong>
                            <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?> WIB
                        </div>
                        <div class="order-meta-item">
                            <strong>Pembayaran</strong>
                            <?= ucfirst($pesanan['metode_pembayaran']) ?>
                        </div>
                        <div class="order-meta-item">
                            <strong>Total</strong>
                            <?= formatHarga($pesanan['total_harga']) ?>
                        </div>
                    </div>
                </div>
                <div style="text-align:right">
                    <span class="status-badge status-<?= $pesanan['status'] ?>">
                        <?= $statusLabel[$pesanan['status']]['icon'] ?>
                        <?= $statusLabel[$pesanan['status']]['label'] ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Progress Steps -->
        <?php
        $steps = ['menunggu' => 0, 'diproses' => 1, 'dikirim' => 2, 'selesai' => 3];
        $currentStep = $steps[$pesanan['status']] ?? 0;
        if ($pesanan['status'] !== 'dibatalkan'):
        ?>
        <div class="card" style="margin-bottom:1.5rem">
            <div style="display:flex;justify-content:space-between;position:relative;padding:1rem 0">
                <div style="position:absolute;top:50%;left:0;right:0;height:3px;background:var(--border);z-index:0;transform:translateY(-50%)">
                    <div style="height:100%;background:var(--primary);width:<?= min(100, $currentStep * 33.3) ?>%;transition:width .5s"></div>
                </div>
                <?php
                $stepInfo = [
                    ['icon' => '📝', 'label' => 'Diterima'],
                    ['icon' => '👨‍🍳', 'label' => 'Diproses'],
                    ['icon' => '🚴', 'label' => 'Dikirim'],
                    ['icon' => '✅', 'label' => 'Selesai'],
                ];
                foreach ($stepInfo as $i => $step):
                    $done = $i <= $currentStep;
                ?>
                <div style="text-align:center;z-index:1;flex:1">
                    <div style="width:40px;height:40px;border-radius:50%;background:<?= $done ? 'var(--primary)' : 'white' ?>;border:3px solid <?= $done ? 'var(--primary)' : 'var(--border)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;font-size:1.1rem">
                        <?= $done ? $step['icon'] : '' ?>
                    </div>
                    <div style="font-size:.75rem;font-weight:600;color:<?= $done ? 'var(--primary)' : 'var(--text-muted)' ?>">
                        <?= $step['label'] ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Detail Item -->
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-title">🧾 Detail Pesanan</div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th style="text-align:center">Qty</th>
                            <th style="text-align:right">Harga</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $d): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($d['nama_menu']) ?></strong></td>
                            <td style="text-align:center"><?= $d['jumlah'] ?></td>
                            <td style="text-align:right"><?= formatHarga($d['harga_satuan']) ?></td>
                            <td style="text-align:right"><strong><?= formatHarga($d['subtotal']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:600;color:var(--text-muted)">Subtotal</td>
                            <td style="text-align:right"><?= formatHarga($pesanan['total_harga'] - 10000) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:600;color:var(--text-muted)">Ongkos Kirim</td>
                            <td style="text-align:right"><?= formatHarga(10000) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:700;font-size:1rem">Total Bayar</td>
                            <td style="text-align:right;color:var(--primary);font-weight:800;font-size:1.1rem"><?= formatHarga($pesanan['total_harga']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Info Pengiriman -->
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-title">📍 Info Pengiriman</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                <div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">Pemesan</div>
                    <div style="font-weight:600"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></div>
                    <div style="color:var(--text-muted)"><?= htmlspecialchars($pesanan['telepon']) ?></div>
                </div>
                <div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">Alamat</div>
                    <div><?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?></div>
                </div>
                <?php if ($pesanan['catatan']): ?>
                <div style="grid-column:1/-1">
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">Catatan</div>
                    <div style="background:var(--bg);padding:12px;border-radius:8px;border-left:3px solid var(--accent)">
                        <?= htmlspecialchars($pesanan['catatan']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="display:flex;gap:1rem;flex-wrap:wrap">
            <a href="index.php" class="btn btn-primary">🍽️ Pesan Lagi</a>
            <a href="cek-pesanan.php" class="btn btn-outline">📦 Cek Pesanan Lain</a>
            <button onclick="printOrder()" class="btn btn-dark">🖨️ Print</button>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
