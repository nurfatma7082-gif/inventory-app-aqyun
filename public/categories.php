<?php
require_once dirname(__DIR__) . '/helpers/auth.php';
checkLogin();
require_once dirname(__DIR__) . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $stmt->execute(['name' => $name, 'description' => $desc]);
        header("Location: categories.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: categories.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kategori Barang - Inventory Control</title>
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
        .card { background-color: var(--card-bg); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.35rem; }
        .form-control { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: 6px; }
        .btn { padding: 0.6rem 1rem; background-color: var(--primary); color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.875rem; }
        table { width: 100%; border-collapse: collapse; background-color: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
        th, td { padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; border-bottom: 1px solid var(--border-color); }
        th { background-color: #f1f5f9; font-weight: 600; }
        .btn-delete { color: #ef4444; text-decoration: none; font-weight: 600; }
        .btn-logout { margin-top: auto; color: #fca5a5; text-decoration: none; font-size: 0.875rem; padding: 0.75rem 1rem; border-radius: 8px; background-color: rgba(239,68,68,0.1); text-align: center; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">📦 InventoryApp</div>
    <ul class="nav-menu">
        <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="categories.php" class="nav-link active">Kategori Barang</a></li>
        <li><a href="products.php" class="nav-link">Data Produk</a></li>
        <li><a href="transactions.php" class="nav-link">Transaksi Stok</a></li>
    </ul>
    <a href="logout.php" class="btn-logout">Keluar</a>
</div>
<div class="main-content">
    <h1 class="page-title">Manajemen Kategori Barang</h1>
    
    <div class="card">
        <h2 style="font-size: 1.1rem; margin-bottom: 1rem;">Tambah Kategori Baru</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Elektronik">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-control" placeholder="Deskripsi singkat">
            </div>
            <button type="submit" name="add_category" class="btn">Simpan Kategori</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                <td><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                <td><a href="?delete=<?= $cat['id'] ?>" class="btn-delete" onclick="return confirm('Hapus kategori ini?')">Hapus</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
            <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada kategori data.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>