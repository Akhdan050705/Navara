<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

// Set Kunci API Midtrans
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Buat instance Midtrans Notification
$notif = new \Midtrans\Notification();

// Ambil ID pesanan dari notifikasi
$order_id_full = $notif->order_id;
// Pisahkan ID pesanan Anda (Misal: "12_navara_167888")
$order_id_parts = explode('_', $order_id_full);
$order_id = $order_id_parts[0]; // Ambil ID aslinya, misal: "12"

$transaction_status = $notif->transaction_status;
$payment_type = $notif->payment_type;
$status_code = $notif->status_code;

// (Sangat disarankan) Verifikasi signature key
// $signature_key = hash('sha512', $order_id_full . $status_code . $notif->gross_amount . MIDTRANS_SERVER_KEY);
// if ($notif->signature_key != $signature_key) {
//     http_response_code(403);
//     echo "Signature tidak valid.";
//     exit;
// }

// Buka koneksi DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Koneksi DB gagal: " . $conn->connect_error);
}

// Update database berdasarkan status
if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
    // Jika pembayaran BERHASIL (settlement)
    $stmt = $conn->prepare("UPDATE orders SET status = 'Processing' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
} 
elseif ($transaction_status == 'expire' || $transaction_status == 'cancel' || $transaction_status == 'deny') {
    // Jika pembayaran GAGAL atau KADALUARSA
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
}
// (Status 'pending' kita abaikan, karena default-nya sudah 'Pending')

$stmt->close();
$conn->close();

echo "Notifikasi diterima.";
?>