# 📦 Sistem Manajemen Inventaris (Inventory App)

Aplikasi berbasis web untuk mengelola data inventaris barang, kategori, serta pencatatan transaksi stok masuk dan keluar secara *real-time*. Proyek ini dikembangkan menggunakan **PHP (Native dengan PDO)** dan **MySQL**.

## 🚀 Fitur Utama
- **Autentikasi Pengguna**: Sistem login yang aman untuk admin.
- **Manajemen Kategori**: Menambah dan menghapus kategori barang.
- **Manajemen Produk**: Mengelola data produk, kode SKU, harga, dan stok barang.
- **Transaksi Stok**: Pencatatan riwayat barang masuk dan keluar yang otomatis memperbarui jumlah stok produk.

## 🛠️ Teknologi yang Digunakan
- **Backend**: PHP (dengan ekstensi PDO)
- **Database**: MySQL / MariaDB (via XAMPP)
- **Frontend / Styling**: HTML5, CSS3, Google Fonts (Plus Jakarta Sans)

## ⚙️ Cara Menjalankan Proyek
1. Clone atau download repositori ini ke folder `htdocs` di direktori XAMPP kamu (`C:\xampp\htdocs\inventory-app`).
2. Nyalakan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buat database baru di phpMyAdmin dengan nama `inventory_db`.
4. Buat tabel-tabel berikut di database `inventory_db`:
   - `users` (id, username, password, role)
   - `categories` (id, name, description)
   - `products` (id, category_id, name, sku, stock, price)
   - `transactions` (id, product_id, type, quantity, reference_note, created_at)
5. Masukkan akun admin default ke tabel `users`:
   - **Username**: `admin`
   - **Password**: `admin123`
6. Buka browser dan akses: `http://localhost/inventory-app/public/login.php`
