<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriPelanggaran; // <-- IMPORT
use Illuminate\Support\Facades\DB;

class KategoriPelanggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel dulu
        DB::table('kategori_pelanggaran')->truncate();

        // 2. Buat 3 kategori
        $kategori = [
            ['nama_kategori' => 'RINGAN'],
            ['nama_kategori' => 'SEDANG'],
            ['nama_kategori' => 'BERAT'],
        ];

        // 3. Masukkan ke database
        foreach ($kategori as $k) {
            KategoriPelanggaran::create($k);
        }
    }
}