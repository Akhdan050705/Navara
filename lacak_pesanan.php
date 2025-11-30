<?php
// 1. Set Judul & Panggil Header
$page_title = "Lacak Pesanan";
require_once 'user_header.php';

// 2. Keamanan: Cek login & ID pesanan
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['id'];

// 3. Ambil data status pesanan
$stmt = $conn->prepare("SELECT status, created_at FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Pesanan tidak ditemukan.";
    require_once 'user_footer.php';
    exit;
}
$order = $result->fetch_assoc();
$status = $order['status'];
$tanggal_pesan = $order['created_at'];
$stmt->close();
$conn->close();

// 4. Logika untuk status
$is_processed = in_array($status, ['Processing', 'Shipped', 'Completed']);
$is_shipped = in_array($status, ['Shipped', 'Completed']);
$is_completed = ($status == 'Completed');
$is_cancelled = ($status == 'Cancelled');

?>

<section class="content-wrapper" style="margin-top: 25px;">
    
    <div class="invoice-box" style="max-width: 800px; margin: 0 auto;">
        <div class="invoice-header">
            <h1>Lacak Pesanan ORD-<?php echo str_pad($order_id, 3, '0', STR_PAD_LEFT); ?></h1>
            <a href="pesanan_saya.php" class="btn-rincian" style="background: #6c757d;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if ($is_cancelled): ?>
            <div class="stepper-wrapper cancelled">
                <div class="stepper-item active">
                    <div class="step-icon"><i class="fa-solid fa-ban"></i></div>
                    <div class="step-label">Pesanan Dibatalkan</div>
                </div>
            </div>
        <?php else: ?>
            <div class="stepper-wrapper">
                <div class="stepper-item active">
                    <div class="step-icon"><i class="fa-solid fa-file-invoice"></i></div>
                    <div class="step-label">Pesanan Dibuat</div>
                    <div class="step-date"><?php echo date('d M Y, H:i', strtotime($tanggal_pesan)); ?></div>
                </div>
                
                <div class="stepper-item <?php echo $is_processed ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fa-solid fa-box-open"></i></div>
                    <div class="step-label">Pesanan Diproses</div>
                    <?php if ($is_processed): ?><div class="step-date">Admin sedang menyiapkan pesanan Anda.</div><?php endif; ?>
                </div>

                <div class="stepper-item <?php echo $is_shipped ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <div class="step-label">Pesanan Dikirim</div>
                    <?php if ($is_shipped): ?><div class="step-date">Pesanan Anda dalam perjalanan.</div><?php endif; ?>
                </div>
                
                <div class="stepper-item <?php echo $is_completed ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fa-solid fa-check-circle"></i></div>
                    <div class="step-label">Pesanan Selesai</div>
                    <?php if ($is_completed): ?><div class="step-date">Pesanan telah diterima.</div><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// 6. Panggil Footer
require_once 'user_footer.php';
?>