<?php
// 1. Panggil config (kita perlu ini sebelum header untuk query)
require_once '../config.php';

// 2. Cek ID Pelanggan dari URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: pelanggan.php");
    exit;
}
$user_id = $_GET['user_id'];

// 3. Ambil data pelanggan (untuk mendapatkan nama)
$stmt_user = $conn->prepare("SELECT nama FROM users WHERE id = ? AND role = 'customer'");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    header("Location: pelanggan.php");
    exit;
}
$user = $result_user->fetch_assoc();
$nama_pelanggan = $user['nama'];

// 4. Set Judul Halaman (setelah dapat nama)
$page_title = "Riwayat: " . htmlspecialchars($nama_pelanggan);

// 5. Panggil Header
require_once 'admin_header.php';

// 6. Ambil data pesanan berdasarkan NAMA pelanggan
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE nama_pelanggan = ? ORDER BY created_at DESC");
$stmt_orders->bind_param("s", $nama_pelanggan);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<div class="table-container-header">
    <h2 class="table-title">Riwayat Pesanan untuk: <?php echo htmlspecialchars($nama_pelanggan); ?></h2>
    <a href="pelanggan.php" class="btn-aksi btn-tambah" style="background-color: #555; color: white;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Pelanggan
    </a>
</div>

<div class="table-container">

    <?php if ($result_orders->num_rows > 0): ?>
        <table>
            <thead class="table-header-dark">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Tanggal Pesan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_orders->fetch_assoc()): ?>
                <tr>
                    <td><strong>ORD-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                    <td>Rp. <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-receipt"></i>
            <p>Pelanggan ini belum memiliki riwayat pesanan.</p>
        </div>
    <?php endif; ?>
    
    <?php 
        $stmt_user->close(); 
        $stmt_orders->close();
        $conn->close(); 
    ?>
</div>

<?php
// 8. Panggil Footer
require_once 'admin_footer.php';
?>