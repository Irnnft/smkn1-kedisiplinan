# BUGFIX: Template Tanda Tangan Tidak Berubah

## ❌ Problem

User melaporkan:
> "Saat saya membuat rule dengan ceklis Wali Kelas saja, lalu cetak surat, **template masih 3 tanda tangan** (Waka + Wali + Kepsek). Begitu juga dengan kombinasi lain, **template selalu sama**."

---

## 🔍 Root Cause

### Flow Data (Sebelum Fix):

1. ✅ User pilih pembina di Frequency Rules (e.g., "Wali Kelas")
2. ✅ Sistem catat pelanggaran → trigger create surat
3. ❌ **Saat create surat**: Field `pembina_roles` **TIDAK DISIMPAN** ke database
4. ❌ Controller `cetakSurat`: Ambil `pembina_roles` → dapat **NULL/kosong**
5. ❌ Template PDF fallback ke **template default** (full: 3 tanda tangan)

### Penyebabnya:

Di file `PelanggaranRulesEngine.php`, saat create/update surat:

```php
// ❌ SEBELUMNYA (SALAH)
$tl->suratPanggilan()->create([
    'nomor_surat' => ...,
    'tipe_surat' => ...,
    'pembina_data' => $pembinaData,  // ✅ Ada
    // ❌ TIDAK ADA pembina_roles!
    'tanggal_pertemuan' => ...,
    'waktu_pertemuan' => ...,
]);
```

Field `pembina_roles` tidak disave, jadi saat generate PDF, controller tidak punya data untuk mapping ke template!

---

## ✅ Solution

### Changes Made:

#### File: `app/Services/Pelanggaran/PelanggaranRulesEngine.php`

**3 Tempat yang diubah:**

1. **Line 519-528** (Create surat pertama kali)
```php
$tl->suratPanggilan()->create([
    'nomor_surat' => $suratService->generateNomorSurat(),
    'tipe_surat' => $tipeSurat,
    'tanggal_surat' => now(),
    'pembina_data' => $pembinaData,
    'pembina_roles' => $pembinaRoles,  // ✅ ADDED
    'tanggal_pertemuan' => $meetingSchedule['tanggal_pertemuan'],
    'waktu_pertemuan' => $meetingSchedule['waktu_pertemuan'],
    'keperluan' => $pemicu,
]);
```

2. **Line 638-643** (Update surat - rekonsiliasi)
```php
$kasusAktif->suratPanggilan()->update([
    'tipe_surat' => $tipeSurat,
    'pembina_data' => $pembinaData,
    'pembina_roles' => $pembinaRolesForSurat,  // ✅ ADDED
    'keperluan' => 'Rekonsiliasi',
]);
```

3. **Line 649-658** (Create surat - rekonsiliasi)
```php
$kasusAktif->suratPanggilan()->create([
    'nomor_surat' => $suratService->generateNomorSurat(),
    'tipe_surat' => $tipeSurat,
    'tanggal_surat' => now(),
    'pembina_data' => $pembinaData,
    'pembina_roles' => $pembinaRolesForSurat,  // ✅ ADDED
    'tanggal_pertemuan' => $meetingSchedule['tanggal_pertemuan'],
    'waktu_pertemuan' => $meetingSchedule['waktu_pertemuan'],
    'keperluan' => 'Rekonsiliasi',
]);
```

4. **Line 723-728** (Update surat - eskalasi)
```php
$kasusAktif->suratPanggilan()->update([
    'tipe_surat' => $tipeSuratBaru,
    'pembina_data' => $pembinaData,
    'pembina_roles' => $pembinaRoles,  // ✅ ADDED
    'keperluan' => $pemicuBaru,
]);
```

---

## 📊 Flow Data (Setelah Fix):

### 1. CREATE RULE
User pilih pembina di Frequency Rules:
- Contoh: `["Wali Kelas"]`
- Tersimpan di `pelanggaran_frequency_rules.pembina_roles`

