<?php
// 1. Set Judul Halaman (harus sebelum memanggil header)
$page_title = "Admin Dashboard";

// 2. Panggil Header (ini sudah termasuk config.php dan session check)
require_once 'admin_header.php';

// 3. Logika Spesifik Halaman (menggunakan $conn dari config.php)
$result_produk = $conn->query("SELECT COUNT(id) as total FROM products");
$data_produk = $result_produk->fetch_assoc();
$jumlah_produk = $data_produk['total'];

$result_pengguna = $conn->query("SELECT COUNT(id) as total FROM users");
$data_pengguna = $result_pengguna->fetch_assoc();
$jumlah_pengguna = $data_pengguna['total'];

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
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>