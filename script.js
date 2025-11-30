// Nama File: script.js

// Menunggu semua konten halaman dimuat
document.addEventListener('DOMContentLoaded', () => {

    // === LOGIKA UNTUK LANDING PAGE (index.php) ===
    const welcomePage = document.querySelector('.welcome-page');
    const clickToStart = document.getElementById('click-to-start');
    const roleSelection = document.getElementById('role-selection');

    // Cek apakah elemen-elemen ini ada di halaman
    if (welcomePage && clickToStart && roleSelection) {
        
        // Tambahkan event listener ke seluruh body halaman
        welcomePage.addEventListener('click', () => {
            
            // 1. Hilangkan teks "Click anywhere" dengan animasi fade-out
            clickToStart.style.opacity = '0';
            
            // 2. Setelah animasi selesai, sembunyikan (display:none)
            setTimeout(() => {
                clickToStart.classList.add('hidden');
                
                // 3. Tampilkan tombol (hapus .hidden)
                roleSelection.classList.remove('hidden');
                
                // 4. Memicu animasi fade-in (di-handle oleh CSS)
                // Kita butuh delay sedikit agar transisi 'display' selesai
                setTimeout(() => {
                    roleSelection.style.opacity = '1';
                }, 50); // delay 50ms

            }, 500); // 500ms = durasi transisi di CSS

        }, { once: true }); // {once: true} agar event ini hanya berjalan sekali
    }

    // === LOGIKA UNTUK DASHBOARD (dashboard.php) ===
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');

    // Cek apakah elemen sidebar ada
    if (sidebar && sidebarToggle) {
        
        sidebarToggle.addEventListener('click', () => {
            // Tambahkan atau hapus class 'collapsed' pada sidebar
            sidebar.classList.toggle('collapsed');
        });
    }

});