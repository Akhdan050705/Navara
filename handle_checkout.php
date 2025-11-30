<?php
// Muat Config, Helper, dan Vendor/Autoload
require_once 'config.php';
require_once 'vendor/autoload.php'; // <-- WAJIB (dari Composer)

// 1. Set Kunci API Midtrans
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// 2. Cek jika data di-POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request salah.']);
    exit;
}

// 3. Ambil data dari form (SAMA SEPERTI SEBELUMNYA)
$nama_pelanggan = $_POST['nama_pelanggan'];
$email = $_POST['email'];
$telepon = $_POST['telepon'];
$alamat = $_POST['alamat'];
$ongkos_kirim = (int)($_POST['ongkos_kirim'] ?? 0);
$nama_paket_layanan = $_POST['nama_paket_layanan'] ?? 'N/A';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

// 4. Ambil data item dari session & Hitung Ulang Total (SAMA SEPERTI SEBELUMNYA)
if (!isset($_SESSION['checkout_items_ids']) || empty($_SESSION['checkout_items_ids'])) {
    echo json_encode(['status' => 'error', 'message' => 'Keranjang checkout kosong.']);
    exit;
}
$items_to_checkout_ids = $_SESSION['checkout_items_ids'];
$id_string = implode(',', $items_to_checkout_ids);

$result = $conn->query("SELECT * FROM products WHERE id IN ($id_string)");
$total_harga_produk = 0;
$items_to_save = []; // Untuk DB
$midtrans_items = []; // Untuk Midtrans

if ($result->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Item tidak valid.']);
    exit;
}

while ($produk = $result->fetch_assoc()) {
    $product_id = $produk['id'];
    $quantity = $_SESSION['cart'][$product_id];
    $harga_saat_beli = $produk['harga'];
    
    $total_harga_produk += ($harga_saat_beli * $quantity);
    
    $items_to_save[] = [ 'id' => $product_id, 'qty' => $quantity, 'harga' => $harga_saat_beli ];
    
    // Siapkan array untuk Midtrans
    $midtrans_items[] = [
        'id'       => $product_id,
        'price'    => $harga_saat_beli,
        'quantity' => $quantity,
        'name'     => $produk['nama_produk']
    ];
}

// 5. Hitung Total Harga FINAL (SAMA SEPERTI SEBELUMNYA)
$total_harga_final = $total_harga_produk + $ongkos_kirim;

// Tambahkan ongkir sebagai item di Midtrans
$midtrans_items[] = [
    'id'       => 'ONGKIR',
    'price'    => $ongkos_kirim,
    'quantity' => 1,
    'name'     => 'Biaya Pengiriman (' . $nama_paket_layanan . ')'
];


// 6. Simpan Pesanan ke Database Anda (KRUSIAL: SEBELUM panggil Midtrans)
$order_id = null;
try {
    $conn->begin_transaction();
    
    $stmt_order = $conn->prepare("INSERT INTO orders 
        (user_id, nama_pelanggan, email, telepon, alamat, total_harga, payment_method, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    
    // (Kita ambil $payment_method dari form)
    $payment_method = $_POST['payment_method']; 
    
    $stmt_order->bind_param("issssis", 
        $user_id, $nama_pelanggan, $email, $telepon, $alamat, $total_harga_final, $payment_method);
    $stmt_order->execute();
    
    $order_id = $conn->insert_id; // <-- Ambil ID pesanan BARU
    
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, harga_saat_beli) VALUES (?, ?, ?, ?)");
    foreach ($items_to_save as $item) {
        $stmt_item->bind_param("iiii", $order_id, $item['id'], $item['qty'], $item['harga']);
        $stmt_item->execute();
    }
    
    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan ke DB: ' . $e->getMessage()]);
    exit;
}

// 7. Siapkan Transaksi untuk Midtrans
// Gunakan Order ID unik dari DB Anda
$transaction_details = [
    'order_id'     => $order_id . '_navara_' . time(), // Buat ID unik
    'gross_amount' => $total_harga_final,
];

// Siapkan detail pelanggan
$customer_details = [
    'first_name' => $nama_pelanggan,
    'email'      => $email,
    'phone'      => $telepon,
];

// Siapkan parameter lengkap
$params = [
    'transaction_details' => $transaction_details,
    'customer_details'    => $customer_details,
    'item_details'        => $midtrans_items,
];

// 8. Dapatkan Snap Token dari Midtrans
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    // Bersihkan keranjang HANYA JIKA token berhasil didapat
    foreach ($items_to_checkout_ids as $id) {
        unset($_SESSION['cart'][$id]);
    }
    unset($_SESSION['checkout_items_ids']);
    
    // Kirim token kembali ke JavaScript
    echo json_encode(['status' => 'success', 'token' => $snapToken]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mendapatkan token Midtrans: ' . $e->getMessage()]);
}

$conn->close();
?>