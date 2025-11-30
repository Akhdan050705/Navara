<?php
// 1. Panggil header
$page_title = "Checkout";
require_once 'user_header.php';

// 2. Ambil ID item yang di-POST dari keranjang
if (!isset($_POST['items_to_checkout']) && !isset($_SESSION['checkout_items_ids'])) {
    // Jika tidak ada item (baik dari POST baru atau session lama), tendang kembali
    header("Location: keranjang.php");
    exit;
}

// 3. Ambil ID Item (dari POST atau Session)
$items_to_checkout_ids = $_POST['items_to_checkout'] ?? $_SESSION['checkout_items_ids'];
$_SESSION['checkout_items_ids'] = $items_to_checkout_ids;

$checkout_items = [];
$total_harga_produk = 0;
$total_berat_gram = 0; // <-- Nilai awal di-set 0

$id_string = implode(',', $items_to_checkout_ids);
$result = $conn->query("SELECT * FROM products WHERE id IN ($id_string)");

if ($result->num_rows > 0) {
    while ($produk = $result->fetch_assoc()) {
        $product_id = $produk['id'];
        $quantity = $_SESSION['cart'][$product_id];
        $subtotal = $produk['harga'] * $quantity;
        
        $checkout_items[] = [
            'id' => $product_id,
            'nama' => $produk['nama_produk'],
            'harga' => $produk['harga'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        $total_harga_produk += $subtotal;
        
        // ▼▼▼ PERUBAHAN PENTING DI SINI ▼▼▼
        $total_berat_gram += ($produk['berat_gram'] * $quantity);
    }
} else {
    // Jika data tidak valid, kembali ke keranjang
    unset($_SESSION['checkout_items_ids']);
    header("Location: keranjang.php");
    exit;
}

// ASUMSI: Berat total adalah 1000 gram (1kg).
// Nanti, Anda harus menambahkan kolom 'berat_gram' di tabel 'products'
// dan menghitung total berat di sini.
$total_berat_gram = 1000;


// 4. Ambil data user jika login, untuk pre-fill form
$user_data = ['nama' => '', 'email' => '', 'telepon' => '', 'alamat' => ''];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_result = $conn->query("SELECT nama, email, telepon, alamat FROM users WHERE id = $user_id");
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
    }
}
?>

<section class="content-wrapper" style="margin-top: 25px;">
    
    <form id="checkout-form" method="POST" class="checkout-form-container">        
        <div class="checkout-left-column">
            
            <div class="form-card-checkout">
                <h2>Alamat Pengiriman</h2>
                <div class="form-group">
                    <label for="nama_pelanggan">Nama Penerima</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="<?php echo htmlspecialchars($user_data['nama'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telepon">Nomor Telepon</label>
                    <input type="tel" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user_data['telepon'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="provinsi">Provinsi Tujuan</label>
                    <select id="provinsi" name="provinsi" required>
                        <option value="">Memuat provinsi...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kota">Kota/Kabupaten Tujuan</label>
                    <select id="kota" name="kota" required disabled>
                        <option value="">Pilih provinsi terlebih dahulu</option>
                    </select>
                    <input type="hidden" name="id_kota_tujuan" id="id_kota_tujuan">
                </div>
                <div class="form-group">
                    <label for="kecamatan">Kecamatan Tujuan</label>
                    <select id="kecamatan" name="kecamatan" required disabled>
                        <option value="">Pilih kota terlebih dahulu</option>
                    </select>
                    <input type="hidden" name="id_kecamatan_tujuan" id="id_kecamatan_tujuan">
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat Lengkap (Jalan, No. Rumah, RT/RW)</label>
                    <textarea id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-card-checkout">
                <h2>Opsi Pengiriman</h2>
                <input type="hidden" id="berat_gram" value="<?php echo $total_berat_gram; ?>">
                
                <div class="form-group">
                    <label for="kurir">Pilih Ekspedisi</label>
                    <select id="kurir" name="kurir" required disabled>
                        <option value="">Pilih kota terlebih dahulu</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="paket_layanan">Pilih Paket Layanan</label>
                    <select id="paket_layanan" name="paket_layanan" required disabled>
                        <option value="">Pilih ekspedisi terlebih dahulu</option>
                    </select>
                    <input type="hidden" name="nama_paket_layanan" id="nama_paket_layanan">
                    <input type="hidden" name="ongkos_kirim" id="ongkos_kirim" value="0">
                </div>
            </div>

        </div>

        <div class="checkout-right-column">
            <div class="summary-card-checkout">
                <h2>Ringkasan Pesanan</h2>
                <div class="item-summary-list">
                    <?php foreach ($checkout_items as $item): ?>
                    <div class="item-summary">
                        <span><?php echo htmlspecialchars($item['nama']); ?> (x<?php echo $item['quantity']; ?>)</span>
                        <strong>Rp. <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
                <hr class="divider">
                
                <div class="total-summary-group">
                    <div class="total-summary-row">
                        <span>Subtotal Produk</span>
                        <span id="subtotal_produk_display">Rp. <?php echo number_format($total_harga_produk, 0, ',', '.'); ?></span>
                    </div>
                    <div class="total-summary-row">
                        <span>Biaya Pengiriman</span>
                        <span id="ongkir_display">Rp. 0</span>
                    </div>
                    <div class="total-summary-row final-total">
                        <strong>Total Pembayaran</strong>
                        <strong id="total_bayar_display">Rp. <?php echo number_format($total_harga_produk, 0, ',', '.'); ?></strong>
                    </div>
                </div>
                
                <h2 style="margin-top: 20px;">Metode Pembayaran</h2>
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="e-wallet" checked> E-Wallet (Midtrans)
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="transfer_bank"> Transfer Bank (Midtrans)
                    </label>
                </div>
                
                <button type="submit" id="btn_konfirmasi" class="btn-checkout" style="width: 100%; margin-top: 20px; padding: 15px; font-size: 1.1rem;" disabled>
                    Pilih Layanan Pengiriman
                </button>
            </div>
        </div>
        
        <input type="hidden" id="total_harga_produk" value="<?php echo $total_harga_produk; ?>">
    </form>
</section>

<?php
// 5. Panggil footer
$conn->close();
require_once 'user_footer.php';
?>