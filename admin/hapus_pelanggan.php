<?php
require_once 'config.php';

// Keamanan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}
$user_id = $_GET['id'];

// Mulai transaksi
$conn->begin_transaction();

try {
    // 1. Ambil nama pelanggan
    $stmt_get = $conn->prepare("SELECT nama FROM users WHERE id = ?");
    $stmt_get->bind_param("i", $user_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Pelanggan tidak ditemukan.");
    }
    $user = $result->fetch_assoc();
    $nama_pelanggan = $user['nama'];
    $stmt_get->close();

    // 2. Hapus pesanan terkait (berdasarkan nama)
    $stmt_del_orders = $conn->prepare("DELETE FROM orders WHERE nama_pelanggan = ?");
    $stmt_del_orders->bind_param("s", $nama_pelanggan);
    $stmt_del_orders->execute();
    $stmt_del_orders->close();

    // 3. Hapus pelanggan
    $stmt_del_user = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_del_user->bind_param("i", $user_id);
    $stmt_del_user->execute();
    $stmt_del_user->close();

    // Jika semua berhasil
    $conn->commit();
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Pelanggan & riwayatnya telah dihapus.'];
    header("Location: pelanggan.php");

} catch (Exception $e) {
    // Jika ada error, batalkan semua
    $conn->rollback();
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal menghapus pelanggan.'];
    header("Location: pelanggan.php");
    }

$conn->close();
exit;
?>