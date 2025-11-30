<?php
require_once 'config.php'; // Mulai session

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Jika keranjang belum ada, buat
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Jika produk sudah ada di keranjang, tambahkan jumlahnya
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        // Jika belum ada, tambahkan baru
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Beri respon sukses (atau redirect kembali)
    // Untuk AJAX, kita kirim JSON. Untuk form biasa, kita redirect.
    header("Location: keranjang.php");
    exit;

} else {
    // Jika diakses langsung, kembalikan ke home
    header("Location: home.php");
    exit;
}
?>