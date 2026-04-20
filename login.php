<?php
require_once '../includes/config.php';

if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kredensial default: admin / admin123
    // UBAH ini di production!
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        $_SESSION['admin_name'] = 'Administrator';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Sora',sans-serif;background:linear-gradient(135deg,#1A0A00,#3D1200);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem; }
        .login-box { background:white;border-radius:24px;padding:3rem;width:100%;max-width:420px;box-shadow:0 32px 80px rgba(0,0,0,.3); }
        .login-logo { text-align:center;margin-bottom:2rem; }
        .login-logo .icon { font-size:3rem;display:block;margin-bottom:.5rem; }
        .login-logo h1 { color:#E8440A;font-size:1.5rem; }
        .login-logo p { color:#8A7060;font-size:.85rem;margin-top:.25rem; }
        .form-group { margin-bottom:1.25rem; }
        .form-group label { display:block;font-weight:600;font-size:.85rem;margin-bottom:.5rem;color:#1A0A00; }
        .form-group input { width:100%;padding:12px 16px;border:2px solid #EAD8CC;border-radius:10px;font-family:'Sora',sans-serif;font-size:.9rem;outline:none;transition:border-color .2s; }
        .form-group input:focus { border-color:#E8440A; }
        .btn { display:block;width:100%;padding:14px;background:#E8440A;color:white;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;transition:all .2s; }
        .btn:hover { background:#C4350A; }
        .error { background:#F8D7DA;color:#721C24;padding:12px 16px;border-radius:10px;margin-bottom:1.5rem;font-size:.875rem;font-weight:500; }
        .hint { text-align:center;margin-top:1rem;font-size:.78rem;color:#8A7060; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <span class="icon">🍜</span>
            <h1><?= SITE_NAME ?> Admin</h1>
            <p>Masuk ke panel administrator</p>
        </div>
        
        <?php if ($error): ?>
        <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                       placeholder="Username admin" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">🔐 Masuk</button>
        </form>
        
        <p class="hint">Default: admin / admin123 (ubah di production)</p>
    </div>
</body>
</html>
