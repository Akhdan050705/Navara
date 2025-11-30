<?php
// 1. Set Judul & Panggil Header
$page_title = "Pesanan Saya";
require_once 'user_header.php';

// 2. Keamanan: Pastikan user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    // Jika belum login, redirect ke login
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 3. Logika Tab Filter (Request 1)
$filter_status = $_GET['status'] ?? 'semua'; // Default 'semua'

$sql = "SELECT * FROM orders WHERE user_id = ? ";
$params = [$user_id]; // <-- $user_id SELALU jadi parameter pertama
$types = "i";         // <-- $types SELALU diawali 'i'

if ($filter_status == 'berlangsung') {
    $sql .= "AND status IN (?, ?) ";
    $types .= "ss";
    $status1 = 'Processing';
    $status2 = 'Shipped';
    $params[] = $status1; // <-- Tambahkan ke array
    $params[] = $status2; // <-- Tambahkan ke array
    $active_tab = 'berlangsung';

} elseif ($filter_status == 'selesai') {
    $sql .= "AND status = ? ";
    $types .= "s";
    $status_selesai = 'Completed'; // <-- Buat variabel
    $params[] = $status_selesai;   // <-- PASTIKAN BARIS INI ADA
    $active_tab = 'selesai';

} else {
    // 'semua'
    $active_tab = 'semua';
}

$sql .= "ORDER BY created_at DESC";

// 4. Ambil Data Pesanan
$stmt = $conn->prepare($sql);

// Cek jika filter aktif ('semua' tidak perlu bind param tambahan)
if (count($params) > 1) { // <-- Perubahan di sini
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result_orders = $stmt->get_result();
?>

<section class="content-wrapper" style="margin-top: 25px;">

    <h1 class="page-title">Pesanan Saya</h1>

    <nav class="order-tabs">
        <a href="pesanan_saya.php?status=semua" 
           class="<?php echo ($active_tab == 'semua') ? 'active' : ''; ?>">Semua</a>
        <a href="pesanan_saya.php?status=berlangsung" 
           class="<?php echo ($active_tab == 'berlangsung') ? 'active' : ''; ?>">Sedang Berlangsung</a>
        <a href="pesanan_saya.php?status=selesai" 
           class="<?php echo ($active_tab == 'selesai') ? 'active' : ''; ?>">Selesai</a>
    </nav>

    <div class="order-list">
        <?php if ($result_orders->num_rows > 0): ?>
            <?php while ($order = $result_orders->fetch_assoc()): ?>
                
                <div class="order-card">
                    <div class="order-card-header">
                        <p>Tanggal Pesan: <?php echo date('d M Y, H:i', strtotime($order['created_at'] ?? 0)); ?></p>
                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>
                    
                    <div class="order-card-body">
                        <div class="order-info">
                            <p>Tanggal Pesan: <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                            <p>Total Bayar: <strong>Rp. <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></strong></p>
                        </div>
                        <div class="order-actions">
                            <a href="lacak_pesanan.php?id=<?php echo $order['id']; ?>" class="btn-lacak">Lacak</a>
                            <a href="rincian_pesanan.php?id=<?php echo $order['id']; ?>" class="btn-rincian">Rincian</a>
                        </div>
                    </div>
                </div>
                
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="padding: 50px 20px;">
                <i class="fa-solid fa-receipt"></i>
                <p>Tidak ada pesanan dalam kategori ini.</p>
            </div>
        <?php endif; ?>
    </div>

</section>

<?php
// 6. Panggil Footer
$stmt->close();
$conn->close();
require_once 'user_footer.php';
?>