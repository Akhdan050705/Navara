<?php
// 1. Set Judul Halaman
$page_title = "Pesanan Saya";

// 2. Panggil Header
require_once 'admin_header.php';

// 3. Logika PHP: Ambil semua data pesanan
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>

<div class="table-container-header">
    <h2 class="table-title">Pesanan Terbaru</h2>
    <a href="tambah_pesanan.php" class="btn-aksi btn-tambah">
        <i class="fa-solid fa-plus"></i> Tambah Pesanan
    </a>
</div>

<div class="table-container">

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead class="table-header-dark">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong>ORD-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                    
                    <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                    
                    <td>Rp. <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    
                    <td>
                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    
                    <td class="aksi-buttons">
                        <a href="edit_pesanan.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                        
                        <a href="hapus_pesanan.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-hapus" 
                           onclick="return confirm('Anda yakin ingin menghapus pesanan ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-receipt"></i>
            <p>Belum ada pesanan yang masuk.</p>
        </div>
    <?php endif; ?>
    
    <?php $conn->close(); ?>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>