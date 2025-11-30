// Nama File: app.js

document.addEventListener('DOMContentLoaded', function() {
    
    const slider = document.querySelector('.slider');
    // Cek apakah slider ada di halaman ini
    if (slider) {
        const slides = slider.querySelectorAll('.slide');
        let currentSlide = 0;

        // Tampilkan slide pertama
        slides[currentSlide].classList.add('active');

        function nextSlide() {
            // Sembunyikan slide saat ini
            slides[currentSlide].classList.remove('active');
            
            // Pindah ke slide berikutnya
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Tampilkan slide baru
            slides[currentSlide].classList.add('active');
        }

        // Atur interval untuk ganti slide otomatis setiap 5 detik
        setInterval(nextSlide, 5000); // 5000ms = 5 detik
    }

});