<?php
// 1. Set Judul Halaman
$page_title = "Manajemen Pelanggan";

// 2. Panggil Header
require_once 'admin_header.php';

// 3. Logika PHP: Ambil data pelanggan (role = customer)
// Kita gunakan LEFT JOIN untuk menggabungkan data user dengan data order
// dan GROUP BY untuk menghitung total pesanan dan harga per user.
$query = "
    SELECT 
        u.id, 
        u.nama, 
        u.email, 
        u.created_at, 
        COUNT(o.id) as total_pesanan, 
        SUM(o.total_harga) as total_harga
    FROM 
        users u
    LEFT JOIN 
        orders o ON u.nama = o.nama_pelanggan
    WHERE 
        u.role = 'customer'
    GROUP BY 
        u.id, u.nama, u.email, u.created_at
    ORDER BY 
        u.created_at DESC
";
$result = $conn->query($query);
?>

<div class="table-container-header">
    <h2 class="table-title">Pelanggan</h2>
    <a href="tambah_pelanggan.php" class="btn-aksi btn-tambah">
        <i class="fa-solid fa-plus"></i> Tambah Pelanggan
    </a>
</div>

<div class="table-container">

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead class="table-header-dark">
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Email</th>
                    <th>Tanggal Bergabung</th>
                    <th>Total Pesanan</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    
                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                    
                    <td><?php echo $row['total_pesanan']; ?></td>
                    
                    <td>Rp. <?php echo number_format($row['total_harga'] ?? 0, 0, ',', '.'); ?></td>
                    
                    <td class="aksi-buttons">
                        <a href="riwayat_pelanggan.php?user_id=<?php echo $row['id']; ?>" class="btn-aksi btn-riwayat">Lihat Riwayat</a>
                        
                        <a href="edit_pelanggan.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                        
                        <a href="hapus_pelanggan.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-hapus" 
                           onclick="return confirm('Anda yakin ingin menghapus pelanggan ini? Ini akan menghapus semua riwayat pesanan mereka juga.');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-users"></i>
            <p>Belum ada pelanggan yang terdaftar.</p>
            <a href="tambah_pelanggan.php" class="btn-submit" style="max-width: 300px;">Tambah Pelanggan Baru</a>
        </div>
    <?php endif; ?>
    
    <?php $conn->close(); ?>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>