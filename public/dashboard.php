<?php
require_once dirname(__DIR__) . '/helpers/auth.php';
checkLogin();
require_once dirname(__DIR__) . '/config/database.php';

$totalProducts     = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories   = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-body: #f8fafc; --sidebar-bg: #0f172a; --card-bg: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --border-color: #e2e8f0; --primary: #2563eb; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg-body); color: var(--text-main); display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: #fff; padding: 2rem 1.5rem; display: flex; flex-direction: column; }
        .sidebar-brand { font-size: 1.125rem; font-weight: 700; margin-bottom: 2.5rem; }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
        .nav-link { display: block; padding: 0.75rem 1rem; color: #94a3b8; text-decoration: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; }
        .nav-link.active, .nav-link:hover { background-color: #1e293b; color: #fff; }
        .main-content { flex: 1; padding: 2.5rem; }
        .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
        .stat-card { background-color: var(--card-bg); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; }
        .stat-label { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; }
        .btn-logout { margin-top: auto; color: #fca5a5; text-decoration: none; font-size: 0.875rem; padding: 0.75rem 1rem; border-radius: 8px; background-color: rgba(239,68,68,0.1); text-align: center; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">📦 InventoryApp</div>
    <ul class="nav-menu">
        <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
        <li><a href="categories.php" class="nav-link">Kategori Barang</a></li>
        <li><a href="products.php" class="nav-link">Data Produk</a></li>
        <li><a href="transactions.php" class="nav-link">Transaksi Stok</a></li>
    </ul>
    <a href="logout.php" class="btn-logout">Keluar</a>
</div>
<div class="main-content">
    <h1 class="page-title">Ringkasan Sistem</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Barang</div>
            <div class="stat-value"><?= number_format($totalProducts) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Kategori</div>
            <div class="stat-value"><?= number_format($totalCategories) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value"><?= number_format($totalTransactions) ?></div>
        </div>
    </div>
</div>
</body>
</html>