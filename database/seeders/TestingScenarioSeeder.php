<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\KategoriPelanggaran;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranFrequencyRule;
use App\Models\PembinaanInternalRule;
use Illuminate\Support\Facades\Hash;

class TestingScenarioSeeder extends Seeder
{
    /**
     * Seed minimal data untuk testing scenario:
     * - Pembinaan Internal: 0-50 (WK), 51-100 (WK+Kaprodi), 101-150 (WK+Kaprodi+Waka), 151-200 (All+Kepsek)
     * - Jenis Pelanggaran: Merokok
     * - Frequency Rule: min=1, max=1, poin=45, trigger_surat=FALSE, pembina=[Wali Kelas]
     */
    public function run(): void
    {
        $this->command->info('🎬 Starting Testing Scenario Seeder...');
        
        // 1. Seed Roles
        $this->seedRoles();
        
        // 2. Seed Users (Developer + Staff)
        $this->seedUsers();
        
        // 3. Seed Jurusan & Kelas
        $this->seedJurusanKelas();
        
        // 4. Seed Siswa
        $this->seedSiswa();
        
        // 5. Seed Kategori & Jenis Pelanggaran
        $this->seedPelanggaran();
        
        // 6. Seed Pembinaan Internal Rules (Sesuai requirement)
        $this->seedPembinaanInternalRules();
        
        $this->command->info('✅ Testing Scenario Seeder DONE!');
    }
    
    private function seedRoles(): void
    {
        $this->command->info('📝 Seeding Roles...');
        
        $roles = [
            'Developer',
            'Operator Sekolah',
            'Kepala Sekolah',
            'Waka Kesiswaan',
            'Waka Sarana',
            'Kaprodi',
            'Wali Kelas',
            'Guru',
            'Wali Murid',
        ];
        
        foreach ($roles as $roleName) {
            Role::create(['nama_role' => $roleName]);
        }
        
        $this->command->info('   ✓ 9 Roles created');
    }
    
