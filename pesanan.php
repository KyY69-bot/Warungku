<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$db = getDB();

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id     = (int)$_POST['pesanan_id'];
    $status = $_POST['status'];
    $validStatus = ['menunggu','diproses','dikirim','selesai','dibatalkan'];
    if (in_array($status, $validStatus)) {
        $stmt = $db->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        setFlash('success', '✅ Status pesanan berhasil diperbarui.');
    }
    header('Location: pesanan.php');
    exit;
}

// Filter
$filterStatus = $_GET['status'] ?? '';
$sql = "SELECT * FROM pesanan WHERE 1=1";
if ($filterStatus) {
    $fs = $db->real_escape_string($filterStatus);
    $sql .= " AND status = '$fs'";
}
$sql .= " ORDER BY created_at DESC";
$pesanans = $db->query($sql)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Kelola Pesanan';
require_once 'header-admin.php';
?>

<!-- Filter Tabs -->
<div style="display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <?php
    $statuses = ['' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
    foreach ($statuses as $s => $label):
    ?>
    <a href="pesanan.php<?= $s ? '?status='.$s : '' ?>" 
       class="btn btn-sm <?= $filterStatus === $s ? 'btn-primary' : 'btn-outline' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pemesan</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesanans)): ?>
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Tidak ada pesanan</td></tr>
                <?php else: ?>
                <?php foreach ($pesanans as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['kode_pesanan']) ?></strong></td>
                    <td>
                        <div style="font-weight:600"><?= htmlspecialchars($p['nama_pelanggan']) ?></div>
                        <div style="font-size:.8rem;color:var(--text-muted)"><?= htmlspecialchars($p['telepon']) ?></div>
                    </td>
                    <td><strong><?= formatHarga($p['total_harga']) ?></strong></td>
                    <td><?= ucfirst($p['metode_pembayaran']) ?></td>
                    <td style="font-size:.82rem;color:var(--text-muted)"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                    <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td style="display:flex;gap:.5rem;flex-wrap:wrap">
                        <a href="pesanan-detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-dark">Detail</a>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                            <select name="status" onchange="this.form.submit()" 
                                    style="padding:6px 10px;border:1px solid var(--border);border-radius:8px;font-family:Sora,sans-serif;font-size:.8rem;cursor:pointer">
                                <?php
                                $opts = ['menunggu','diproses','dikirim','selesai','dibatalkan'];
                                foreach ($opts as $o):
                                ?>
                                <option value="<?= $o ?>" <?= $p['status'] === $o ? 'selected' : '' ?>>
                                    <?= ucfirst($o) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
