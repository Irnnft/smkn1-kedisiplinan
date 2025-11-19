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

        // 4. Masukkan 7 peran (Aktor) kita
        $roles = [
            ['nama_role' => 'Operator Sekolah'],
            ['nama_role' => 'Waka Kesiswaan'],
            ['nama_role' => 'Kepala Sekolah'],
            ['nama_role' => 'Kaprodi'],
            ['nama_role' => 'Wali Kelas'],
            ['nama_role' => 'Guru'],
            ['nama_role' => 'Orang Tua'],
        ];

        // 5. Masukkan data ke database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}