<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // <-- 1. IMPORT MODEL ROLE
use App\Models\User; // <-- 2. IMPORT MODEL USER
use Illuminate\Support\Facades\Hash; // <-- 3. IMPORT HASH UNTUK PASSWORD

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID dari setiap role
        $roleOperator = Role::where('nama_role', 'Operator Sekolah')->first()->id;
        $roleWaka = Role::where('nama_role', 'Waka Kesiswaan')->first()->id;
        $roleKepsek = Role::where('nama_role', 'Kepala Sekolah')->first()->id;
        $roleGuru = Role::where('nama_role', 'Guru')->first()->id;
        $roleKaprodi = Role::where('nama_role', 'Kaprodi')->first()->id;
        $roleWaliKelas = Role::where('nama_role', 'Wali Kelas')->first()->id;

        // 2. Buat Pengguna Operator Sekolah
        User::updateOrCreate(
            ['username' => 'operator'], // Cari berdasarkan username 'operator'
            [
                'role_id' => $roleOperator,
                'nama' => 'Operator Admin',
                'email' => 'operator@smkn1.sch.id',
                'password' => Hash::make('password'), // passwordnya: "password"
                'email_verified_at' => now(),
            ]
        );

        // 3. Buat Pengguna Waka Kesiswaan
        User::updateOrCreate(
            ['username' => 'waka'], // Cari berdasarkan username 'waka'
            [
                'role_id' => $roleWaka,
                'nama' => 'Waka Kesiswaan',
                'email' => 'waka@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 4. Buat Pengguna Kepala Sekolah
        User::updateOrCreate(
            ['username' => 'kepsek'], // Cari berdasarkan username 'kepsek'
            [
                'role_id' => $roleKepsek,
                'nama' => 'Kepala Sekolah',
                'email' => 'kepsek@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 5. Buat Pengguna Guru (Umum)
        User::updateOrCreate(
            ['username' => 'guru'], // Cari berdasarkan username 'guru'
            [
                'role_id' => $roleGuru,
                'nama' => 'Guru Umum',
                'email' => 'guru@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 6. Buat 5 User Kaprodi (satu per jurusan)
        User::updateOrCreate(
            ['username' => 'kaprodi.atp'],
            [
                'role_id' => $roleKaprodi,
                'nama' => 'Kaprodi ATP',
                'email' => 'kaprodi.atp@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['username' => 'kaprodi.aphp'],
            [
                'role_id' => $roleKaprodi,
                'nama' => 'Kaprodi APHP',
                'email' => 'kaprodi.aphp@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['username' => 'kaprodi.atu'],
            [
                'role_id' => $roleKaprodi,
                'nama' => 'Kaprodi ATU',
                'email' => 'kaprodi.atu@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['username' => 'kaprodi.teb'],
            [
                'role_id' => $roleKaprodi,
                'nama' => 'Kaprodi TEB',
                'email' => 'kaprodi.teb@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['username' => 'kaprodi.akl'],
            [
                'role_id' => $roleKaprodi,
                'nama' => 'Kaprodi AKL',
                'email' => 'kaprodi.akl@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // 7. Buat 1 User Wali Kelas (untuk tes awal)
        User::updateOrCreate(
            ['username' => 'walikelas.tes'],
            [
                'role_id' => $roleWaliKelas,
                'nama' => 'Wali Kelas Tes',
                'email' => 'walikelas.tes@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}