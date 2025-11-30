<?php
// 1. Set Judul & Panggil Header
$page_title = "Rincian Pesanan";
require_once 'user_header.php';

// 2. Keamanan: Cek login & ID pesanan
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['id'];

// 3. Ambil data pesanan (pastikan pesanan ini milik user yg login)
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result_order = $stmt->get_result();

if ($result_order->num_rows === 0) {
    echo "Pesanan tidak ditemukan.";
    require_once 'user_footer.php';
    exit;
}
$order = $result_order->fetch_assoc();
$stmt->close();

// 4. Ambil item-item di dalam pesanan
$stmt_items = $conn->prepare("
    SELECT oi.quantity, oi.harga_saat_beli, p.nama_produk, p.gambar
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<section class="content-wrapper" style="margin-top: 25px;">
    
    <div class="invoice-box">
        <div class="invoice-header">
            <h1>Rincian Pesanan</h1>
            <a href="pesanan_saya.php" class="btn-rincian" style="background: #6c757d;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="invoice-details">
            <div>
                <strong>ID Pesanan:</strong> ORD-<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?><br>
                <strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($order['created_at'])); ?><br>
                <strong>Status:</strong> 
                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                    <?php echo htmlspecialchars($order['status']); ?>
                </span>
            </div>
            <div>
                <strong>Dikirim Ke:</strong><br>
                <?php echo htmlspecialchars($order['nama_pelanggan']); ?><br>
                <?php echo htmlspecialchars($order['telepon']); ?><br>
                <?php echo nl2br(htmlspecialchars($order['alamat'])); ?>
            </div>
        </div>

        <div class="table-container" style="margin-top: 20px;">
            <table>
                <thead class="table-header-dark">
                    <tr>
                        <th colspan="2">Produk</th>
                        <th>Harga</th>
                        <th>Kuantitas</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $result_items->fetch_assoc()): ?>
                    <tr>
                        <td style="width: 80px;">
                            <img src="uploads/<?php echo htmlspecialchars($item['gambar']); ?>" class="product-image-thumbnail">
                        </td>
                        <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                        <td>Rp. <?php echo number_format($item['harga_saat_beli'], 0, ',', '.'); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><strong>Rp. <?php echo number_format($item['harga_saat_beli'] * $item['quantity'], 0, ',', '.'); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="invoice-total">
            <p>Metode Pembayaran: <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong></p>
            <p>Total Harga: <strong>Rp. <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></strong></p>
        </div>
        
    </div>
</section>

<?php
$stmt_items->close();
$conn->close();
require_once 'user_footer.php';
?>