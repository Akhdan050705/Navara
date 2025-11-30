<?php
$page_title = "Pesanan Berhasil";
require_once 'user_header.php';
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>

<section class="content-wrapper" style="margin-top: 25px; text-align: center; padding: 50px;">
    <i class="fa-solid fa-check-circle" style="font-size: 5rem; color: #28a745; margin-bottom: 20px;"></i>
    <h1>Pesanan Berhasil!</h1>
    <p style="font-size: 1.2rem; margin-top: 10px;">
        Terima kasih atas pesanan Anda.
    </p>
    <p style="margin-top: 10px;">
        Nomor pesanan Anda adalah: <strong>ORD-<?php echo str_pad($order_id, 3, '0', STR_PAD_LEFT); ?></strong>
    </p>
    <p style="margin-top: 20px;">
        <a href="home.php" class="btn-checkout">Kembali ke Home</a>
    </p>
</section>

<?php
require_once 'user_footer.php';
?>