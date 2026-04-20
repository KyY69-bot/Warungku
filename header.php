<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= $pageTitle ?? 'Pesan Makanan Online' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>
<header class="header">
    <div class="container">
        <a href="<?= SITE_URL ?>/index.php" class="logo">
            <span class="logo-icon">🍜</span>
            <span class="logo-text"><?= SITE_NAME ?></span>
        </a>
        <nav class="nav">
            <a href="<?= SITE_URL ?>/index.php">Menu</a>
            <a href="<?= SITE_URL ?>/cek-pesanan.php">Cek Pesanan</a>
            <a href="<?= SITE_URL ?>/admin/" class="btn-admin">Admin</a>
        </nav>
        <a href="<?= SITE_URL ?>/keranjang.php" class="cart-btn">
            🛒 Keranjang
            <?php
            $cartCount = 0;
            if (isset($_SESSION['keranjang'])) {
                foreach ($_SESSION['keranjang'] as $item) $cartCount += $item['jumlah'];
            }
            if ($cartCount > 0): ?>
                <span class="cart-badge"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>

<?php
$flash = getFlash();
if ($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>">
    <?= htmlspecialchars($flash['message']) ?>
    <button onclick="this.parentElement.remove()">✕</button>
</div>
<?php endif; ?>
