var activeTopic = 'all';

// 1. VISUAL SELECTION (JQUERY)
// Fungsi untuk memberikan highlight visual pada kartu siswa yang dipilih
function selectStudent(el) {
    $('.student-item').removeClass('selected'); // Hapus seleksi lama
    $(el).addClass('selected'); // Tambah seleksi baru
    $(el).find('input').prop('checked', true); // Centang radio button tersembunyi
}

// Fungsi untuk memberikan highlight visual pada kartu pelanggaran yang dipilih
function selectViolation(el) {
    $('.violation-item').removeClass('selected');
    $(el).addClass('selected');
    $(el).find('input').prop('checked', true);
}

$(document).ready(function() {
    // Inisialisasi Custom File Input (Agar nama file muncul setelah dipilih)
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
    
    // Fallback manual untuk UI File Input jika bsCustomFileInput bermasalah
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // ==================================
    // 2. LOGIKA FILTER SISWA
    // ==================================
    function filterStudents() {
        var fTingkat = $('#filterTingkat').val();
        var fJurusan = $('#filterJurusan').val();
        var fKelas = $('#filterKelas').val();
        var fSearch = $('#searchSiswa').val().toLowerCase();
        var visible = 0;

        $('.student-item').each(function() {
            var el = $(this);
            var match = true;

            // Cek kecocokan dengan setiap filter dropdown
            // Menggunakan attribut data-* dari HTML
            if(fTingkat && el.data('tingkat') != fTingkat) match = false;
            if(fJurusan && el.data('jurusan') != fJurusan) match = false;
            if(fKelas && el.data('kelas') != fKelas) match = false;
            
            // Cek kecocokan dengan teks pencarian (Nama atau NISN)
            if(fSearch && !el.data('search').includes(fSearch)) match = false;
            
            // Tampilkan atau sembunyikan elemen berdasarkan hasil match
            if(match) { 
                el.show(); 
                visible++; 
            } else { 
                el.hide(); 
            }
        });
        
        // Update badge jumlah siswa yang tampil
        $('#countSiswa').text(visible + ' Siswa');
        
        // Tampilkan pesan "Tidak ditemukan" jika hasil 0
        if(visible === 0) {
            $('#noResultMsg').show(); 
        } else { 
            $('#noResultMsg').hide(); 
        }
    }

    // Pasang Event Listener pada input filter siswa
    $('#filterTingkat, #filterJurusan, #filterKelas').on('change', filterStudents);
    $('#searchSiswa').on('keyup', filterStudents);

    // Logika Cascading: Reset filter Kelas saat Jurusan berubah
    // Agar user tidak memilih kelas yang salah (misal Jurusan TKJ tapi kelas Akuntansi)
    $('#filterJurusan').on('change', function() {
        var jurId = $(this).val();
        $('#filterKelas option').each(function() {
            var kJur = $(this).data('jurusan');
            // Tampilkan opsi jika Jurusan cocok atau value kosong (pilihan default)
            if(!jurId || !kJur || kJur == jurId || $(this).val() == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#filterKelas').val(''); // Reset nilai dropdown kelas
        filterStudents(); // Jalankan filter ulang
    });

    // Fungsi Reset Filter Siswa
    window.resetFilters = function() {
        $('#filterTingkat, #filterJurusan, #filterKelas, #searchSiswa').val('').trigger('change');
        $('.student-item').removeClass('selected');
        $('input[name="siswa_id"]').prop('checked', false);
    };

    // ==================================
    // 3. FILTER PELANGGARAN (SMART SEARCH)
    // ==================================
    
    // KAMUS TOPIK (Mapping Kategori Tombol ke Kata Kunci Nama Pelanggaran)
    const topics = {
        'atribut': ['dasi', 'topi', 'kaos', 'baju', 'seragam', 'ikat', 'sabuk', 'sepatu', 'logo', 'atribut', 'pakaian', 'bad'],
        'kehadiran': ['lambat', 'telat', 'bolos', 'cabut', 'alfa', 'absen', 'keluar', 'pulang', 'masuk'],
        'kerapian': ['rambut', 'kuku', 'panjang', 'cat', 'warna', 'gondrong', 'make up', 'alis', 'lipstik', 'cukur', 'botak'],
        'ibadah': ['sholat', 'doa', 'jumat', 'mengaji', 'ibadah', 'musholla'],
        // Untuk 'berat', kita akan cek kategori database juga nanti
        'berat': ['rokok', 'vape', 'hantam', 'pukul', 'kelahi', 'tajam', 'curi', 'maling', 'porno', 'bokep', 'narkoba', 'miras', 'obat', 'bully', 'ancam', 'palak', 'rusak', 'sangat']
    };
    
    // KAMUS ALIAS (Bahasa Gaul -> Kata Kunci Nama Pelanggaran)
    // Kunci (Key) adalah kata yang ada di database.
    // Nilai (Value) adalah kata yang mungkin diketik user.
    const aliasMap = {
        'rokok': ['sebat', 'asap', 'bakar', 'surya', 'udud', 'vape', 'pod'],
        'bolos': ['alfamart', 'warnet', 'kantin', 'wc', 'minggat', 'lompat'],
        'terlambat': ['telat', 'kesiangan', 'macet', 'bangun'],
        'berkelahi': ['gelut', 'ribut', 'tawuran', 'tumbuk', 'baku hantam', 'tonjok'],
        'atribut': ['topi', 'dasi', 'sabuk', 'kaos kaki', 'bet'],
        'pornografi': ['bokep', 'blue', 'video', '18+', 'semok', 'sange'],
        'sajam': ['pisau', 'gear', 'clurit', 'cutter', 'gunting', 'parang']
    };

    function filterViolations() {
        var fSearch = $('#searchPelanggaran').val().toLowerCase();
        var visible = 0;

        $('.violation-item').each(function() {
            var el = $(this);
            var nama = el.data('nama'); // string lowercase dari database
            var kategoriDB = el.data('kategori'); // string lowercase dari database
            var match = true;

            // 1. Cek Filter Topik (Tombol Kategori di atas)
            if(activeTopic !== 'all') {
                var topicMatch = false;
                
                // Khusus topik 'berat', cek juga kategori dari database langsung
                if(activeTopic === 'berat') {
                    if(kategoriDB.includes('berat') || kategoriDB.includes('sangat')) {
                        topicMatch = true;
                    }
                }
                
                // Cek kecocokan keyword topik pada nama pelanggaran
                if(!topicMatch && topics[activeTopic]) {
                    if(topics[activeTopic].some(w => nama.includes(w))) {
                        topicMatch = true;
                    }
                }
                
                if(!topicMatch) match = false;
            }

            // 2. Cek Search Text & Alias (Pencarian Cerdas)
            if(match && fSearch) {
                var textMatch = false;
                
                // A. Cek langsung nama pelanggaran (Partial Match)
                if(nama.includes(fSearch)) {
                    textMatch = true;
                } 
                else {
                    // B. Cek Kamus Alias (Smart Search)
                    // Loop setiap kata kunci utama di kamus
                    Object.keys(aliasMap).forEach(function(key) {
                        // Jika nama pelanggaran di DB mengandung kunci ini (misal: "Merokok")
                        if(nama.includes(key)) {
                            // Cek apakah input user ada di daftar alias kunci tersebut (misal user ketik "Sebat")
                            var aliases = aliasMap[key];
                            if(aliases.some(alias => alias.includes(fSearch))) {
                                textMatch = true;
                            }
                        }
                    });
                }
                
                if(!textMatch) match = false;
            }

            // Tampilkan/Sembunyikan kartu pelanggaran
            if(match) { 
                el.show(); 
                visible++; 
            } else { 
                el.hide(); 
            }
        });
        
        // Tampilkan pesan jika tidak ada pelanggaran yang cocok
        if(visible === 0) {
            $('#noViolationMsg').show(); 
        } else { 
            $('#noViolationMsg').hide(); 
        }
    }

    // Fungsi yang dipanggil saat tombol Filter Topik diklik
    window.setFilterTopic = function(topic, btn) {
        activeTopic = topic;
        // Update visual tombol (Hapus active dari semua, tambah ke yang diklik)
        $('.filter-pills .btn').removeClass('active');
        $(btn).addClass('active');
        
        filterViolations();
    }

    // Pasang Event Listener pada input pencarian pelanggaran
    $('#searchPelanggaran').on('keyup', function() { filterViolations(); });
});