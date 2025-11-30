<?php
// 1. Panggil config
require_once '../config.php';

// 2. Keamanan: Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

// 3. Cek ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}
$id_pesanan = $_GET['id'];

// 4. Hapus data dari database
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $id_pesanan);

if ($stmt->execute()) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Pesanan berhasil dihapus.'];
} else {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal menghapus pesanan.'];
}
header("Location: pesanan.php");
exit;

$stmt->close();
$conn->close();
exit;
?>