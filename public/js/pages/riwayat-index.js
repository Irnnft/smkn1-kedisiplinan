document.addEventListener('DOMContentLoaded', function() {
    
    // ==================================================
    // 1. LOGIC STICKY & SHRINK FILTER (Filter Melayang)
    // ==================================================
    const stickyFilter = document.getElementById('stickyFilter');
    
    // Event listener saat window discroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 20) {
            // Jika scroll ke bawah > 20px, ubah ke mode compact
            stickyFilter.classList.add('compact-mode');
            stickyFilter.classList.add('header-hidden'); 
        } else {
            // Jika kembali ke atas, kembalikan ke tampilan penuh
            stickyFilter.classList.remove('compact-mode');
            stickyFilter.classList.remove('header-hidden'); 
        }
    });

    // ==================================================
    // 2. LOGIC LIVE SEARCH (Auto Submit)
    // ==================================================
    let timeout = null;
    const searchInput = document.getElementById('liveSearch');
    const form = document.getElementById('filterForm');

    if(searchInput){
        // Event saat mengetik (keyup)
        searchInput.addEventListener('keyup', function() {
            // Reset timer sebelumnya
            clearTimeout(timeout);
            
            // Set timer baru (Debounce 800ms)
            // Artinya: Tunggu user berhenti mengetik selama 0.8 detik baru submit
            timeout = setTimeout(function () {
                form.submit(); 
            }, 800);
        });

        // Fokus kembali ke input setelah halaman reload
        // Ini memberikan efek "Seamless" seolah-olah halaman tidak reload
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('cari_siswa')) {
            searchInput.focus();
            const val = searchInput.value;
            // Trik memindahkan kursor ke akhir teks
            searchInput.value = '';
            searchInput.value = val;
        }
    }
});