<?php
// 1. Panggil header pengguna
require_once 'user_header.php';

// 2. Ambil nilai dari URL (default string kosong jika tidak ada)
$search_query = $_GET['query'] ?? '';
$kategori_query = $_GET['kategori'] ?? '';

// 3. Logika Filter BARU
$sql = "SELECT * FROM products WHERE 1=1"; // Query dasar
$params_types = "";
$params_values = [];

// Tambahkan filter KATEGORI jika dipilih
if (!empty($kategori_query)) {
    $sql .= " AND kategori = ?";
    $params_types .= "s";
    $params_values[] = $kategori_query;
}

// Tambahkan filter NAMA/DESKRIPSI jika diisi
if (!empty($search_query)) {
    $sql .= " AND (nama_produk LIKE ? OR deskripsi LIKE ?)";
    $params_types .= "ss";
    $search_term = "%" . $search_query . "%";
    $params_values[] = $search_term;
    $params_values[] = $search_term;
}

$sql .= " ORDER BY created_at DESC";

// 4. Eksekusi query
// Cek apakah ada parameter yang perlu di-bind
if (!empty($params_values)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($params_types, ...$params_values);
    $stmt->execute();
    $result_produk = $stmt->get_result();
} else {
    // Jika tidak ada filter (kategori kosong DAN search kosong),
    // jalankan query dasar untuk tampilkan semua
    $result_produk = $conn->query($sql);
}

// 5. Tentukan Judul Halaman
$page_title = "Semua Produk"; // Default
if (!empty($search_query)) {
    $page_title = "Hasil untuk \"" . htmlspecialchars($search_query) . "\"";
} elseif (!empty($kategori_query)) {
    $page_title = "Kategori: " . htmlspecialchars($kategori_query);
}
?>

<section class="produk-section" style="margin-top: 30px;">
    
    <div class="produk-header">
        <h2><?php echo $page_title; ?></h2>
    </div>

    <div class="produk-grid">
        
        <?php if ($result_produk && $result_produk->num_rows > 0): ?>
            <?php while ($produk = $result_produk->fetch_assoc()): ?>
            <div class="produk-card">
                <div class="produk-image">
                    <img src="uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                </div>
                <div class="produk-info">
                    <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                    <p class="harga">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <div class="produk-rating">
                        <span>★ 5.0</span>
                    </div>
                    <div class="produk-actions">
                        <form action="handle_cart.php" method="POST" class="form-keranjang">
                            <input type="hidden" name="product_id" value="<?php echo $produk['id']; ?>">
                            <button type="submit" class="btn-keranjang"><i class="fa-solid fa-cart-plus"></i></button>
                        </form>
                        <a href="#" class="btn-checkout">Checkout</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #777;">
                Maaf, tidak ada produk yang ditemukan.
            </p>
        <?php endif; ?>
    </div>
</section>

<?php
// 6. Panggil footer
if (isset($stmt)) $stmt->close();
$conn->close();
require_once 'user_footer.php';
?>