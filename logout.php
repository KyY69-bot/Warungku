<?php
require_once '../includes/config.php';
unset($_SESSION['admin'], $_SESSION['admin_name']);
setFlash('info', 'Anda telah keluar dari panel admin.');
header('Location: login.php');
exit;
