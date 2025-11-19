<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class MassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID Role yang dibutuhkan
        $roleWali = Role::where('nama_role', 'Wali Kelas')->first()->id;
        $roleOrtu = Role::where('nama_role', 'Orang Tua')->first()->id;

        // 2. Ambil semua Jurusan yang sudah dibuat di JurusanSeeder
        $jurusanList = Jurusan::all();

        echo "Memulai proses seeding massal... (Ini mungkin memakan waktu beberapa detik)\n";

        foreach ($jurusanList as $jurusan) {
            
            // Kita ambil singkatan jurusan (misal "Agribisnis Tanaman Perkebunan (ATP)" -> ambil "ATP")
            // Cara simpel: Ambil teks di dalam kurung
            preg_match('/\((.*?)\)/', $jurusan->nama_jurusan, $match);
            $kodeJurusan = $match[1] ?? 'JURUSAN';

            // 3. Buat Kelas untuk Tingkat X, XI, XII
            $tingkats = ['X', 'XI', 'XII'];

            foreach ($tingkats as $tingkat) {
                
                // --- A. BUAT USER WALI KELAS ---
                // Username: wali.xi.atp, wali.x.tkj, dll
                $usernameWali = 'wali.' . strtolower($tingkat) . '.' . strtolower($kodeJurusan);
                
                $waliUser = User::create([
                    'role_id' => $roleWali,
                    'nama' => "Wali Kelas $tingkat $kodeJurusan",
                    'username' => $usernameWali,
                    'email' => $usernameWali . '@smkn1.sch.id',
                    'password' => Hash::make('password'), // Password default
                ]);

                // --- B. BUAT KELAS ---
                $kelas = Kelas::create([
                    'jurusan_id' => $jurusan->id,
                    'wali_kelas_user_id' => $waliUser->id,
                    'nama_kelas' => "$tingkat $kodeJurusan 1", // Contoh: XI ATP 1
                ]);

                echo "  -> Membuat Kelas: $kelas->nama_kelas (Wali: $waliUser->nama)\n";

                // --- C. BUAT 20 SISWA PER KELAS ---
                for ($i = 1; $i <= 20; $i++) {
                    
                    // 1. Buat Akun Orang Tua untuk siswa ini
                    $ortuUser = User::factory()->create([
                        'role_id' => $roleOrtu,
                        'nama' => 'Ortu Siswa ' . $kelas->nama_kelas . ' ' . $i,
                        'username' => 'ortu.' . strtolower($kodeJurusan) . '.' . $kelas->id . '.' . $i, // unik
                        'password' => Hash::make('password'),
                    ]);

                    // 2. Buat Siswa (Pakai Factory)
                    Siswa::factory()->create([
                        'kelas_id' => $kelas->id,
                        'orang_tua_user_id' => $ortuUser->id,
                    ]);
                }
            }
        }

        echo "SELESAI! Database sekarang penuh dengan data dummy.\n";
    }
}