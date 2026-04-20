<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$db = getDB();

// Tambah menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'tambah') {
    $nama        = clean($_POST['nama'] ?? '');
    $deskripsi   = clean($_POST['deskripsi'] ?? '');
    $harga       = (float)($_POST['harga'] ?? 0);
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $tersedia    = isset($_POST['tersedia']) ? 1 : 0;

    if ($nama && $harga > 0) {
        $stmt = $db->prepare("INSERT INTO menu (nama, deskripsi, harga, kategori_id, tersedia) VALUES (?,?,?,?,?)");
        $stmt->bind_param('ssdii', $nama, $deskripsi, $harga, $kategori_id, $tersedia);
        $stmt->execute();
        setFlash('success', "✅ Menu \"$nama\" berhasil ditambahkan.");
    } else {
        setFlash('error', '❌ Nama dan harga wajib diisi.');
    }
    header('Location: menu.php');
    exit;
}

// Edit menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id          = (int)$_POST['id'];
    $nama        = clean($_POST['nama'] ?? '');
    $deskripsi   = clean($_POST['deskripsi'] ?? '');
    $harga       = (float)($_POST['harga'] ?? 0);
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $tersedia    = isset($_POST['tersedia']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE menu SET nama=?,deskripsi=?,harga=?,kategori_id=?,tersedia=? WHERE id=?");
    $stmt->bind_param('ssdiid', $nama, $deskripsi, $harga, $kategori_id, $tersedia, $id);  
    // Fix: correct types
    $stmt = $db->prepare("UPDATE menu SET nama=?,deskripsi=?,harga=?,kategori_id=?,tersedia=? WHERE id=?");
    $stmt->bind_param('ssddii', $nama, $deskripsi, $harga, $kategori_id, $tersedia, $id);
    $stmt->execute();
    setFlash('success', '✅ Menu berhasil diperbarui.');
    header('Location: menu.php');
    exit;
}

// Hapus menu
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $db->query("DELETE FROM menu WHERE id = $id");
    setFlash('success', '🗑️ Menu berhasil dihapus.');
    header('Location: menu.php');
    exit;
}

// Toggle tersedia
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->query("UPDATE menu SET tersedia = NOT tersedia WHERE id = $id");
    header('Location: menu.php');
    exit;
}

$menus     = $db->query("SELECT m.*, k.nama AS nama_kategori FROM menu m LEFT JOIN kategori k ON m.kategori_id = k.id ORDER BY k.nama, m.nama")->fetch_all(MYSQLI_ASSOC);
$kategoris = $db->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);

// Edit mode
$editMenu = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $editMenu = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Kelola Menu';
require_once 'header-admin.php';
?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">
    <!-- Daftar Menu -->
    <div class="card">
        <div class="card-title">Daftar Menu (<?= count($menus) ?>)</div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th style="text-align:right">Harga</th>
                        <th style="text-align:center">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($m['nama']) ?></strong>
                            <?php if ($m['deskripsi']): ?>
                            <div style="font-size:.78rem;color:var(--text-muted);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                <?= htmlspecialchars($m['deskripsi']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m['nama_kategori'] ?? '-') ?></td>
                        <td style="text-align:right;font-weight:600;color:var(--primary)"><?= formatHarga($m['harga']) ?></td>
                        <td style="text-align:center">
                            <a href="menu.php?toggle=<?= $m['id'] ?>" 
                               style="display:inline-block;padding:4px 12px;border-radius:999px;font-size:.75rem;font-weight:700;<?= $m['tersedia'] ? 'background:#D4EDDA;color:#155724' : 'background:#F8D7DA;color:#721C24' ?>">
                                <?= $m['tersedia'] ? '✅ Tersedia' : '❌ Habis' ?>
                            </a>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <a href="menu.php?edit=<?= $m['id'] ?>" class="btn btn-sm btn-dark">Edit</a>
                                <a href="menu.php?hapus=<?= $m['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirmDelete('Hapus menu <?= htmlspecialchars(addslashes($m['nama'])) ?>?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Form Tambah/Edit -->
    <div class="card" style="position:sticky;top:90px">
        <div class="card-title"><?= $editMenu ? '✏️ Edit Menu' : '➕ Tambah Menu Baru' ?></div>
        
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editMenu ? 'edit' : 'tambah' ?>">
            <?php if ($editMenu): ?>
            <input type="hidden" name="id" value="<?= $editMenu['id'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nama Menu *</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($editMenu['nama'] ?? '') ?>" required placeholder="Nama menu">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoris as $kat): ?>
                    <option value="<?= $kat['id'] ?>" <?= ($editMenu['kategori_id'] ?? '') == $kat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Harga (Rp) *</label>
                <input type="number" name="harga" value="<?= $editMenu['harga'] ?? '' ?>" required min="0" step="500" placeholder="25000">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" placeholder="Deskripsi singkat menu..."><?= htmlspecialchars($editMenu['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="tersedia" id="tersedia" style="width:auto" 
                       <?= ($editMenu['tersedia'] ?? 1) ? 'checked' : '' ?>>
                <label for="tersedia" style="margin:0;cursor:pointer">Menu tersedia / aktif</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <?= $editMenu ? '💾 Simpan Perubahan' : '➕ Tambah Menu' ?>
            </button>
            <?php if ($editMenu): ?>
            <a href="menu.php" class="btn btn-outline btn-block" style="margin-top:.75rem">Batal Edit</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
