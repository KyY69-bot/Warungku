<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    setFlash('error', 'Pesanan tidak ditemukan.');
    header('Location: pesanan.php');
    exit;
}

$details = $db->query("SELECT * FROM detail_pesanan WHERE pesanan_id = $id")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Detail Pesanan #' . $pesanan['kode_pesanan'];
require_once 'header-admin.php';
?>

<div style="margin-bottom:1.5rem">
    <a href="pesanan.php" class="btn btn-outline btn-sm">← Kembali ke Daftar Pesanan</a>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">
    <div>
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-title">🧾 Item Pesanan</div>
            <table>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Harga Satuan</th>
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
                        <td colspan="3" style="text-align:right;font-weight:600;color:var(--text-muted)">Ongkir</td>
                        <td style="text-align:right"><?= formatHarga(10000) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:700">TOTAL</td>
                        <td style="text-align:right;color:var(--primary);font-weight:800;font-size:1.1rem"><?= formatHarga($pesanan['total_harga']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="card">
            <div class="card-title">📍 Info Pelanggan</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                <div>
                    <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Nama</div>
                    <div style="font-weight:600"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></div>
                </div>
                <div>
                    <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Telepon</div>
                    <div><?= htmlspecialchars($pesanan['telepon']) ?></div>
                </div>
                <div style="grid-column:1/-1">
                    <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Alamat Pengiriman</div>
                    <div><?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?></div>
                </div>
                <?php if ($pesanan['catatan']): ?>
                <div style="grid-column:1/-1">
                    <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Catatan</div>
                    <div style="background:var(--bg);padding:12px;border-radius:8px;border-left:3px solid var(--accent)">
                        <?= htmlspecialchars($pesanan['catatan']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="card" style="position:sticky;top:90px">
        <div class="card-title">⚙️ Kelola Pesanan</div>
        
        <div style="margin-bottom:1.5rem">
            <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Kode Pesanan</div>
            <div style="font-size:1.2rem;font-weight:800;color:var(--primary)"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></div>
        </div>
        
        <div style="margin-bottom:1rem">
            <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Status Saat Ini</div>
            <span class="status-badge status-<?= $pesanan['status'] ?>"><?= ucfirst($pesanan['status']) ?></span>
        </div>
        
        <div style="margin-bottom:1rem">
            <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Pembayaran</div>
            <div><?= ucfirst($pesanan['metode_pembayaran']) ?></div>
        </div>
        
        <div style="margin-bottom:1.5rem">
            <div style="font-size:.78rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:.5rem">Waktu Pesanan</div>
            <div><?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?> WIB</div>
        </div>
        
        <div style="border-top:1px solid var(--border);padding-top:1.5rem">
            <div style="font-weight:600;margin-bottom:.75rem">Ubah Status:</div>
            <form method="POST" action="pesanan.php">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="pesanan_id" value="<?= $pesanan['id'] ?>">
                <div class="form-group">
                    <select name="status" style="font-size:1rem">
                        <?php
                        $opts = ['menunggu','diproses','dikirim','selesai','dibatalkan'];
                        foreach ($opts as $o):
                        ?>
                        <option value="<?= $o ?>" <?= $pesanan['status'] === $o ? 'selected' : '' ?>>
                            <?= ucfirst($o) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-block">✅ Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
