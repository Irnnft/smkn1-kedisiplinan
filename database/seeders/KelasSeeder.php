<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurusan; // <-- 1. IMPORT JURUSAN
use App\Models\User;    // <-- 2. IMPORT USER
use App\Models\Kelas;   // <-- 3. IMPORT KELAS
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel dulu
        DB::table('kelas')->truncate();

        // 2. Ambil data induk (parent)
        $jurusanATP = Jurusan::where('nama_jurusan', 'Agribisnis Tanaman Perkebunan (ATP)')->first();
        $waliKelasTes = User::where('username', 'walikelas.tes')->first();

        // 3. Pastikan data induk ada sebelum membuat data anak
        if ($jurusanATP && $waliKelasTes) {
            Kelas::create([
                'jurusan_id' => $jurusanATP->id,
                'wali_kelas_user_id' => $waliKelasTes->id,
                'nama_kelas' => 'XII ATP 1' // Kelas contoh
            ]);
        }
        
        // Anda bisa tambahkan kelas lain di sini jika perlu
        // Contoh:
        // $jurusanAKL = Jurusan::where('nama_jurusan', 'like', 'Akuntansi%')->first();
        // $waliKelasLain = User::where('username', 'guru')->first(); // Ambil guru umum
        // if ($jurusanAKL && $waliKelasLain) {
        //     Kelas::create([
        //         'jurusan_id' => $jurusanAKL->id,
        //         'wali_kelas_user_id' => $waliKelasLain->id,
        //         'nama_kelas' => 'X AKL 1'
        //     ]);
        // }
    }
}