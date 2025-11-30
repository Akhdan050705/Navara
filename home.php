<?php
// 1. Panggil header
require_once 'user_header.php';

// 2. Query BARU untuk Rekomendasi (Constraint 4: Paling Banyak Dibeli)
// Kita join tabel products dengan order_items, hitung total kuantitas,
// dan urutkan dari yang paling besar.
$result_rekomendasi = $conn->query("
    SELECT 
        p.id, p.nama_produk, p.deskripsi, p.kategori, p.harga, p.stok, p.gambar, p.created_at, p.berat_gram,
        COALESCE(SUM(oi.quantity), 0) as total_terjual
    FROM 
        products p
    LEFT JOIN 
        order_items oi ON p.id = oi.product_id
    GROUP BY 
        p.id, p.nama_produk, p.deskripsi, p.kategori, p.harga, p.stok, p.gambar, p.created_at, p.berat_gram
    ORDER BY 
        total_terjual DESC
    LIMIT 6 
");

// 2b. Query untuk Semua Produk (tanpa LIMIT)
$result_semua_produk = $conn->query("
    SELECT 
        p.id, p.nama_produk, p.deskripsi, p.kategori, p.harga, p.stok, p.gambar, p.created_at, p.berat_gram,
        COALESCE(SUM(oi.quantity), 0) as total_terjual
    FROM 
        products p
    LEFT JOIN 
        order_items oi ON p.id = oi.product_id
    GROUP BY 
        p.id, p.nama_produk, p.deskripsi, p.kategori, p.harga, p.stok, p.gambar, p.created_at, p.berat_gram
    ORDER BY 
        total_terjual DESC
");
?>

<section class="slider-container">
    <div class="slider">
        <img src="images/banner1.jpg" alt="Iklan 1" class="slide active">
        <img src="images/banner2.png" alt="Iklan 2" class="slide">
    </div>
</section>

<section class="filter-bar">
    <span class="filter-label">Cari oleh-oleh favorit:</span>
    <form action="search.php" method="GET" class="filter-form">
        <div class="filter-group">
            <select name="kategori" required>
                <option value="">Semua Kategori</option>
                <option value="Makanan">Makanan</option>
                <option value="Pakaian">Pakaian</option>
                <option value="Aksesoris">Aksesoris</option>
            </select>
        </div>
        <div class="filter-group filter-search">
            <input type="text" name="query" placeholder="Cari nama oleh-oleh...">
        </div>
        <button type="submit" class="btn-cari"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    </form>
</section>

<section class="produk-section">
    <div class="produk-header">
        <h2>Produk Rekomendasi</h2>
        <a href="search.php" class="btn-lihat-lainnya">Produk Lainnya &rarr;</a>
    </div>
    
    <div class="produk-grid">
        <?php if ($result_rekomendasi && $result_rekomendasi->num_rows > 0): ?>
            <?php while ($produk = $result_rekomendasi->fetch_assoc()): ?>
                <div class="produk-card">
                    <div class="produk-image">
                        <img src="uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                    </div>
                    <div class="produk-info">
                        <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                        <p class="harga">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <div class="produk-rating">
                            <span><?php echo (int)$produk['total_terjual']; ?> terjual</span>
                            <span class="rating">
                                <i class="fa-solid fa-star"></i> 5.0
                            </span>
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
            <p style="grid-column: 1 / -1; text-align: center;">Belum ada produk untuk direkomendasikan.</p>
        <?php endif; ?>
    </div>
</section>

<section class="produk-section">
    <div class="produk-header">
        <h2>Semua produk</h2>
        <a href="search.php" class="btn-lihat-lainnya">Produk Lainnya &rarr;</a>
    </div>
    
    <div class="produk-grid">
        <?php if ($result_semua_produk && $result_semua_produk->num_rows > 0): ?>
            <?php while ($produk = $result_semua_produk->fetch_assoc()): ?>
                <div class="produk-card">
                    <div class="produk-image">
                        <img src="uploads/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                    </div>
                    <div class="produk-info">
                        <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                        <p class="harga">Rp. <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <div class="produk-rating">
                            <span><?php echo (int)$produk['total_terjual']; ?> terjual</span>
                            <span class="rating">
                                <i class="fa-solid fa-star"></i> 5.0
                            </span>
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
            <p style="grid-column: 1 / -1; text-align: center;">Belum ada produk untuk direkomendasikan.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// 3. Panggil footer
require_once 'user_footer.php';
$conn->close();
?>