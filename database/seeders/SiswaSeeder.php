<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;    // <-- 1. IMPORT
use App\Models\User;    // <-- 2. IMPORT
use App\Models\Kelas;   // <-- 3. IMPORT
use App\Models\Siswa;   // <-- 4. IMPORT
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel siswa dulu
        DB::table('siswa')->truncate();

        // 2. Ambil data induk (role Ortu dan kelas)
        $roleOrtu = Role::where('nama_role', 'Orang Tua')->first();
        $kelasATP = Kelas::where('nama_kelas', 'XII ATP 1')->first();

        // 3. Pastikan data induk ada
        if ($roleOrtu && $kelasATP) {
            
            // 4. Buat 1 User Orang Tua (untuk Budi)
            $orangTuaBudi = User::updateOrCreate(
                ['username' => 'ortu.budi'], // Cari berdasarkan username
                [
                    'role_id' => $roleOrtu->id,
                    'nama' => 'Orang Tua Budi',
                    'email' => 'ortu.budi@example.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // 5. Buat 1 Siswa (Budi)
            Siswa::create([
                'kelas_id' => $kelasATP->id,
                'orang_tua_user_id' => $orangTuaBudi->id, // Hubungkan ke ortu
                'nisn' => '123456789', // NISN Contoh
                'nama_siswa' => 'Budi Santoso',
                'nomor_hp_ortu' => '08123456789' // No HP Ortu (untuk WA)
            ]);

            // 6. Buat 1 Siswa lain (tanpa user ortu, untuk tes)
            Siswa::create([
                'kelas_id' => $kelasATP->id,
                'orang_tua_user_id' => null, // Tes jika ortu belum ada user
                'nisn' => '987654321', // NISN Contoh
                'nama_siswa' => 'Ani Lestari',
                'nomor_hp_ortu' => '08987654321'
            ]);
        }
    }
}