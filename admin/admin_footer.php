</main> 
<script src="../script.js"></script>

<script>
    /**
     * Fungsi untuk memformat input menjadi format Rupiah
     * @param {HTMLInputElement} inputElement - Input yang terlihat (this)
     * @param {string} hiddenInputId - ID dari input tersembunyi
     */
    function formatRupiah(inputElement, hiddenInputId) {
        let value = inputElement.value;
        let hiddenInput = document.getElementById(hiddenInputId);
        
        // 1. Bersihkan nilai dari semua karakter non-angka
        let angka = value.replace(/[^\d]/g, '');
        
        // 2. Simpan nilai angka bersih ke input tersembunyi
        hiddenInput.value = angka;

        // 3. Jika input kosong, biarkan kosong
        if (angka === "") {
            inputElement.value = "";
            return;
        }

        // 4. Buat format Rupiah (e.g., "1.000.000")
        let formatted = new Intl.NumberFormat('id-ID').format(angka);
        
        // 5. Tampilkan format Rupiah di input yang terlihat
        inputElement.value = "Rp. " + formatted;
    }
</script>
<?php
    // Cek apakah ada notifikasi di session
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        
        // Tampilkan SweetAlert menggunakan JavaScript
        echo "<script>
                Swal.fire({
                    icon: '{$alert['type']}',
                    title: '{$alert['message']}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
              </script>";
        
        // Hapus notifikasi dari session agar tidak muncul lagi
        unset($_SESSION['alert']);
    }
    ?>
</body>
</html>