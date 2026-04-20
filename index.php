<?php
require_once '../includes/config.php';

// Simple admin auth check
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();

// Stats
$totalPesanan = $db->query("SELECT COUNT(*) FROM pesanan")->fetch_row()[0];
$totalPendapatan = $db->query("SELECT SUM(total_harga) FROM pesanan WHERE status = 'selesai'")->fetch_row()[0] ?? 0;
$totalMenu = $db->query("SELECT COUNT(*) FROM menu WHERE tersedia = 1")->fetch_row()[0];
$pesananBaru = $db->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu'")->fetch_row()[0];

// Pesanan terbaru
$pesananTerbaru = $db->query(
    "SELECT * FROM pesanan ORDER BY created_at DESC LIMIT 10"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Dashboard Admin';
require_once 'header-admin.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">📦</div>
        <div class="stat-info">
            <h3>Total Pesanan</h3>
            <p><?= number_format($totalPesanan) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">💰</div>
        <div class="stat-info">
            <h3>Total Pendapatan</h3>
            <p style="font-size:1.2rem"><?= formatHarga($totalPendapatan) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">🍽️</div>
        <div class="stat-info">
            <h3>Menu Aktif</h3>
            <p><?= $totalMenu ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">⏳</div>
        <div class="stat-info">
            <h3>Pesanan Baru</h3>
            <p><?= $pesananBaru ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-title">Pesanan Terbaru</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesananTerbaru)): ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">Belum ada pesanan</td></tr>
                <?php else: ?>
                <?php foreach ($pesananTerbaru as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['kode_pesanan']) ?></strong></td>
                    <td>
                        <div><?= htmlspecialchars($p['nama_pelanggan']) ?></div>
                        <div style="font-size:.8rem;color:var(--text-muted)"><?= htmlspecialchars($p['telepon']) ?></div>
                    </td>
                    <td><strong><?= formatHarga($p['total_harga']) ?></strong></td>
                    <td><?= ucfirst($p['metode_pembayaran']) ?></td>
                    <td style="font-size:.82rem;color:var(--text-muted)"><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
                    <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td>
                        <a href="pesanan-detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-dark">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">
        <a href="pesanan.php" class="btn btn-outline btn-sm">Lihat Semua Pesanan →</a>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
