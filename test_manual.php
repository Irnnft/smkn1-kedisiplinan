<?php

// Manual testing script untuk trace alur sistem
// Run: php test_manual.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\User;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\RiwayatPelanggaran;
use App\Models\PembinaanInternalRule;
use App\Models\PelanggaranFrequencyRule;
use App\Services\Pelanggaran\PelanggaranRulesEngine;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "🎬 MANUAL TESTING - Trace Alur Sistem\n";
echo "=================================\n\n";

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// SETUP: Create minimal data
echo "📝 STEP 1: Setup Data...\n";

// 1. Roles
DB::table('roles')->truncate();
$roles = ['Developer', 'Wali Kelas', 'Kaprodi', 'Waka Kesiswaan', 'Kepala Sekolah', 'Wali Murid'];
foreach ($roles as $r) {
    Role::create(['nama_role' => $r]);
}
echo "   ✓ Roles created\n";

// 2. Developer User
DB::table('users')->truncate();
$devRole = Role::where('nama_role', 'Developer')->first();
User::create([
    'role_id' => $devRole->id,
    'nama' => 'Developer',
    'username' => 'dev',
    'email' => 'arigumilang91@gmail.com',
    'password' => Hash::make('password'),
    'is_active' => true,
]);
echo "   ✓ Developer user created\n";

// 3. Pembinaan Internal Rules (sesuai requirement)
DB::table('pembinaan_internal_rules')->truncate();
$pembinaanRules = [
    ['poin_min' => 0, 'poin_max' => 50, 'pembina_roles' => json_encode(['Wali Kelas']), 'keterangan' => 'Pembinaan WK', 'display_order' => 1],
    ['poin_min' => 51, 'poin_max' => 100, 'pembina_roles' => json_encode(['Wali Kelas', 'Kaprodi']), 'keterangan' => 'Pembinaan WK+Kaprodi', 'display_order' => 2],
    ['poin_min' => 101, 'poin_max' => 150, 'pembina_roles' => json_encode(['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan']), 'keterangan' => 'Pembinaan WK+Kaprodi+Waka', 'display_order' => 3],
    ['poin_min' => 151, 'poin_max' => 200, 'pembina_roles' => json_encode(['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan', 'Kepala Sekolah']), 'keterangan' => 'Pembinaan All+Kepsek', 'display_order' => 4],
];
foreach ($pembinaanRules as $rule) {
    PembinaanInternalRule::create($rule);
}
echo "   ✓ Pembinaan Internal Rules created (0-50, 51-100, 101-150, 151-200)\n";

// 4. Jenis Pelanggaran + Frequency Rule
DB::table('pelanggaran_frequency_rules')->truncate();
DB::table('jenis_pelanggaran')->truncate();
DB::table('kategori_pelanggaran')->truncate();

$kategori = DB::table('kategori_pelanggaran')->insertGetId([
    'nama_kategori' => 'Berat',
    'tingkat_keseriusan' => 'Berat',
]);

$jenisPelanggaran = JenisPelanggaran::create([
    'kategori_id' => $kategori,
    'nama_pelanggaran' => 'Merokok',
    'poin' => 0,
    'has_frequency_rules' => true,
    'is_active' => true,
]);

// Frequency Rule: min=1, max=1, poin=45, trigger_surat=FALSE
PelanggaranFrequencyRule::create([
    'jenis_pelanggaran_id' => $jenisPelanggaran->id,
    'frequency_min' => 1,
    'frequency_max' => 1,
    'poin' => 45,
    'sanksi_description' => 'Pembinaan ditempat',
    'trigger_surat' => false,
    'pembina_roles' => ['Wali Kelas'],
    'display_order' => 1,
]);

echo "   ✓ Jenis Pelanggaran 'Merokok' created\n";
echo "   ✓ Frequency Rule: min=1, max=1, poin=45, NO surat\n\n";

// 5. Jurusan & Kelas (required for siswa)
DB::table('kelas')->truncate();
DB::table('jurusan')->truncate();

$jurusanId = DB::table('jurusan')->insertGetId([
    'nama_jurusan' => 'Akuntansi',
    'kode_jurusan' => 'AKL',
    'kaprodi_user_id' => null,
]);

$kelasId = DB::table('kelas')->insertGetId([
    'nama_kelas' => 'X AKL 1',
    'tingkat' => 10,
    'jurusan_id' => $jurusanId,
    'wali_kelas_user_id' => null,
]);

echo "   ✓ Jurusan & Kelas created\n";

