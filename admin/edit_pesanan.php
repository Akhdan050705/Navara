<?php
// 1. Set Judul Halaman
$page_title = "Edit Pesanan";

// 2. Panggil Header
require_once 'admin_header.php';

$pesan_error = "";
$pesan_sukses = "";

$result_customers = $conn->query("SELECT nama FROM users WHERE role = 'customer' ORDER BY nama ASC");
$customers = [];
if ($result_customers->num_rows > 0) {
    while ($row = $result_customers->fetch_assoc()) {
        $customers[] = $row['nama'];
    }
}

// 3. Cek ID Pesanan dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}
$id_pesanan = $_GET['id'];

// 4. Logika POST (Saat menyimpan perubahan)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $total_harga = $_POST['harga']; // Ambil dari input hidden
    $status = $_POST['status'];

    if (empty($nama_pelanggan) || empty($total_harga) || empty($status)) {
        $pesan_error = "Semua field wajib diisi.";
    } else {
        // Update database
        $stmt = $conn->prepare("UPDATE orders SET nama_pelanggan = ?, total_harga = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sisi", $nama_pelanggan, $total_harga, $status, $id_pesanan);
        
        if ($stmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Pesanan berhasil diperbarui!'];
            header("Location: pesanan.php"); // Redirect ke halaman daftar pesanan
            exit; // Wajib ada
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }
        $stmt->close();
    }
}

// 5. Logika GET (Ambil data untuk ditampilkan di form)
$stmt_get = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_get->bind_param("i", $id_pesanan);
$stmt_get->execute();
$result = $stmt_get->get_result();

if ($result->num_rows === 0) {
    echo "<div class='message error'>Pesanan tidak ditemukan.</div>";
    require_once 'admin_footer.php';
    $conn->close();
    exit;
}
$order = $result->fetch_assoc();
$stmt_get->close();
$conn->close();
?>

<div class="form-card">
    <h3>Edit Pesanan ORD-<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></h3>
    <br>
    
    <form action="edit_pesanan.php?id=<?php echo $id_pesanan; ?>" method="POST">
        <div class="form-group">
            <label for="nama_pelanggan">Nama Pelanggan:</label>
            <select id="nama_pelanggan" name="nama_pelanggan" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($customers as $nama_customer): ?>
                    <option value="<?php echo htmlspecialchars($nama_customer); ?>" 
                        <?php 
                        // Tambahkan 'selected' jika nama customer cocok dengan data pesanan
                        if ($nama_customer == $order['nama_pelanggan']) { 
                            echo 'selected'; 
                        } 
                        ?>>
                        <?php echo htmlspecialchars($nama_customer); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="harga_display">Total Harga:</label>
            <input type="text" id="harga_display" onkeyup="formatRupiah(this, 'harga')" value="Rp. <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>" required>
            <input type="hidden" id="harga" name="harga" value="<?php echo $order['total_harga']; ?>">
        </div>

        <div class="form-group">
            <label for="status">Status Pesanan:</label>
            <select id="status" name="status" required>
                <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped (Dikirim)</option>
                <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed (Selesai)</option>
                <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Dibatalkan)</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn-submit">Update Pesanan</button>
        </div>
    </form>
</div>

<?php
// 7. Panggil Footer
require_once 'admin_footer.php';
?>