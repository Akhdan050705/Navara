<?php
require_once '../config.php';

// --- KEAMANAN ---
// Cek apakah pengguna sudah login DAN apakah rolenya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?role=admin&error=auth");
    exit;
}

// Ambil nama admin dari session untuk sapaan "Welcome"
$admin_nama = htmlspecialchars($_SESSION['nama']);

// $page_title akan di-set oleh file yang memanggil header ini
// (Contoh: $page_title = "Admin Dashboard";)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel'; ?> - Navara Oleh-Oleh</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="dashboard-body">

    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo-dashboard">
                <img src="../images/logo.png" alt="Navara Oleh-Oleh Logo">
            </a>
        </div>

        <ul class="sidebar-nav">
            <li>
                <a href="dashboard.php" class="<?php echo ($page_title == 'Admin Dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="tambah_produk.php" class="<?php echo ($page_title == 'Tambah Produk Baru') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus"></i> <span>Tambah Produk Baru</span>
                </a>
            </li>
            <li>
                <a href="lihat_produk.php" class="<?php echo ($page_title == 'Lihat Produk') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box-open"></i> <span>Lihat Produk</span>
                </a>
            </li>
            <li>
                <a href="pesanan.php" class="<?php echo ($page_title == 'Pesanan') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-receipt"></i> <span>Pesanan</span>
                </a>
            </li>
            <li>
                <a href="pelanggan.php" class="<?php echo ($page_title == 'Pelanggan' || str_starts_with($page_title, 'Riwayat')) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> <span>Pelanggan</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span></a>
            <button class="sidebar-toggle" id="sidebar-toggle">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>
    </nav>

    <main class="main-content">
        <header class="main-header">
            <h1 class="header-title"><?php echo $page_title ?? 'Admin Panel'; ?></h1>
            <span class="header-welcome">Welcome, <?php echo $admin_nama; ?></span>
        </header>
        
        
</body>
</html>