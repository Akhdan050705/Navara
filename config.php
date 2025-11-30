<?php
session_start();

define('KOMERCE_API_KEY', '');
define('KOMERCE_ORIGIN_DISTRICT_ID', '1391');
define('KOMERCE_BASE_URL', 'https://rajaongkir.komerce.id/api/v1/');

// 4. Konfigurasi Midtrans
define('MIDTRANS_SERVER_KEY', ''); // <-- GANTI DENGAN SERVER KEY ANDA
define('MIDTRANS_CLIENT_KEY', ''); // <-- GANTI DENGAN CLIENT KEY ANDA
define('MIDTRANS_IS_PRODUCTION', false); // false = Sandbox, true = Production


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');      
define('DB_NAME', 'navara_db');

// 3. Buat Koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// 4. Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Mengatur charset ke utf8mb4 (disarankan)
$conn->set_charset("utf8mb4");
?>