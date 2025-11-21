$(document).ready(function() {
    
    // 1. Inisialisasi Select2 Dinamis
    // Sekarang script akan mengecek atribut 'data-placeholder' di HTML
    if (jQuery().select2) {
        $('.select2').each(function() {
            $(this).select2({
                theme: 'classic',
                // Ambil teks placeholder dari atribut HTML data-placeholder
                // Jika tidak ada, fallback ke default kosong
                placeholder: $(this).data('placeholder') || "-- Pilih Opsi --",
                allowClear: true,
                width: '100%'
            });
        });
    }

    // 2. Input Masking / Validation
    // Memastikan NISN hanya angka
    $('input[name="nisn"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Memastikan No HP hanya angka
    $('input[name="nomor_hp_ortu"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

});