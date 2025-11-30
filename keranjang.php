<?php
// 1. Panggil header
$page_title = "Keranjang";
require_once 'user_header.php';

$cart_items = [];
$total_harga_cart = 0;

// 2. Cek jika keranjang ada & tidak kosong
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $id_string = implode(',', $product_ids);
    
    // 3. Ambil data produk dari DB
    $result = $conn->query("SELECT * FROM products WHERE id IN ($id_string)");
    
    if ($result && $result->num_rows > 0) {
        while ($produk = $result->fetch_assoc()) {
            $product_id = $produk['id'];
            $quantity = $_SESSION['cart'][$product_id];
            $subtotal = $produk['harga'] * $quantity;
            
            $cart_items[] = [
                'id' => $product_id,
                'nama' => $produk['nama_produk'],
                'gambar' => $produk['gambar'],
                'harga' => $produk['harga'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
            $total_harga_cart += $subtotal;
        }
    } else {
        // Jika produk di keranjang sudah dihapus dari DB
        unset($_SESSION['cart']);
    }
}
?>

<section class="content-wrapper" style="margin-top: 25px;">

    <div class="home-header">
        <h1><i class="fa-solid fa-cart-shopping"></i> Keranjang Saya</h1>
    </div>

    <form action="checkout.php" method="POST">
    
        <div class="table-container">
            <table>
                <thead class="table-header-dark">
                    <tr>
                        <th style="width: 5%;"><input type="checkbox" id="pilih_semua"></th>
                        <th colspan="2">Produk</th>
                        <th>Harga</th>
                        <th>Kuantitas</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cart_items)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 50px;">
                                <p>Keranjang Anda kosong.</p>
                                <a href="home.php" class="btn-checkout" style="max-width: 300px; display: inline-block; margin-top: 20px;">Mulai Belanja</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="cek_item" name="items_to_checkout[]" value="<?php echo $item['id']; ?>">
                            </td>
                            <td style="width: 100px;">
                                <img src="uploads/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" class="product-image-thumbnail">
                            </td>
                            <td><?php echo htmlspecialchars($item['nama']); ?></td>
                            <td>Rp. <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php echo $item['quantity']; ?>
                                </td>
                            <td><strong>Rp. <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></strong></td>
                            <td>
                                <a href="#" class="btn-aksi btn-hapus">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($cart_items)): ?>
        <div class="checkout-footer-bar">
            <span>Total Harga Keranjang: <strong>Rp. <?php echo number_format($total_harga_cart, 0, ',', '.'); ?></strong></span>
            <button type="submit" class="btn-checkout">Checkout Item Terpilih</button>
        </div>
        <?php endif; ?>
    </form>
</section>

<?php
// 4. Panggil footer
$conn->close();
require_once 'user_footer.php';
?>
<script>
// (Opsional) JS untuk centang semua
document.getElementById('pilih_semua').onclick = function() {
    var checkboxes = document.querySelectorAll('.cek_item');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>