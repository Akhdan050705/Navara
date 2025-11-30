<?php
// 1. Set Judul Halaman
$page_title = "Tambah Pesanan Baru";

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

// 3. Logika PHP (Handle Form Submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $total_harga = $_POST['harga']; // Ambil dari input hidden (untuk format Rp)
    $status = $_POST['status'];

    if (empty($nama_pelanggan) || empty($total_harga) || empty($status)) {
        $pesan_error = "Semua field wajib diisi.";
    } else {
        // Masukkan ke database
        $stmt = $conn->prepare("INSERT INTO orders (nama_pelanggan, total_harga, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nama_pelanggan, $total_harga, $status);
        
        if ($stmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Pesanan baru berhasil ditambahkan!'];
            header("Location: pesanan.php"); // Redirect ke halaman daftar pesanan
            exit; // Wajib ada
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }
        $stmt->close();
    }
}
$conn->close();
?>

<div class="form-card">
    <form action="tambah_pesanan.php" method="POST">
        
        <div class="form-group">
            <label for="nama_pelanggan">Nama Pelanggan:</label>
            <select id="nama_pelanggan" name="nama_pelanggan" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($customers as $nama_customer): ?>
                    <option value="<?php echo htmlspecialchars($nama_customer); ?>">
                        <?php echo htmlspecialchars($nama_customer); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="harga_display">Total Harga:</label>
            <input type="text" id="harga_display" onkeyup="formatRupiah(this, 'harga')" placeholder="Rp. 100.000" required>
            <input type="hidden" id="harga" name="harga">
        </div>

        <div class="form-group">
            <label for="status">Status Pesanan:</label>
            <select id="status" name="status" required>
                <option value="Processing">Processing</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn-submit">Simpan Pesanan</button>
        </div>
        
    </form>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>