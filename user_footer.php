</main> <footer class="user-footer-new">
        <div class="footer-content">
            
            <div class="footer-column about">
                <h3>Navara Oleh-Oleh</h3>
                <p>Navara Oleh-Oleh adalah platform terkurasi untuk menemukan dan membeli produk khas Sumatra Barat terbaik, langsung dari pengrajin lokal.</p>
                </div>
            
            <div class="footer-column links">
                <h3>Hubungi Kami</h3>
                <p>Jl. Raya Padang No. 123
                   <br>Padang, Sumatra Barat
                   <br>Indonesia
                </p>
                <br>
                <p><strong>Email:</strong><br> customercare@navara.com</p>
                <p><strong>Telepon:</strong><br> (021) 123-4567</p>
            </div>
            
            <div class="footer-column social">
                <h3>Ikuti Kami</h3>
                <a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                <a href="#"><i class="fa-brands fa-youtube"></i> YouTube</a>
            </div>
            
            <div class="footer-column payment">
                <h3>Metode Pembayaran</h3>
                <p>Semua transaksi aman dan terenkripsi. Kami menerima:</p>
                <img src="images/payment-logos.png" alt="Metode Pembayaran (BCA, DANA, dll)" class="footer-payment-logo">
            </div>
            
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Navara Oleh-Oleh. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="<?php require_once 'config.php'; echo MIDTRANS_CLIENT_KEY; ?>">
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="app.js"></script>
    
    <script>
    
    /**
     * Fungsi untuk memformat input menjadi format Rupiah
     */
    function formatRupiah(inputElement, hiddenInputId) {
        let value = inputElement.value;
        let hiddenInput = document.getElementById(hiddenInputId);
        let angka = value.replace(/[^\d]/g, '');
        hiddenInput.value = angka;
        if (angka === "") {
            inputElement.value = "";
            return;
        }
        let formatted = new Intl.NumberFormat('id-ID').format(angka);
        inputElement.value = "Rp. " + formatted;
    }

    // Pastikan skrip ini hanya berjalan jika kita ada di halaman checkout
    if ($('#provinsi').length > 0) {
        
        $(document).ready(function() {
            // 1. Ambil data Provinsi saat halaman dimuat
            $.ajax({
                url: 'komerce_handler.php?action=get_provinsi',
                method: 'GET',
                success: function(response) {
                    try {
                        let data = JSON.parse(response);
                        // Sesuaikan dengan struktur JSON Komerce
                        let results = data.data; 
                        let options = '<option value="">-- Pilih Provinsi --</option>';
                        results.forEach(function(prov) {
                            options += `<option value="${prov.id}">${prov.name}</option>`; 
                        });
                        $('#provinsi').html(options).prop('disabled', false);
                    } catch (e) {
                        console.error("Gagal parsing JSON Provinsi:", response);
                        $('#provinsi').html('<option value="">Gagal memuat data</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error AJAX Provinsi:", error);
                    $('#provinsi').html('<option value="">Gagal memuat data</option>');
                }
            });

            // 2. Saat Provinsi diubah
            $('#provinsi').on('change', function() {
                let province_id = $(this).val();
                resetKota();
                resetKurir();
                resetLayanan();
                
                if (province_id) {
                    $('#kota').html('<option value="">Memuat kota...</option>').prop('disabled', false);
                    $.ajax({
                        url: `komerce_handler.php?action=get_kota&province_id=${province_id}`,
                        method: 'GET',
                        success: function(response) {
                            try {
                                let data = JSON.parse(response);
                                let results = data.data; 
                                let options = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                                results.forEach(function(kota) {
                                    // PERBAIKAN: Gunakan 'name' saja, karena 'type' tidak terjamin ada
                                    options += `<option value="${kota.id}">${kota.name}</option>`; 
                                });
                                $('#kota').html(options);
                            } catch (e) {
                                console.error("Gagal parsing JSON Kota:", response);
                                $('#kota').html('<option value="">Gagal memuat data</option>');
                            }
                        }
                    });
                }
            });

           // 3. ▼▼▼ LOGIKA BARU: Saat Kota diubah ▼▼▼
            $('#kota').on('change', function() {
                let city_id = $(this).val();
                resetKecamatan();
                resetKurir();
                resetLayanan();
                
                if (city_id) {
                    $('#kecamatan').html('<option value="">Memuat kecamatan...</option>').prop('disabled', false);
                    $.ajax({
                        // Panggil action 'get_district' yang baru
                        url: `komerce_handler.php?action=get_district&city_id=${city_id}`,
                        method: 'GET',
                        success: function(response) {
                            try {
                                let data = JSON.parse(response);
                                let results = data.data; 
                                let options = '<option value="">-- Pilih Kecamatan --</option>';
                                results.forEach(function(kec) {
                                    options += `<option value="${kec.id}">${kec.name}</option>`; 
                                });
                                $('#kecamatan').html(options);
                            } catch (e) {
                                $('#kecamatan').html('<option value="">Gagal memuat data</option>');
                            }
                        }
                    });
                }
            });

            // 4. ▼▼▼ LOGIKA DIPINDAH: Saat Kecamatan diubah ▼▼▼
            // (Sebelumnya ini adalah 'kota', sekarang 'kecamatan')
            $('#kecamatan').on('change', function() {
                let district_id = $(this).val();
                // Simpan ID kecamatan di input hidden
                $('#id_kecamatan_tujuan').val(district_id); 
                resetKurir();
                resetLayanan();
                if (district_id) {
                    // Aktifkan dropdown kurir
                    $('#kurir').prop('disabled', false);
                }
            });
            
            // 5. Saat Kurir diubah (Logika AJAX diubah)
            $('#kurir').on('change', function() {
                let courier = $(this).val();
                // Ambil ID KECAMATAN, bukan kota
                let district_id = $('#id_kecamatan_tujuan').val(); 
                let weight = $('#berat_gram').val();
                resetLayanan();
                
                if (courier && district_id) {
                    $('#paket_layanan').html('<option value="">Mencari layanan...</option>').prop('disabled', false);
                    $.ajax({
                        url: 'komerce_handler.php?action=get_ongkir',
                        method: 'POST',
                        data: {
                            district_id: district_id, // <-- Kirim ID Kecamatan
                            courier: courier,
                            weight: weight
                        },
                        success: function(response) {
                            try {
                                let data = JSON.parse(response);
                                if (data.meta && data.meta.code != 200) {
                                    throw new Error(data.meta.message);
                                }

                                // ▼▼▼ PERBAIKAN PARSING JSON (Sesuai image_fd2d49.png) ▼▼▼
                                let results = data.data; // 'data' adalah array berisi ongkir
                                let options = '<option value="">-- Pilih Paket Layanan --</option>';
                                
                                results.forEach(function(pkg) {
                                    // Ambil data langsung dari 'pkg'
                                    let price = pkg.cost;
                                    let etd = pkg.etd;
                                    let service = pkg.service;
                                    let desc = pkg.description;
                                    
                                    options += `<option value="${price}" data-nama-paket="${service} (${desc})">
                                                    ${service} - Rp. ${price.toLocaleString('id-ID')} (Est. ${etd})
                                              </option>`;
                                });
                                // ▲▲▲ AKHIR PERBAIKAN PARSING ▲▲▲
                                
                                $('#paket_layanan').html(options);
                            } catch (e) {
                                console.error("Gagal parsing JSON Ongkir:", response);
                                $('#paket_layanan').html('<option value="">Layanan tidak ditemukan</option>');
                            }
                        }
                    });
                }
            });
            
            // 5. Saat Paket Layanan diubah
            $('#paket_layanan').on('change', function() {
                let ongkir = $(this).val();
                if (!ongkir) {
                    resetLayanan();
                    return;
                }
                let nama_paket = $(this).find('option:selected').data('nama-paket');
                let total_produk = parseInt($('#total_harga_produk').val());
                let total_bayar = total_produk + parseInt(ongkir);
                $('#ongkos_kirim').val(ongkir);
                $('#nama_paket_layanan').val(nama_paket);
                $('#ongkir_display').text(`Rp. ${parseInt(ongkir).toLocaleString('id-ID')}`);
                $('#total_bayar_display').text(`Rp. ${total_bayar.toLocaleString('id-ID')}`);
                $('#btn_konfirmasi').prop('disabled', false).text('Konfirmasi Pesanan');
            });
        });
        
        // Fungsi reset
        function resetKota() {
            $('#kota').html('<option value="">Pilih provinsi terlebih dahulu</option>').prop('disabled', true);
        }
        
        // ▼▼▼ TAMBAHKAN FUNGSI RESET BARU ▼▼▼
        function resetKecamatan() {
            $('#kecamatan').html('<option value="">Pilih kota terlebih dahulu</option>').prop('disabled', true);
            $('#id_kecamatan_tujuan').val('');
        }
        
        function resetKurir() {
            $('#kurir').val('').prop('disabled', true);
        }
        
        function resetLayanan() {
            $('#paket_layanan').html('<option value="">Pilih ekspedisi terlebih dahulu</option>').prop('disabled', true);
            $('#ongkos_kirim').val(0);
            $('#ongkir_display').text('Rp. 0');
            let total_produk = parseInt($('#total_harga_produk').val());
            $('#total_bayar_display').text(`Rp. ${total_produk.toLocaleString('id-ID')}`);
            $('#btn_konfirmasi').prop('disabled', true).text('Pilih Layanan Pengiriman');
        }
        $('#checkout-form').on('submit', function(e) {
            e.preventDefault(); // Hentikan form submit biasa
            
            var submitButton = $('#btn_konfirmasi');
            submitButton.prop('disabled', true).text('Memproses...'); // Matikan tombol

            // Kirim semua data form ke handle_checkout.php
            $.ajax({
                url: 'handle_checkout.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    try {
                        let data = JSON.parse(response);
                        
                        if (data.token) {
                            // Jika sukses dapat token, buka pop-up Midtrans
                            snap.pay(data.token, {
                                onSuccess: function(result){
                                    // Redirect ke halaman sukses
                                    window.location.href = 'pesanan_sukses.php?order_id=' + result.order_id;
                                },
                                onPending: function(result){
                                    // Redirect ke halaman pesanan (jika pending)
                                    window.location.href = 'pesanan_saya.php';
                                },
                                onError: function(result){
                                    alert('Pembayaran Gagal. Silakan coba lagi.');
                                    submitButton.prop('disabled', false).text('Konfirmasi Pesanan');
                                },
                                onClose: function(){
                                    alert('Anda menutup pop-up pembayaran.');
                                    submitButton.prop('disabled', false).text('Konfirmasi Pesanan');
                                }
                            });
                        } else {
                            // Jika backend kirim error
                            alert(data.message || 'Terjadi kesalahan.');
                            submitButton.prop('disabled', false).text('Konfirmasi Pesanan');
                        }
                    } catch (err) {
                        alert('Gagal memproses pesanan. Periksa konsol.');
                        console.error("Gagal parsing JSON:", response);
                        submitButton.prop('disabled', false).text('Konfirmasi Pesanan');
                    }
                },
                error: function(xhr) {
                    alert('Error terhubung ke server. Silakan coba lagi.');
                    console.error(xhr.responseText);
                    submitButton.prop('disabled', false).text('Konfirmasi Pesanan');
                }
            });
        });
    }
    
    </script>

</body>
</html>