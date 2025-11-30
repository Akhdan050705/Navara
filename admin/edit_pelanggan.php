<?php
// 1. Set Judul Halaman
$page_title = "Edit Pelanggan";
require_once 'admin_header.php';

$pesan_error = "";
$pesan_sukses = "";

// 2. Cek ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}
$user_id = $_GET['id'];

// 3. Logika POST (Saat menyimpan)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Password baru (opsional)

    // Ambil nama lama (jika nama berubah, kita harus update tabel orders)
    $nama_lama = $_POST['nama_lama'];
    
    if (empty($nama) || empty($email)) {
        $pesan_error = "Nama dan Email tidak boleh kosong.";
    } else {
        // Query dinamis
        $query_sql = "UPDATE users SET nama = ?, email = ?";
        $params_types = "ss";
        $params_values = [$nama, $email];
        
        // Cek jika password diisi
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $pesan_error = "Password baru minimal harus 6 karakter.";
            } else {
                $query_sql .= ", password = ?";
                $params_types .= "s";
                $params_values[] = password_hash($password, PASSWORD_DEFAULT);
            }
        }
        
        $query_sql .= " WHERE id = ?";
        $params_types .= "i";
        $params_values[] = $user_id;

        if (empty($pesan_error)) {
            // Update tabel users
            $stmt = $conn->prepare($query_sql);
            $stmt->bind_param($params_types, ...$params_values);
            
            if ($stmt->execute()) {
                // Jika nama berubah, update juga di tabel orders
                if ($nama !== $nama_lama) {
                    $stmt_update_orders = $conn->prepare("UPDATE orders SET nama_pelanggan = ? WHERE nama_pelanggan = ?");
                    $stmt_update_orders->bind_param("ss", $nama, $nama_lama);
                    $stmt_update_orders->execute();
                    $stmt_update_orders->close();
                }
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Data pelanggan diperbarui!'];
            } else {
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal: Mungkin email sudah digunakan.'];
            }
            $stmt->close();
        }
    }
}

// 4. Logika GET (Ambil data untuk form)
$stmt_get = $conn->prepare("SELECT nama, email FROM users WHERE id = ?");
$stmt_get->bind_param("i", $user_id);
$stmt_get->execute();
$result = $stmt_get->get_result();
if ($result->num_rows === 0) {
    header("Location: pelanggan.php");
    exit;
}
$user = $result->fetch_assoc();
$stmt_get->close();
$conn->close();
?>

<div class="form-card">
    <form action="edit_pelanggan.php?id=<?php echo $user_id; ?>" method="POST">
        <input type="hidden" name="nama_lama" value="<?php echo htmlspecialchars($user['nama']); ?>">

        <div class="form-group">
            <label for="nama">Nama Pelanggan:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password Baru (Opsional):</label>
            <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin ganti">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-submit">Update Pelanggan</button>
        </div>
    </form>
</div>

<?php
// 6. Panggil Footer
require_once 'admin_footer.php';
?>