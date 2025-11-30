<?php
// 1. Set Judul Halaman
$page_title = "Tambah Produk Baru";

// 2. Panggil Header
require_once 'admin_header.php';

// Variabel untuk pesan
$pesan_error = "";
$pesan_sukses = "";

// 3. Logika PHP (Handle Form Submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $berat_gram = $_POST['berat_gram'];
    $nama_gambar = "";

    // Handle Upload Gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/"; // Folder yang kita buat tadi
        $nama_gambar = uniqid() . '-' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $nama_gambar;
        
        // Cek tipe file (opsional tapi disarankan)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $pesan_error = "Maaf, hanya file JPG, JPEG, & PNG yang diperbolehkan.";
        } else {
            // Pindahkan file ke folder uploads
            if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $pesan_error = "Maaf, terjadi kesalahan saat mengupload gambar.";
            }
        }
    }

    // Jika tidak ada error sebelumnya, masukkan ke database
    if (empty($pesan_error)) {
        $stmt = $conn->prepare("INSERT INTO products (nama_produk, deskripsi, kategori, harga, stok, gambar, berat_gram) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidsi", $nama_produk, $deskripsi, $kategori, $harga, $stok, $nama_gambar, $berat_gram);
        
        if ($stmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Produk baru berhasil ditambahkan!'];
            header("Location: tambah_produk.php"); // Redirect kembali ke halaman ini
            exit; // Wajib ada untuk menghentikan eksekusi skrip
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<div class="form-card">
    <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="nama_produk">Nama Produk:</label>
            <input type="text" id="nama_produk" name="nama_produk" placeholder="Nama Produk" required>
        </div>
        
        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk:</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Deskripsi singkat produk..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="kategori">Kategori Produk:</label>
            <select id="kategori" name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Makanan">Makanan</option>
                <option value="Pakaian">Pakaian</option>
                <option value="Aksesoris">Aksesoris</option>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group half-width">
                <label for="harga_display">Harga:</label>
                <input type="text" id="harga_display" 
                    onkeyup="formatRupiah(this, 'harga')" 
                    placeholder="contoh: Rp. 50.000" required>
                
                <input type="hidden" id="harga" name="harga">
            </div>
            
            <div class="form-group half-width">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" placeholder="contoh: 100" min="0" required>
            </div>

            <div class="form-group">
                <label for="berat_gram">Berat (dalam gram):</label>
                <input type="number" id="berat_gram" name="berat_gram" placeholder="contoh: 1000" min="1" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="gambar">Gambar Produk:</label>
            <input type="file" id="gambar" name="gambar" class="file-input" accept="image/png, image/jpeg, image/jpg">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn-submit">Simpan Produk</button>
        </div>
        
    </form>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>