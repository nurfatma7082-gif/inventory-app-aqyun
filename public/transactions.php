<?php
require_once dirname(__DIR__) . '/helpers/auth.php';
checkLogin();
require_once dirname(__DIR__) . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaction'])) {
    $product_id = (int)$_POST['product_id'];
    $type       = $_POST['type']; // 'in' atau 'out'
    $qty        = (int)$_POST['quantity'];
    $note       = trim($_POST['reference_note']);

    if ($product_id > 0 && $qty > 0 && in_array($type, ['in', 'out'])) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $current_stock = $stmt->fetchColumn();

        if ($type === 'out' && $current_stock < $qty) {
            $error = "Stok tidak mencukupi untuk transaksi keluar!";
        } else {
            $pdo->beginTransaction();
            try {
                $stmtTx = $pdo->prepare("INSERT INTO transactions (product_id, type, quantity, reference_note) VALUES (:pid, :type, :qty, :note)");
                $stmtTx->execute(['pid' => $product_id, 'type' => $type, 'qty' => $qty, 'note' => $note]);

                $new_stock = ($type === 'in') ? ($current_stock + $qty) : ($current_stock - $qty);
                $stmtUpd   = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
                $stmtUpd->execute(['stock' => $new_stock, 'id' => $product_id]);

                $pdo->commit();
                header("Location: transactions.php");
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Gagal memproses transaksi: " . $e->getMessage();
            }
        }
    }
}

$products     = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$transactions = $pdo->query("SELECT t.*, p.name AS product_name FROM transactions t LEFT JOIN products p ON t.product_id = p.id ORDER BY t.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Stok - Inventory Control</title>
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
        .badge-in { background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; }
        .badge-out { background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; }
        .btn-logout { margin-top: auto; color: #fca5a5; text-decoration: none; font-size: 0.875rem; padding: 0.75rem 1rem; border-radius: 8px; background-color: rgba(239,68,68,0.1); text-align: center; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">📦 InventoryApp</div>
    <ul class="nav-menu">
        <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="categories.php" class="nav-link">Kategori Barang</a></li>
        <li><a href="products.php" class="nav-link">Data Produk</a></li>
        <li><a href="transactions.php" class="nav-link active">Transaksi Stok</a></li>
    </ul>
    <a href="logout.php" class="btn-logout">Keluar</a>
</div>
<div class="main-content">
    <h1 class="page-title">Pencatatan Transaksi Stok</h1>

    <?php if (!empty($error)): ?>
        <div style="background-color:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:0.75rem 1rem; border-radius:8px; margin-bottom:1.5rem;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 style="font-size: 1.1rem; margin-bottom: 1rem;">Catat Stok Masuk / Keluar</h2>
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Produk</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (Stok: <?= $p['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="type" class="form-control" required>
                        <option value="in">Barang Masuk (+)</option>
                        <option value="out">Barang Keluar (-)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" name="quantity" class="form-control" min="1" required placeholder="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan / Keterangan</label>
                    <input type="text" name="reference_note" class="form-control" placeholder="Contoh: Restok Supplier A">
                </div>
            </div>
            <button type="submit" name="add_transaction" class="btn" style="margin-top: 0.5rem;">Simpan Transaksi</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                <td><strong><?= htmlspecialchars($t['product_name'] ?? 'Produk Dihapus') ?></strong></td>
                <td>
                    <?php if ($t['type'] === 'in'): ?>
                        <span class="badge-in">MASUK</span>
                    <?php else: ?>
                        <span class="badge-out">KELUAR</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= $t['quantity'] ?></strong></td>
                <td><?= htmlspecialchars($t['reference_note'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada riwayat transaksi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>