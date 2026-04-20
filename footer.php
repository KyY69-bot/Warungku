
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="logo" style="margin-bottom:1rem">
                    <span class="logo-icon">🍜</span>
                    <span class="logo-text"><?= SITE_NAME ?></span>
                </div>
                <p style="color:var(--text-muted);font-size:.9rem">Nikmati makanan lezat dengan pemesanan mudah dan cepat.</p>
            </div>
            <div>
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/index.php">Menu Makanan</a></li>
                    <li><a href="<?= SITE_URL ?>/keranjang.php">Keranjang</a></li>
                    <li><a href="<?= SITE_URL ?>/cek-pesanan.php">Cek Pesanan</a></li>
                </ul>
            </div>
            <div>
                <h4>Kontak</h4>
                <p style="color:var(--text-muted);font-size:.9rem">
                    📍 Jl. Kuliner No. 1, Jakarta<br>
                    📞 0812-3456-7890<br>
                    🕐 08.00 - 22.00 WIB
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> <?= SITE_NAME ?>. Dibuat dengan ❤️ menggunakan PHP murni.</p>
        </div>
    </div>
</footer>
<script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
