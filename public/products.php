<?php
require_once dirname(__DIR__) . '/helpers/auth.php';
checkLogin();
require_once dirname(__DIR__) . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name     = trim($_POST['name']);
    $cat_id   = (int)$_POST['category_id'];
    $sku      = trim($_POST['sku']);
    $stock    = (int)$_POST['stock'];
    $price    = (float)$_POST['price'];

    if (!empty($name) && $cat_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, sku, stock, price) VALUES (:cat, :name, :sku, :stock, :price)");
        $stmt->execute(['cat' => $cat_id, 'name' => $name, 'sku' => $sku, 'stock' => $stock, 'price' => $price]);
        header("Location: products.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: products.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$products   = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Produk - Inventory Control</title>
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
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
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
        <li><a href="categories.php" class="nav-link">Kategori Barang</a></li>
        <li><a href="products.php" class="nav-link active">Data Produk</a></li>
        <li><a href="transactions.php" class="nav-link">Transaksi Stok</a></li>
    </ul>
    <a href="logout.php" class="btn-logout">Keluar</a>
</div>
<div class="main-content">
    <h1 class="page-title">Manajemen Data Produk</h1>
    
    <div class="card">
        <h2 style="font-size: 1.1rem; margin-bottom: 1rem;">Tambah Produk Baru</h2>
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Laptop Asus">
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">SKU / Kode Unique</label>
                    <input type="text" name="sku" class="form-control" placeholder="SKU-1001">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stock" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="number" name="price" step="0.01" class="form-control" required placeholder="100000">
                </div>
            </div>
            <button type="submit" name="add_product" class="btn" style="margin-top: 0.5rem;">Simpan Produk</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><code><?= htmlspecialchars($p['sku'] ?? '-') ?></code></td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'Tanpa Kategori') ?></td>
                <td><span style="font-weight:700; color: <?= $p['stock'] < 5 ? '#ef4444' : '#10b981' ?>"><?= $p['stock'] ?></span></td>
                <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                <td><a href="?delete=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Hapus produk ini?')">Hapus</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada data produk.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>