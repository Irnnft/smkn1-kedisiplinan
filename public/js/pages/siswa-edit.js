$(document).ready(function() {
    
    // 1. Inisialisasi Select2
    if (jQuery().select2) {
        $('.select2').select2({
            theme: 'classic',
            allowClear: true,
            width: '100%'
        });
    }

    // 2. Input Validation (Hanya Angka)
    // Mencegah user mengetik huruf di field nomor
    $('input[name="nomor_hp_ortu"], input[name="nisn"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // 3. SweetAlert Confirmation (Opsional - jika pakai SweetAlert)
    // Memberikan feedback saat form di-submit
    $('form').on('submit', function() {
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    });

});