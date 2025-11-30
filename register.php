<?php
require_once 'config.php'; // Hubungkan ke config
$error = ''; // Variabel untuk menyimpan pesan error

// Cek apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi sederhana
    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // Cek apakah email sudah terdaftar
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            // Email aman, lanjutkan registrasi
            // HASH password sebelum disimpan!
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Masukkan ke database (role default adalah 'customer')
            $stmt_insert = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt_insert->bind_param("sss", $nama, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                // Registrasi berhasil, arahkan ke halaman login
                header("Location: login.php?status=registered");
                exit;
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Navara Oleh-Oleh</title>
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
            <h2>REGISTER AKUN BARU</h2>
            
            <?php
            // Tampilkan pesan error jika ada
            if (!empty($error)) {
                echo '<p class="error-message">' . $error . '</p>';
            }
            ?>
            
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Nama Anda" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email Anda" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi Password" required>
                </div>
                
                <button type="submit" class="btn btn-login">REGISTER</button>
            </form>
            
            <p class="register-link">
                Sudah punya akun? <a href="login.php">Login</a>
            </p>
        </div>
    </div>

</body>
</html>