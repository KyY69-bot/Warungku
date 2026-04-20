<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> Admin - <?= $pageTitle ?? 'Dashboard' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>
<header class="header">
    <div class="container">
        <a href="<?= SITE_URL ?>/admin/" class="logo">
            <span class="logo-icon">🍜</span>
            <span class="logo-text"><?= SITE_NAME ?> <span style="font-size:.8rem;opacity:.7">Admin</span></span>
        </a>
        <nav class="nav">
            <a href="<?= SITE_URL ?>/index.php" target="_blank">Lihat Toko</a>
            <span style="color:var(--text-muted);font-size:.85rem">👤 <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
            <a href="logout.php" style="color:var(--danger)">Keluar</a>
        </nav>
    </div>
</header>

<?php $flash = getFlash(); if ($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>">
    <?= htmlspecialchars($flash['message']) ?>
    <button onclick="this.parentElement.remove()">✕</button>
</div>
<?php endif; ?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>Menu Admin</h3>
        <nav class="admin-nav">
            <?php
            $current = basename($_SERVER['PHP_SELF']);
            $links = [
                ['href' => 'index.php',    'icon' => '📊', 'label' => 'Dashboard'],
                ['href' => 'pesanan.php',  'icon' => '📦', 'label' => 'Kelola Pesanan'],
                ['href' => 'menu.php',     'icon' => '🍽️', 'label' => 'Kelola Menu'],
                ['href' => 'kategori.php', 'icon' => '📁', 'label' => 'Kategori'],
            ];
            foreach ($links as $l):
            ?>
            <a href="<?= $l['href'] ?>" class="<?= $current === $l['href'] ? 'active' : '' ?>">
                <?= $l['icon'] ?> <?= $l['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="admin-main">
        <div style="margin-bottom:1.5rem">
            <h1 style="font-family:'DM Serif Display',serif;font-size:1.8rem"><?= $pageTitle ?? 'Dashboard' ?></h1>
        </div>
