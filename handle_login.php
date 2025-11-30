<?php
require_once 'config.php'; // Hubungkan ke config

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_login = $_POST['role']; // 'admin' atau 'customer'

    // 1. Ambil data user dari database berdasarkan email
    $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 2. Verifikasi password yang di-hash
        // 3. Verifikasi apakah role-nya sesuai dengan yang dipilih di form login
        if (password_verify($password, $user['password']) && $user['role'] === $role_login) {
            
            // Login sukses! Simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            
            // 4. Arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } else {
                // Nanti bisa diarahkan ke halaman utama customer
                header("Location: home.php"); 
                exit;
            }
            
        } else {
            // Password salah atau role tidak sesuai
            header("Location: login.php?role=" . $role_login . "&error=1");
            exit;
        }
        
    } else {
        // Email tidak ditemukan
        header("Location: login.php?role=" . $role_login . "&error=1");
        exit;
    }

    $stmt->close();
    $conn->close();
    
} else {
    // Jika diakses langsung, tendang ke index
    header("Location: index.php");
    exit;
}
?>