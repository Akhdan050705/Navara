<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navara Oleh-Oleh</title>
    
    <link rel="stylesheet" href="main.css"> 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="user-body">

    <header class="user-header">
        <div class="header-container">
            <a href="home.php" class="user-logo">
                <img src="images/logo.png" alt="Navara Oleh-Oleh Logo">
            </a>
            <nav class="user-nav">
                <a href="home.php" class="nav-link <?php echo ($page_title == 'Home') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
                <a href="keranjang.php" class="nav-link <?php echo ($page_title == 'Keranjang') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Keranjang</span>
                </a>
                <a href="pesanan_saya.php" class="nav-link <?php echo ($page_title == 'Pesanan Saya') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Pesanan Saya</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="main-container">