// 6. Siswa
DB::table('siswa')->truncate();
$siswaId = DB::table('siswa')->insertGetId([
    'nisn' => '1234567890',
    'nama_siswa' => 'Ahmad Test',
    'kelas_id' => $kelasId,
    'wali_murid_user_id' => null,
    'nomor_hp_wali_murid' => '081234567890',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "   ✓ Siswa 'Ahmad Test' created (ID: {$siswaId})\n\n";

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ==============================================================
// TESTING: Catat pelanggaran 5 kali
// ==============================================================

$rulesEngine = new PelanggaranRulesEngine(new \App\Notifications\TindakLanjutNotificationService());

echo "🧪 STEP 2: Testing - Catat Pelanggaran 5x...\n";
echo "=========================================\n\n";

for ($i = 1; $i <= 5; $i++) {
    echo "--- PENCATATAN KE-{$i} ---\n";
    
    // Catat pelanggaran
    $riwayat = RiwayatPelanggaran::create([
        'siswa_id' => $siswaId,
        'jenis_pelanggaran_id' => $jenisPelanggaran->id,
        'guru_pencatat_user_id' => 1, // Developer
        'tanggal_kejadian' => now(),
        'keterangan' => "Pencatatan ke-{$i}",
    ]);
    
    // Trigger rules engine
    $rulesEngine->processBatch($siswaId, [$jenisPelanggaran->id]);
    
    // Hitung total poin
    $totalPoin = $rulesEngine->hitungTotalPoinAkumulasi($siswaId);
    
    // Get pembinaan rekomendasi
    $rekomendasi = $rulesEngine->getPembinaanInternalRekomendasi($totalPoin);
    
    // Get tindak lanjut jika ada
    $tindakLanjut = DB::table('tindak_lanjut')->where('siswa_id', $siswaId)->latest()->first();
    $suratPanggilan = $tindakLanjut ? DB::table('surat_panggilan')->where('tindak_lanjut_id', $tindakLanjut->id)->first() : null;
    
    echo "✅ Riwayat ID: {$riwayat->id}\n";
    echo "📊 Total Poin Akumulasi: {$totalPoin}\n";
    echo "👥 Pembinaan  Rekomendasi:\n";
    echo "   - Range: {$rekomendasi['range_text']}\n";
    
    // Handle pembina_roles yang mungkin array atau string
    $pembinaList = is_array($rekomendasi['pembina_roles']) 
        ? implode(', ', $rekomendasi['pembina_roles']) 
        : $rekomendasi['pembina_roles'];
    echo "   - Pembina: {$pembinaList}\n";
    echo "   - Keterangan: {$rekomendasi['keterangan']}\n";
    
    if ($tindakLanjut) {
        echo "📄 Tindak Lanjut:\n";
        echo "   - ID: {$tindakLanjut->id}\n";
        echo "   - Status: {$tindakLanjut->status}\n";
        echo "   - Sanksi: {$tindakLanjut->sanksi_deskripsi}\n";
    } else {
        echo "📄 Tindak Lanjut: TIDAK ADA (Sesuai ekspektasi - trigger_surat=FALSE)\n";
    }
    
    if ($suratPanggilan) {
        echo "✉️  Surat Panggilan:\n";
        echo "   - Tipe: {$suratPanggilan->tipe_surat}\n";
        echo "   - Nomor: {$suratPanggilan->nomor_surat}\n";
    } else {
        echo "✉️  Surat Panggilan: TIDAK ADA (Sesuai ekspektasi - trigger_surat=FALSE)\n";
    }
    
    echo "\n";
}

echo "\n🎉 TESTING SELESAI!\n";
echo "==================\n\n";

echo "📌 RINGKASAN EKSPEKTASI:\n";
echo "1x catat → Poin: 45, Pembinaan: WK, NO surat\n";
echo "2x catat → Poin: 90, Pembinaan: WK+Kaprodi, NO surat\n";
echo "3x catat → Poin: 135, Pembinaan: WK+Kaprodi+Waka, NO surat\n";
echo "4x catat → Poin: 180, Pembinaan: All+Kepsek, NO surat\n";
echo "5x catat → Poin: 225, Pembinaan: All+Kepsek, NO surat\n\n";

echo "⚠️  CATATAN:\n";
echo "- Frequency rule: min=1, max=1 → setiap kali +45 poin\n";
echo "- trigger_surat=FALSE → TIDAK ada TindakLanjut & Surat\n";
echo "- Pembinaan Internal hanya REKOMENDASI, bukan trigger surat\n";