    private function seedUsers(): void
    {
        $this->command->info('👤 Seeding Users...');
        
        $developerRole = Role::where('nama_role', 'Developer')->first();
        $waliKelasRole = Role::where('nama_role', 'Wali Kelas')->first();
        $kaprodiRole = Role::where('nama_role', 'Kaprodi')->first();
        $wakaRole = Role::where('nama_role', 'Waka Kesiswaan')->first();
        $kepsekRole = Role::where('nama_role', 'Kepala Sekolah')->first();
        
        // Developer account (dari requirement)
        User::create([
            'role_id' => $developerRole->id,
            'nama' => 'Developer Account',
            'username' => 'developer',
            'email' => 'arigumilang91@gmail.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
       
        // Wali Kelas
        User::create([
            'role_id' => $waliKelasRole->id,
            'nama' => 'Ibu Guru Wali Kelas A',
            'username' => 'walikelas_a',
            'email' => 'walikelas@test.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        
        // Kaprodi
        User::create([
            'role_id' => $kaprodiRole->id,
            'nama' => 'Bapak Kaprodi AKL',
            'username' => 'kaprodi_akl',
            'email' => 'kaprodi@test.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        
        // Waka Kesiswaan
        User::create([
            'role_id' => $wakaRole->id,
            'nama' => 'Bapak Waka Kesiswaan',
            'username' => 'waka_kesiswaan',
            'email' => 'waka@test.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        
        // Kepala Sekolah
        User::create([
            'role_id' => $kepsekRole->id,
            'nama' => 'Bapak Kepala Sekolah',
            'username' => 'kepala_sekolah',
            'email' => 'kepsek@test.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        
        $this->command->info('   ✓ 5 Users created (Developer, Wali Kelas, Kaprodi, Waka, Kepsek)');
    }
    
    private function seedJurusanKelas(): void
    {
        $this->command->info('🏫 Seeding Jurusan & Kelas...');
        
        $kaprodi = User::where('username', 'kaprodi_akl')->first();
        $waliKelas = User::where('username', 'walikelas_a')->first();
        
        // Create Jurusan AKL
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga',
            'kode_jurusan' => 'AKL',
            'kaprodi_user_id' => $kaprodi->id,
        ]);
        
        // Create Kelas A
        $kelas = Kelas::create([
            'nama_kelas' => 'X AKL 1',
            'tingkat' => 10,
            'jurusan_id' => $jurusan->id,
            'wali_kelas_user_id' => $waliKelas->id,
        ]);
        
        $this->command->info('   ✓ 1 Jurusan (AKL) & 1 Kelas (X AKL 1) created');
    }
    
    private function seedSiswa(): void
    {
        $this->command->info('👨‍🎓 Seeding Siswa...');
        
        $kelas = Kelas::first();
        $waliMuridRole = Role::where('nama_role', 'Wali Murid')->first();
        
        // Create Wali Murid
        $waliMurid = User::create([
            'role_id' => $waliMuridRole->id,
            'nama' => 'Bapak Wali Ahmad',
            'username' => 'ahmad_wali',
            'email' => 'wali_ahmad@test.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'profile_completed_at' => now(),
        ]);
        
        // Create Siswa
        Siswa::create([
            'kelas_id' => $kelas->id,
            'wali_murid_user_id' => $waliMurid->id,
            'nisn' => '1234567890',
            'nama_siswa' => 'Ahmad Maulana',
            'nomor_hp_wali_murid' => '081234567890',
        ]);
        
        $this->command->info('   ✓ 1 Siswa (Ahmad Maulana) created in Kelas A');
    }
    
    private function seedPelanggaran(): void
    {
        $this->command->info('⚠️  Seeding Jenis Pelanggaran...');
        
        // Create Kategori
        $kategori = KategoriPelanggaran::create([
            'nama_kategori' => 'Pelanggaran Berat',
            'tingkat_keseriusan' => 'Berat',
        ]);
        
        // Create Jenis Pelanggaran: Merokok
        $jenisPelanggaran = JenisPelanggaran::create([
            'kategori_pelanggaran_id' => $kategori->id,
            'nama_pelanggaran' => 'Merokok',
            'poin' => 0, // Will be calculated from frequency rules
            'has_frequency_rules' => true,
            'is_active' => true,
        ]);
        
        // Create Frequency Rule sesuai requirement:
        // min=1, max=1, poin=45, trigger_surat=FALSE, pembina=[Wali Kelas]
        PelanggaranFrequencyRule::create([
            'jenis_pelanggaran_id' => $jenisPelanggaran->id,
            'frequency_min' => 1,
            'frequency_max' => 1,
            'poin' => 45,
            'sanksi_description' => 'Pembinaan ditempat oleh Wali Kelas',
            'trigger_surat' => false,
            'pembina_roles' => ['Wali Kelas'],
            'display_order' => 1,
        ]);
        
        $this->command->info('   ✓ Jenis Pelanggaran "Merokok" created');
        $this->command->info('   ✓ Frequency Rule: min=1, max=1, poin=45, trigger_surat=FALSE');
    }
    
    private function seedPembinaanInternalRules(): void
    {
        $this->command->info('📋 Seeding Pembinaan Internal Rules...');
        $this->command->info('   (Sesuai requirement: 0-50, 51-100, 101-150, 151-200)');
        
        $rules = [
            [
                'poin_min' => 0,
                'poin_max' => 50,
                'pembina_roles' => ['Wali Kelas'],
                'keterangan' => 'Pembinaan oleh Wali Kelas',
                'display_order' => 1,
            ],
            [
                'poin_min' => 51,
                'poin_max' => 100,
                'pembina_roles' => ['Wali Kelas', 'Kaprodi'],
                'keterangan' => 'Pembinaan oleh Wali Kelas dan Kaprodi',
                'display_order' => 2,
            ],
            [
                'poin_min' => 101,
                'poin_max' => 150,
                'pembina_roles' => ['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan'],
                'keterangan' => 'Pembinaan oleh Wali Kelas, Kaprodi, dan Waka Kesiswaan',
                'display_order' => 3,
            ],
            [
                'poin_min' => 151,
                'poin_max' => 200,
                'pembina_roles' => ['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan', 'Kepala Sekolah'],
                'keterangan' => 'Pembinaan oleh Wali Kelas, Kaprodi, Waka Kesiswaan, dan Kepala Sekolah',
                'display_order' => 4,
            ],
        ];
        
        foreach ($rules as $rule) {
            PembinaanInternalRule::create($rule);
        }
        
        $this->command->info('   ✓ 4 Pembinaan Internal Rules created');
    }
}
