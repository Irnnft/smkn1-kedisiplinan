<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- 1. IMPORT USER
use App\Models\Jurusan; // <-- 2. IMPORT JURUSAN
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel dulu
        DB::table('jurusan')->truncate();

        // 2. Ambil ID Kaprodi yang sudah kita buat di UserSeeder
        $kaprodiATP = User::where('username', 'kaprodi.atp')->first()->id;
        $kaprodiAPHP = User::where('username', 'kaprodi.aphp')->first()->id;
        $kaprodiATU = User::where('username', 'kaprodi.atu')->first()->id;
        $kaprodiTEB = User::where('username', 'kaprodi.teb')->first()->id;
        $kaprodiAKL = User::where('username', 'kaprodi.akl')->first()->id;

        // 3. Buat 5 Jurusan
        $jurusan = [
            [
                'kaprodi_user_id' => $kaprodiATP,
                'nama_jurusan' => 'Agribisnis Tanaman Perkebunan (ATP)'
            ],
            [
                'kaprodi_user_id' => $kaprodiAPHP,
                'nama_jurusan' => 'Agribisnis Pengolahan Hasil Pertanian (APHP)'
            ],
            [
                'kaprodi_user_id' => $kaprodiATU,
                'nama_jurusan' => 'Agribisnis Ternak Unggas (ATU)'
            ],
            [
                'kaprodi_user_id' => $kaprodiTEB,
                'nama_jurusan' => 'Teknik Energi Biomassa (TEB)'
            ],
            [
                'kaprodi_user_id' => $kaprodiAKL,
                'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga (AKL)'
            ],
        ];

        // 4. Masukkan ke database
        foreach ($jurusan as $j) {
            Jurusan::create($j);
        }
    }
}