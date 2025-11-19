<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriPelanggaran; // <-- 1. IMPORT
use App\Models\JenisPelanggaran; // <-- 2. IMPORT
use Illuminate\Support\Facades\DB;

class JenisPelanggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel dulu
        DB::table('jenis_pelanggaran')->truncate();

        // 2. Ambil ID kategori
        $ringan = KategoriPelanggaran::where('nama_kategori', 'RINGAN')->first()->id;
        $sedang = KategoriPelanggaran::where('nama_kategori', 'SEDANG')->first()->id;
        $berat = KategoriPelanggaran::where('nama_kategori', 'BERAT')->first()->id;

        // 3. Definisikan semua pelanggaran
        $pelanggaran = [
            // --- Kategori RINGAN ---
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Berkuku panjang dan berkutek', 'poin' => 0],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Memakai aksesoris berlebihan', 'poin' => 0],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Rambut tidak sesuai (3-2-1, diwarnai, crop)', 'poin' => 0],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Atribut/seragam tidak lengkap', 'poin' => 5],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Terlambat apel pagi', 'poin' => 5],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Terlambat tidak apel pagi', 'poin' => 10],
            ['kategori_id' => $ringan, 'nama_pelanggaran' => 'Tidak melaksanakan sholat Dzuhur dan Ashar', 'poin' => 10],

            // --- Kategori SEDANG ---
            ['kategori_id' => $sedang, 'nama_pelanggaran' => 'Alfa (absen tanpa keterangan)', 'poin' => 25],
            ['kategori_id' => $sedang, 'nama_pelanggaran' => 'Cabut keluar sekolah', 'poin' => 25],
            ['kategori_id' => $sedang, 'nama_pelanggaran' => 'Tidak mengikuti kegiatan hari besar', 'poin' => 25],
            ['kategori_id' => $sedang, 'nama_pelanggaran' => 'Membawa HP/Elektronik tanpa izin', 'poin' => 25],
            ['kategori_id' => $sedang, 'nama_pelanggaran' => 'Mencoret/merusak fasilitas sekolah', 'poin' => 50],

            // --- Kategori BERAT ---
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Merokok (di sekolah / luar pakai atribut)', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Melakukan bullying', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Membawa senjata tajam', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Mencuri', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Berkelahi / main hakim sendiri', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Pemerasan teman', 'poin' => 100],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Melawan/memaki guru/TU', 'poin' => 200],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Membawa/minum alkohol', 'poin' => 200],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Porno aksi dan pornografi', 'poin' => 200],
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Membawa/konsumsi/edar narkoba', 'poin' => 501], // Kita set 501 agar > 500
            ['kategori_id' => $berat, 'nama_pelanggaran' => 'Kejahatan (urusan kepolisian)', 'poin' => 501], // Kita set 501 agar > 500
        ];

        // 4. Masukkan ke database
        foreach ($pelanggaran as $p) {
            JenisPelanggaran::create($p);
        }
    }
}