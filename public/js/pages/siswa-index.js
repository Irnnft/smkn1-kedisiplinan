document.addEventListener('DOMContentLoaded', function() {
    
    // 1. STICKY HEADER LOGIC
    const stickyFilter = document.getElementById('stickyFilter');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 20) {
            stickyFilter.classList.add('compact-mode');
            stickyFilter.classList.add('header-hidden'); 
        } else {
            stickyFilter.classList.remove('compact-mode');
            stickyFilter.classList.remove('header-hidden'); 
        }
    });

    // 2. LIVE SEARCH & AUTO SUBMIT LOGIC
    let timeout = null;
    const searchInput = document.getElementById('liveSearch');
    const form = document.getElementById('filterForm');

    if(searchInput){
        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                form.submit(); 
            }, 800); // Debounce 800ms
        });

        // Restore focus & cursor position
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('cari')) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    }
});