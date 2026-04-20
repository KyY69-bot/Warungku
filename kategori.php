<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'tambah') {
        $nama = clean($_POST['nama'] ?? '');
        $ikon = clean($_POST['ikon'] ?? '🍽️');
        if ($nama) {
            $stmt = $db->prepare("INSERT INTO kategori (nama, ikon) VALUES (?,?)");
            $stmt->bind_param('ss', $nama, $ikon);
            $stmt->execute();
            setFlash('success', "✅ Kategori \"$nama\" ditambahkan.");
        }
    } elseif ($_POST['action'] === 'edit') {
        $id   = (int)$_POST['id'];
        $nama = clean($_POST['nama'] ?? '');
        $ikon = clean($_POST['ikon'] ?? '🍽️');
        $stmt = $db->prepare("UPDATE kategori SET nama=?,ikon=? WHERE id=?");
        $stmt->bind_param('ssi', $nama, $ikon, $id);
        $stmt->execute();
        setFlash('success', '✅ Kategori diperbarui.');
    }
    header('Location: kategori.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $db->query("DELETE FROM kategori WHERE id = $id");
    setFlash('success', '🗑️ Kategori dihapus.');
    header('Location: kategori.php');
    exit;
}

$kategoris = $db->query("SELECT k.*, COUNT(m.id) AS jumlah_menu FROM kategori k LEFT JOIN menu m ON k.id = m.kategori_id GROUP BY k.id ORDER BY k.nama")->fetch_all(MYSQLI_ASSOC);

$editKat = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM kategori WHERE id=?");
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $editKat = $stmt->get_result()->fetch_assoc();
}

$pageTitle = 'Kategori Menu';
require_once 'header-admin.php';
?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">
    <div class="card">
        <div class="card-title">Daftar Kategori</div>
        <table>
            <thead>
                <tr>
                    <th>Ikon</th>
                    <th>Nama Kategori</th>
                    <th style="text-align:center">Jumlah Menu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoris as $k): ?>
                <tr>
                    <td style="font-size:1.5rem"><?= htmlspecialchars($k['ikon']) ?></td>
                    <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
                    <td style="text-align:center"><?= $k['jumlah_menu'] ?> menu</td>
                    <td>
                        <div style="display:flex;gap:.5rem">
                            <a href="kategori.php?edit=<?= $k['id'] ?>" class="btn btn-sm btn-dark">Edit</a>
                            <a href="kategori.php?hapus=<?= $k['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirmDelete('Hapus kategori ini? Semua menu terkait akan kehilangan kategorinya.')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card" style="position:sticky;top:90px">
        <div class="card-title"><?= $editKat ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?></div>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editKat ? 'edit' : 'tambah' ?>">
            <?php if ($editKat): ?>
            <input type="hidden" name="id" value="<?= $editKat['id'] ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Ikon (Emoji)</label>
                <input type="text" name="ikon" value="<?= htmlspecialchars($editKat['ikon'] ?? '🍽️') ?>" placeholder="🍽️" style="font-size:1.5rem">
            </div>
            <div class="form-group">
                <label>Nama Kategori *</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($editKat['nama'] ?? '') ?>" required placeholder="Nama kategori">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <?= $editKat ? '💾 Simpan' : '➕ Tambah' ?>
            </button>
            <?php if ($editKat): ?>
            <a href="kategori.php" class="btn btn-outline btn-block" style="margin-top:.75rem">Batal</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once 'footer-admin.php'; ?>
