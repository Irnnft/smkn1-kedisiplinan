<?php

// Debug script untuk cek Siswa Perlu Pembinaan
// Run: php debug_siswa_pembinaan.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Pelanggaran\PelanggaranRulesEngine;
use App\Models\User;
use App\Models\Siswa;

echo "🔍 DEBUG: Siswa Perlu Pembinaan\n";
echo "=================================\n\n";

// 1. Get siswa yang ada
$siswa = Siswa::where('nama_siswa', 'LIKE', '%Ari%')->first();

if (!$siswa) {
    echo "❌ Siswa 'Ari Gumilang' tidak ditemukan!\n";
    echo "   Mencari semua siswa...\n\n";
    $allSiswa = Siswa::all();
    foreach ($allSiswa as $s) {
        echo "   - {$s->nama_siswa} (ID: {$s->id})\n";
    }
    exit;
}

echo "✅ Siswa ditemukan:\n";
echo "   Nama: {$siswa->nama_siswa}\n";
echo "   ID: {$siswa->id}\n";
echo "   Kelas ID: {$siswa->kelas_id}\n";
if ($siswa->kelas) {
    echo "   Kelas: {$siswa->kelas->nama_kelas}\n";
    echo "   Jurusan: {$siswa->kelas->jurusan->nama_jurusan}\n";
}
echo "\n";

// 2. Hitung poin menggunakan rules engine
$rulesEngine = new PelanggaranRulesEngine(new \App\Notifications\TindakLanjutNotificationService());
$totalPoin = $rulesEngine->hitungTotalPoinAkumulasi($siswa->id);

echo "📊 Total Poin Akumulasi: {$totalPoin}\n\n";

// 3. Get rekomendasi pembinaan
$rekomendasi = $rulesEngine->getPembinaanInternalRekomendasi($totalPoin);

echo "👥 Rekomendasi Pembinaan:\n";
echo "   Range: {$rekomendasi['range_text']}\n";
echo "   Pembina Roles: " . print_r($rekomendasi['pembina_roles'], true);
echo "   Keterangan: {$rekomendasi['keterangan']}\n\n";

// 4. Check format pembina_roles
echo "🔍 Format Check:\n";
echo "   Type: " . gettype($rekomendasi['pembina_roles']) . "\n";
echo "   Is Array: " . (is_array($rekomendasi['pembina_roles']) ? 'YES' : 'NO') . "\n";
if (is_array($rekomendasi['pembina_roles'])) {
    echo "   Contents:\n";
    foreach ($rekomendasi['pembina_roles'] as $role) {
        echo "      - {$role}\n";
    }
}
echo "\n";

// 5. Get wali kelas user
$waliKelas = User::whereHas('role', function($q) {
    $q->where('nama_role', 'Wali Kelas');
})->first();

if ($waliKelas) {
    echo "✅ Wali Kelas ditemukan:\n";
    echo "   Nama: {$waliKelas->nama}\n";
    echo "   ID: {$waliKelas->id}\n";
    echo "   Role: {$waliKelas->role->nama_role}\n";
    
    // Check kelas binaan
    $kelasBinaan = $waliKelas->kelasDiampu;
    if ($kelasBinaan) {
        echo "   Kelas Binaan: {$kelasBinaan->nama_kelas} (ID: {$kelasBinaan->id})\n";
        echo "   Match dengan siswa: " . ($kelasBinaan->id === $siswa->kelas_id ? 'YES ✅' : 'NO ❌') . "\n";
    } else {
        echo "   ⚠️  Tidak punya kelas binaan!\n";
    }
} else {
    echo "❌ Wali Kelas tidak ditemukan!\n";
}
echo "\n";

// 6. Simulate filter logic
echo "🧪 SIMULASI FILTER:\n";
echo "-------------------\n";

$userRole = 'Wali Kelas';
$pembinaRoles = $rekomendasi['pembina_roles'];

echo "1. Check role in pembina_roles:\n";
echo "   User Role: {$userRole}\n";
echo "   Pembina Roles: " . implode(', ', $pembinaRoles) . "\n";
echo "   in_array result: " . (in_array($userRole, $pembinaRoles) ? 'TRUE ✅' : 'FALSE ❌') . "\n\n";

if ($waliKelas && $waliKelas->kelasDiampu) {
    echo "2. Check kelas match:\n";
    echo "   Siswa Kelas ID: {$siswa->kelas_id}\n";
    echo "   Wali kelas Binaan ID: {$waliKelas->kelasDiampu->id}\n";
    echo "   Match: " . ($siswa->kelas_id === $waliKelas->kelasDiampu->id ? 'TRUE ✅' : 'FALSE ❌') . "\n\n";
}

// 7. Final verdict
echo "🎯 FINAL VERDICT:\n";
echo "================\n";

$shouldShow = in_array($userRole, $pembinaRoles);
if ($waliKelas && $waliKelas->kelasDiampu) {
    $shouldShow = $shouldShow && ($siswa->kelas_id === $waliKelas->kelasDiampu->id);
}

echo "Siswa '{$siswa->nama_siswa}' SHOULD " . ($shouldShow ? "✅ SHOW" : "❌ NOT SHOW") . " untuk Wali Kelas\n\n";

if (!$shouldShow) {
    echo "🔧 TROUBLESHOOTING:\n";
    if (!in_array($userRole, $pembinaRoles)) {
        echo "   ❌ Wali Kelas tidak ada di pembina_roles\n";
        echo "   Expected: Wali Kelas harus ada di: " . implode(', ', $pembinaRoles) . "\n";
    }
    if ($waliKelas && $waliKelas->kelasDiampu && $siswa->kelas_id !== $waliKelas->kelasDiampu->id) {
        echo "   ❌ Kelas tidak match\n";
        echo "   Expected: Siswa di kelas {$waliKelas->kelasDiampu->nama_kelas}\n";
        echo "   Actual: Siswa di kelas {$siswa->kelas->nama_kelas}\n";
    }
    if ($waliKelas && !$waliKelas->kelasDiampu) {
        echo "   ⚠️  Wali Kelas tidak punya kelas binaan\n";
        echo "   Solution: Assign kelas ke wali kelas atau relax validation\n";
    }
}

echo "\n✅ Debug selesai!\n";
