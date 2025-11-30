<?php
// 1. Set Judul Halaman
$page_title = "Lihat Produk";

// 2. Panggil Header
require_once 'admin_header.php';

// 3. Logika PHP: Ambil semua data produk dari database
// Kita urutkan berdasarkan yang paling baru dibuat
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// (Koneksi $conn sudah ada dari admin_header.php)
?>

<div class="table-container">

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead class="table-header-dark">
                <tr>
                    <th>Code</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong>Pr-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                    
                    <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                    
                    <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                    
                    <td>Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    
                    <td><?php echo htmlspecialchars($row['stok']); ?></td>
                    
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['gambar']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" 
                                 class="product-image-thumbnail">
                        <?php else: ?>
                            <span>(No Image)</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="aksi-buttons">
                        <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                        
                        <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-hapus" 
                           onclick="return confirm('Anda yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-box-open"></i>
            <p>Belum ada produk yang ditambahkan.</p>
            <a href="tambah_produk.php" class="btn-submit" style="max-width: 300px;">Tambah Produk Pertama Anda</a>
        </div>
    <?php endif; ?>
    
    <?php $conn->close(); ?>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>