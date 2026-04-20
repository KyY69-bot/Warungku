<?php
require_once 'includes/config.php';

$db = getDB();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ===== AKSI KERANJANG =====
if ($action === 'tambah' && isset($_POST['menu_id'])) {
    $menu_id = (int)$_POST['menu_id'];
    $jumlah  = max(1, (int)($_POST['jumlah'] ?? 1));
    
    $stmt = $db->prepare("SELECT * FROM menu WHERE id = ? AND tersedia = 1");
    $stmt->bind_param('i', $menu_id);
    $stmt->execute();
    $menu = $stmt->get_result()->fetch_assoc();
    
    if ($menu) {
        if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];
        if (isset($_SESSION['keranjang'][$menu_id])) {
            $_SESSION['keranjang'][$menu_id]['jumlah'] += $jumlah;
        } else {
            $_SESSION['keranjang'][$menu_id] = [
                'menu_id' => $menu['id'],
                'nama'    => $menu['nama'],
                'harga'   => $menu['harga'],
                'jumlah'  => $jumlah,
            ];
        }
        setFlash('success', "✅ {$menu['nama']} ditambahkan ke keranjang!");
    } else {
        setFlash('error', '❌ Menu tidak tersedia.');
    }
    header('Location: keranjang.php');
    exit;
}

if ($action === 'update' && isset($_POST['menu_id'])) {
    $menu_id = (int)$_POST['menu_id'];
    $jumlah  = (int)$_POST['jumlah'];
    if ($jumlah > 0 && isset($_SESSION['keranjang'][$menu_id])) {
        $_SESSION['keranjang'][$menu_id]['jumlah'] = $jumlah;
    }
    header('Location: keranjang.php');
    exit;
}

if ($action === 'hapus' && isset($_GET['id'])) {
    $menu_id = (int)$_GET['id'];
    if (isset($_SESSION['keranjang'][$menu_id])) {
        $nama = $_SESSION['keranjang'][$menu_id]['nama'];
        unset($_SESSION['keranjang'][$menu_id]);
        setFlash('info', "🗑️ $nama dihapus dari keranjang.");
    }
    header('Location: keranjang.php');
    exit;
}

if ($action === 'kosongkan') {
    unset($_SESSION['keranjang']);
    setFlash('info', '🗑️ Keranjang dikosongkan.');
    header('Location: keranjang.php');
    exit;
}

// Hitung total
$keranjang   = $_SESSION['keranjang'] ?? [];
$total_harga = 0;
$total_item  = 0;
foreach ($keranjang as $item) {
    $total_harga += $item['harga'] * $item['jumlah'];
    $total_item  += $item['jumlah'];
}
$ongkir  = $total_harga > 0 ? 10000 : 0;
$grand_total = $total_harga + $ongkir;

$pageTitle = 'Keranjang';
require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section-title" style="margin-bottom:2rem">🛒 Keranjang Belanja</h1>
        
        <?php if (empty($keranjang)): ?>
        <div class="empty-state">
            <div class="icon">🛒</div>
            <h3>Keranjang Masih Kosong</h3>
            <p>Yuk, pilih menu lezat yang kamu inginkan!</p>
            <a href="index.php" class="btn btn-primary btn-lg">🍽️ Lihat Menu</a>
        </div>
        
        <?php else: ?>
        <div class="page-layout">
            <!-- Kiri: Items -->
            <div>
                <div class="card">
                    <div class="card-title">
                        Item Pesanan
                        <a href="keranjang.php?action=kosongkan" 
                           onclick="return confirm('Kosongkan semua keranjang?')"
                           style="float:right;font-size:.8rem;color:var(--danger);font-family:Sora,sans-serif">
                            🗑️ Kosongkan
                        </a>
                    </div>
                    
                    <?php foreach ($keranjang as $menu_id => $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-icon">🍽️</div>
                        <div class="cart-item-info">
                            <h4><?= htmlspecialchars($item['nama']) ?></h4>
                            <div class="price"><?= formatHarga($item['harga']) ?>/porsi</div>
                        </div>
                        <form method="POST" action="keranjang.php" style="display:flex;align-items:center;gap:8px">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="menu_id" value="<?= $menu_id ?>">
                            <div class="qty-control">
                                <button type="button" class="qty-minus">−</button>
                                <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" 
                                       min="1" max="50" class="cart-qty-input">
                                <button type="button" class="qty-plus">+</button>
                            </div>
                        </form>
                        <div class="price"><?= formatHarga($item['harga'] * $item['jumlah']) ?></div>
                        <a href="keranjang.php?action=hapus&id=<?= $menu_id ?>" class="btn-remove" title="Hapus">✕</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top:1rem">
                    <a href="index.php" class="btn btn-outline">← Tambah Menu Lagi</a>
                </div>
            </div>
            
            <!-- Kanan: Summary & Checkout -->
            <div>
                <div class="card">
                    <div class="card-title">Ringkasan Pesanan</div>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?= $total_item ?> item)</span>
                        <span><?= formatHarga($total_harga) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span><?= formatHarga($ongkir) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="price"><?= formatHarga($grand_total) ?></span>
                    </div>
                    
                    <div style="margin-top:1.5rem">
                        <a href="checkout.php" class="btn btn-primary btn-block btn-lg">
                            Lanjut Checkout →
                        </a>
                    </div>
                </div>
                
                <div class="card" style="margin-top:1rem">
                    <div style="display:flex;gap:10px;align-items:center;font-size:.85rem;color:var(--text-muted)">
                        <span style="font-size:1.5rem">🔒</span>
                        <span>Pembayaran aman. Pesanan dikonfirmasi setelah pembayaran diterima.</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
