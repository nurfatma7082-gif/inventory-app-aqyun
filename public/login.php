<?php
session_start();
require_once dirname(__DIR__) . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = trim($_POST['username'] ?? '');
    $pass_input = trim($_POST['password'] ?? '');

    if (!empty($user_input) && !empty($pass_input)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $user_input]);
        $user = $stmt->fetch();

        if ($user) {
            if ($pass_input === $user['password'] || password_verify($pass_input, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password yang kamu masukkan salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    } else {
        $error = "Harap isi username dan password.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Manajemen Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --danger-bg: rgba(239, 68, 68, 0.1);
            --danger-border: rgba(239, 68, 68, 0.3);
            --danger-text: #fca5a5;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .login-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; width: 100%; max-width: 400px; padding: 2.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); }
        .brand-header { margin-bottom: 2rem; }
        .brand-logo { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: var(--primary); border-radius: 8px; font-weight: 700; color: #fff; margin-bottom: 1rem; }
        .brand-title { font-size: 1.25rem; font-weight: 700; }
        .brand-subtitle { font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; background-color: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-size: 0.875rem; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .btn-submit { width: 100%; padding: 0.75rem 1rem; background-color: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; margin-top: 0.5rem; }
        .btn-submit:hover { background-color: var(--primary-hover); }
        .alert-error { background-color: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger-text); padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand-header">
        <div class="brand-logo">INV</div>
        <h1 class="brand-title">Inventory Control</h1>
        <p class="brand-subtitle">Silakan masuk untuk mengelola data stok.</p>
    </div>
    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-submit">Masuk ke Sistem</button>
    </form>
</div>
</body>
</html>