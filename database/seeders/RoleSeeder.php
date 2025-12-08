<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // <-- 1. IMPORT MODEL ROLE
use Illuminate\Support\Facades\DB; // <-- 2. (Opsional) Untuk stabilitas

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 3. Kosongkan tabel dulu agar tidak duplikat
        DB::table('roles')->truncate();

        // 4. Masukkan SEMUA peran (Aktor) termasuk Developer dan Waka Sarana
        $roles = [
            ['nama_role' => 'Developer'],           // Role khusus untuk development
            ['nama_role' => 'Operator Sekolah'],    // Role utama untuk CRUD
            ['nama_role' => 'Waka Kesiswaan'],      // Wakil Kepala Sekolah Kesiswaan
            ['nama_role' => 'Waka Sarana'],         // Wakil Kepala Sekolah Sarana Prasarana
            ['nama_role' => 'Kepala Sekolah'],      // Kepala Sekolah
            ['nama_role' => 'Kaprodi'],             // Kepala Program Studi
            ['nama_role' => 'Wali Kelas'],          // Wali Kelas
            ['nama_role' => 'Guru'],                // Guru
            ['nama_role' => 'Wali Murid'],          // Orang Tua/Wali Murid
        ];

        // 5. Masukkan data ke database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}