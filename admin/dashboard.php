<?php
// 1. Set Judul Halaman
$page_title = "Admin Dashboard";

// 2. Panggil Header
require_once 'admin_header.php';

// 3. Logika Spesifik Halaman
// Hitung Jumlah Produk
$result_produk = $conn->query("SELECT COUNT(id) as total FROM products");
$data_produk = $result_produk->fetch_assoc();
$jumlah_produk = $data_produk['total'];

// Hitung Jumlah Pengguna
$result_pengguna = $conn->query("SELECT COUNT(id) as total FROM users");
$data_pengguna = $result_pengguna->fetch_assoc();
$jumlah_pengguna = $data_pengguna['total'];

// BARU: Hitung Total Pendapatan
// Mengambil total sum dari kolom 'total_harga' di tabel 'orders'
$result_pendapatan = $conn->query("SELECT SUM(total_harga) as total FROM orders");
$data_pendapatan = $result_pendapatan->fetch_assoc();
// Jika tidak ada data (NULL), set ke 0.
$total_pendapatan = $data_pendapatan['total'] ? $data_pendapatan['total'] : 0;

$conn->close();
?>

<div class="dashboard-cards">
    <div class="card">
        <h3>Jumlah Produk</h3>
        <p><?php echo $jumlah_produk; ?></p>
    </div>

    <div class="card">
        <h3>Jumlah Pengguna</h3>
        <p><?php echo $jumlah_pengguna; ?></p>
    </div>

    <div class="card">
        <h3>Total Pendapatan</h3>
        <p>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
    </div>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>