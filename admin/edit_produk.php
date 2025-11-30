<?php
// 1. Set Judul Halaman
$page_title = "Edit Produk";

// 2. Panggil Header
require_once 'admin_header.php';

// Variabel pesan
$pesan_error = "";
$pesan_sukses = "";

// 3. Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: lihat_produk.php");
    exit;
}
$id_produk = $_GET['id'];


// 4. LOGIKA SAAT MENYIMPAN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga']; // Ini sudah angka bersih dari input hidden
    $stok = $_POST['stok'];
    $berat_gram = $_POST['berat_gram'];
    $gambar_lama = $_POST['gambar_lama']; // Nama gambar saat ini
    
    $nama_gambar_baru = $gambar_lama; // Defaultnya, kita pakai gambar lama

    // 4a. Handle Upload Gambar BARU (jika ada)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0 && !empty($_FILES['gambar']['name'])) {
        $target_dir = "uploads/";
        $nama_gambar_baru = uniqid() . '-' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $nama_gambar_baru;
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $pesan_error = "Maaf, hanya file JPG, JPEG, & PNG yang diperbolehkan.";
        } else {
            // Pindahkan file baru
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                // Sukses upload, hapus gambar lama (jika ada)
                if (!empty($gambar_lama) && file_exists("uploads/" . $gambar_lama)) {
                    unlink("uploads/" . $gambar_lama);
                }
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengupload gambar baru.";
                $nama_gambar_baru = $gambar_lama; // Gagal upload, kembali pakai gambar lama
            }
        }
    }

    // 4b. Update Database (jika tidak ada error upload)
    if (empty($pesan_error)) {
        $stmt = $conn->prepare("UPDATE products SET 
            nama_produk = ?, 
            deskripsi = ?, 
            kategori = ?, 
            harga = ?, 
            stok = ?, 
            gambar = ?,
            berat_gram = ? 
            WHERE id = ?");

        $stmt->bind_param("sssidsii", 
            $nama_produk, 
            $deskripsi, 
            $kategori, 
            $harga, 
            $stok, 
            $nama_gambar_baru,
            $berat_gram, 
            $id_produk
        );
        
        if ($stmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Produk berhasil diperbarui!'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }
        $stmt->close();
    }
} // Akhir dari logika POST


// 5. LOGIKA SAAT MEMUAT HALAMAN (GET)
// Ambil data produk terbaru dari DB untuk ditampilkan di form
$stmt_get = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_get->bind_param("i", $id_produk);
$stmt_get->execute();
$result_produk = $stmt_get->get_result();

if ($result_produk->num_rows === 0) {
    // Jika ID produk tidak ditemukan
    echo "<div class='message error'>Produk tidak ditemukan.</div>";
    require_once 'admin_footer.php';
    exit;
}
$produk = $result_produk->fetch_assoc();
$stmt_get->close();
$conn->close();
?>

<div class="form-card">
    <form action="edit_produk.php?id=<?php echo $id_produk; ?>" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="nama_produk">Nama Produk:</label>
            <input type="text" id="nama_produk" name="nama_produk" 
                   value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="deskripsi">Deskripsi Produk:</label>
            <textarea id="deskripsi" name="deskripsi" rows="5"><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="kategori">Kategori Produk:</label>
            <select id="kategori" name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                
                <option value="Makanan" <?php echo ($produk['kategori'] == 'Makanan') ? 'selected' : ''; ?>>
                    Makanan
                </option>
                <option value="Pakaian" <?php echo ($produk['kategori'] == 'Pakaian') ? 'selected' : ''; ?>>
                    Pakaian
                </option>
                <option value="Aksesoris" <?php echo ($produk['kategori'] == 'Aksesoris') ? 'selected' : ''; ?>>
                    Aksesoris
                </option>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group half-width">
                <label for="harga_display">Harga:</label>
                <input type="text" id="harga_display" 
                       onkeyup="formatRupiah(this, 'harga')" 
                       value="Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?>" required>
                
                <input type="hidden" id="harga" name="harga" 
                       value="<?php echo $produk['harga']; ?>">
            </div>
            
            <div class="form-group half-width">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" 
                       value="<?php echo $produk['stok']; ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="berat_gram">Berat (dalam gram):</label>
                <input type="number" id="berat_gram" name="berat_gram" value="<?php echo htmlspecialchars($produk['berat_gram']); ?>" min="1" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Gambar Saat Ini:</label>
            <?php if (!empty($produk['gambar'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                     alt="Gambar Produk" class="current-product-image">
            <?php else: ?>
                <span>(Tidak ada gambar)</span>
            <?php endif; ?>
            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($produk['gambar']); ?>">
        </div>

        <div class="form-group">
            <label for="gambar">Ganti Gambar (Opsional):</label>
            <input type="file" id="gambar" name="gambar" class="file-input" accept="image/png, image/jpeg, image/jpg">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
        
    </form>
</div>

<?php
// 7. Panggil Footer
require_once 'admin_footer.php';
?>