### 2. CATAT PELANGGARAN
Sistem evaluasi rule → match:
- Ambil `pembina_roles` dari rule: `["Wali Kelas"]`
- **Create surat** dengan `pembina_roles: ["Wali Kelas"]` ✅
- Tersimpan di `surat_panggilan.pembina_roles`

### 3. CETAK SURAT
Controller `cetakSurat`:
```php
// Ambil pembina_roles dari database
$pembinaRoles = $kasus->suratPanggilan->pembina_roles; // ["Wali Kelas"]

// Mapping ke format template
$pihakTerlibat = [
    'wali_kelas' => true,   // ✅ Ada
    'kaprodi' => false,
    'waka_kesiswaan' => false,
    'kepala_sekolah' => false,
];

// Pass ke PDF view
return Pdf::loadView('pdf.surat-panggilan', [
    'pihakTerlibat' => $pihakTerlibat,  // ✅ Data lengkap
]);
```

### 4. PDF TEMPLATE
Blade template:
```php
@if($templateType === 'wali_only')
    // ✅ Render HANYA tanda tangan Wali Kelas
    <td colspan="2" align="center">
        Wali Kelas
        <strong>{{ $siswa->kelas->waliKelas->username }}</strong>
    </td>
@endif
```

---

## 🧪 Testing

### Test Case 1: Wali Kelas Only
1. Buat rule dengan pembina: `Wali Kelas`
2. Catat pelanggaran
3. Cetak surat
4. **Expected**: PDF dengan 1 tanda tangan (Wali Kelas, centered)

### Test Case 2: Wali + Kaprodi
1. Buat rule dengan pembina: `Wali Kelas + Kaprodi`
2. Catat pelanggaran
3. Cetak surat
4. **Expected**: PDF dengan 2 tanda tangan (Wali kiri, Kaprodi kanan)

### Test Case 3: Wali + Waka + Kepsek
1. Buat rule dengan pembina: `Wali Kelas + Waka Kesiswaan + Kepala Sekolah`
2. Catat pelanggaran
3. Cetak surat
4. **Expected**: PDF dengan template full (2 baris)

---

## ✅ Verification

### Check Database
```sql
-- Lihat data surat yang baru dibuat
SELECT id, pembina_roles, pembina_data, tipe_surat 
FROM surat_panggilan 
ORDER BY created_at DESC 
LIMIT 5;
```

**Expected**: Field `pembina_roles` berisi JSON array, contoh:
```json
["Wali Kelas"]
["Wali Kelas", "Kaprodi"]
["Wali Kelas", "Waka Kesiswaan", "Kepala Sekolah"]
```

### Check PDF Output
1. Generate PDF
2. Hitung jumlah tanda tangan
3. Verify sesuai dengan `pembina_roles`

---

## 🎯 Bonus: Ubah Teks Tombol

User menyarankan:
> "Lebih bagus tombolnya hanya teks 'Cetak Surat' dibandingkan teks di tombolnya berbeda"

Implementasi (opsional):
```blade
{{-- SEBELUMNYA --}}
<button>Cetak {{ $kasus->suratPanggilan->tipe_surat }}</button>

{{-- SETELAH (LEBIH GENERIC) --}}
<button>Cetak Surat</button>
```

Tipe surat tetap penting untuk **internal logic** (status, approval), tapi **tidak perlu ditampilkan di tombol**.

---

## 📝 Summary

### Fixed:
- ✅ Field `pembina_roles` sekarang tersimpan saat create/update surat
- ✅ Controller dapat mapping data ke template
- ✅ Template PDF render sesuai kombinasi pembina

### Files Changed:
1. `app/Services/Pelanggaran/PelanggaranRulesEngine.php` (4 tempat)

### Impact:
- ✅ Template tanda tangan sekarang **DINAMIS**
- ✅ Sesuai dengan pembina yang dipilih di Frequency Rules
- ✅ Tidak perlu template file terpisah (semua dalam 1 Blade file)

**Status**: ✅ FIXED & READY FOR TESTING
