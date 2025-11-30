<?php
    // Ambil role dari URL
    $role = $_GET['role'] ?? 'customer';
    $title = ($role === 'admin') ? 'LOGIN AS ADMIN' : 'LOGIN AS CUSTOMER';

    // Siapkan pesan
    $message = '';
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        $message = '<p class="error-message">Email atau password salah.</p>';
    }
    if (isset($_GET['error']) && $_GET['error'] == 'auth') {
        $message = '<p class="error-message">Anda harus login untuk mengakses halaman itu.</p>';
    }
    if (isset($_GET['status']) && $_GET['status'] == 'registered') {
        $message = '<p class="success-message">Registrasi berhasil! Silakan login.</p>';
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Navara Oleh-Oleh</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-container">
        <a href="index.php" class="logo-login">
            <img src="images/logo.png" alt="Navara Oleh-Oleh Logo">
        </a>

        <div class="login-box">
            <h2><?php echo $title; ?></h2>
            
            <?php echo $message; ?>
            
            <form action="handle_login.php" method="POST">
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email Anda" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="btn btn-login">LOGIN</button>
            </form>
            
            <p class="register-link">
                Belum punya akun? <a href="register.php">Register</a>
            </p>
        </div>
    </div>

</body>
</html>