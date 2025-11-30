<?php
// 1. Panggil config untuk session dan database
require_once 'config.php';

// 2. Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Anda bukan admin.");
}

// 3. Cek apakah ID produk ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: lihat_produk.php"); // Kembali jika tidak ada ID
    exit;
}

$id_produk = $_GET['id'];

// 4. (PENTING) Hapus file gambar dari server
// Kita ambil dulu nama filenya dari database
$stmt_select = $conn->prepare("SELECT gambar FROM products WHERE id = ?");
$stmt_select->bind_param("i", $id_produk);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $gambar_lama = $row['gambar'];

    // Jika ada nama gambar DAN file-nya ada di folder 'uploads'
    if (!empty($gambar_lama) && file_exists("uploads/" . $gambar_lama)) {
        unlink("uploads/" . $gambar_lama); // Hapus file gambar
    }
}
$stmt_select->close();


// 5. Hapus data produk dari database
$stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt_delete->bind_param("i", $id_produk);

if ($stmt_delete->execute()) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Produk berhasil dihapus.'];
} else {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal menghapus produk.'];
}
header("Location: lihat_produk.php");
exit;

$stmt_delete->close();
$conn->close();
exit;
?>