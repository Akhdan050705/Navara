<?php
// 1. Set Judul Halaman
$page_title = "Tambah Pelanggan Baru";

// 2. Panggil Header
require_once 'admin_header.php';

$pesan_error = "";
$pesan_sukses = "";

// 3. Logika PHP (Handle Form Submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($nama) || empty($email) || empty($password)) {
        $pesan_error = "Semua field wajib diisi.";
    } elseif (strlen($password) < 6) {
        $pesan_error = "Password minimal harus 6 karakter.";
    } else {
        // Cek apakah email sudah ada
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $pesan_error = "Email sudah terdaftar. Gunakan email lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Masukkan ke database
            $stmt_insert = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt_insert->bind_param("sss", $nama, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Pelanggan baru ditambahkan!'];
                header("Location: pelanggan.php"); // Redirect ke halaman daftar pelanggan
                exit; // Wajib ada
            } else {
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal: ' . $stmt_insert->error];
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
$conn->close();
?>

<div class="form-card">
    <form action="tambah_pelanggan.php" method="POST">
        <div class="form-group">
            <label for="nama">Nama Pelanggan:</label>
            <input type="text" id="nama" name="nama" placeholder="Nama lengkap pelanggan" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="email@example.com" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn-submit">Simpan Pelanggan</button>
        </div>
    </form>
</div>

<?php
// 5. Panggil Footer
require_once 'admin_footer.php';
?>