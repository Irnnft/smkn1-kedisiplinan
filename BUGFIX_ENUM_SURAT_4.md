# BUGFIX: Pelanggaran Gagal Tercatat untuk 4 Pembina

## ❌ Problem

User melaporkan:
> "Saat saya menetapkan pembina: Wali Kelas + Kaprodi + Waka Kesiswaan + Kepala Sekolah, lalu catat pelanggaran → **GAGAL!** Tidak tercatat pelanggaran sama sekali."

**Yang Berhasil:**
- ✅ Wali Kelas saja
- ✅ Wali Kelas + Kaprodi
- ✅ Wali Kelas + Kaprodi + Waka Kesiswaan

**Yang Gagal:**
- ❌ Wali Kelas + Kaprodi + Waka Kesiswaan + Kepala Sekolah (4 pembina)

---

## 🔍 Root Cause

### Database Constraint Issue

Tabel `surat_panggilan` punya field `tipe_surat` dengan **ENUM constraint**:

```sql
-- SEBELUM FIX (SALAH)
tipe_surat ENUM('Surat 1', 'Surat 2', 'Surat 3')
```

**Masalahnya:**
1. Rule dengan 4 pembina → `getSuratType()` return `'Surat 4'` ✅
2. Sistem coba insert `'Surat 4'` ke database ❌
3. **MySQL reject** karena `'Surat 4'` tidak ada di ENUM values
4. **Exception thrown** → Transaction rollback
5. Pelanggaran **TIDAK TERCATAT**

---

## ✅ Solution

### Migration untuk Update ENUM

**File**: `2025_12_23_161740_update_tipe_surat_enum_to_include_surat_4.php`

```php
public function up(): void
{
    // Step 1: Change to VARCHAR temporarily (workaround for MySQL ENUM)
    DB::statement("ALTER TABLE surat_panggilan MODIFY COLUMN tipe_surat VARCHAR(10)");
    
    // Step 2: Change back to ENUM with new values
    DB::statement("ALTER TABLE surat_panggilan MODIFY COLUMN tipe_surat ENUM('Surat 1', 'Surat 2', 'Surat 3', 'Surat 4') NOT NULL");
}
```

**Why VARCHAR First?**
MySQL tidak support `ALTER ENUM` langsung. Solusinya:
1. Convert ke VARCHAR dulu (temporary storage)
2. Convert kembali ke ENUM dengan values baru

---

## 📊 Mapping Pembina → Tipe Surat

| Pembina Selected | Count | Tipe Surat | Status |
|------------------|-------|------------|--------|
| Wali Kelas | 1 | Surat 1 | Baru |
| Wali + Kaprodi | 2 | Surat 2 | Baru |
| Wali + Kaprodi + Waka | 3 | Surat 3 | Menunggu Persetujuan |
| Wali + Kaprodi + Waka + Kepsek | **4+** | **Surat 4** ✅ | Menunggu Persetujuan |

**Logic:**
- Jika ada **"Kepala Sekolah"** dalam pembina → Status: `Menunggu Persetujuan`
- Jika **TIDAK ada** Kepala Sekolah → Status: `Baru`

---

## 🔄 Flow Data (After Fix):

### 1. CREATE RULE
User pilih 4 pembina:
```php
$pembinaRoles = [
    'Wali Kelas',
    'Kaprodi',
    'Waka Kesiswaan',
    'Kepala Sekolah'
];
```

### 2. CATAT PELANGGARAN
Sistem evaluasi:
```php
$tipeSurat = $rule->getSuratType(); 
// → "Surat 4" ✅

$status = $this->tentukanStatusBerdasarkanPembina($pembinaRoles);
// →'Menunggu Persetujuan' (karena ada Kepala Sekolah)
```

### 3. CREATE SURAT
```php
$tl->suratPanggilan()->create([
    'tipe_surat' => 'Surat 4',  // ✅ SEKARANG VALID!
    'pembina_roles' => [...],
    // ... fields lainnya
]);
```

### 4. RESULT
- ✅ Pelanggaran tercatat di `log_pelanggaran`
- ✅ Kasus tercatat di `tindak_lanjut`
- ✅ Surat tercatat di `surat_panggilan` dengan `tipe_surat = 'Surat 4'`
- ✅ Status: `Menunggu Persetujuan` (perlu approval Kepala Sekolah)

---

## 🧪 Testing

### Test Case: 4 Pembina
1. Buat rule dengan pembina: `Wali Kelas + Kaprodi + Waka Kesiswaan + Kepala Sekolah`
2. Set trigger surat: ON
3. Catat pelanggaran untuk siswa
4. **Expected**:
   - ✅ Pelanggaran tercatat
   - ✅ Kasus muncul dengan status "Menunggu Persetujuan"
   - ✅ Surat tipe "Surat 4" dibuat
   - ✅ Muncul di Dashboard Kepala Sekolah untuk approval

### Test Case: Generate PDF
1. Kepala Sekolah approve kasus
2. Buka detail kasus
3. Klik "Cetak Surat"
4. **Expected**:
   - ✅ PDF ter-generate
   - ✅ Template: Waka + Wali (2 kolom) + Kepala Sekolah (centered bawah)
   - ✅ **TANPA Kaprodi** (sesuai template full)

---

## 📝 Note Penting

### Tentang Template PDF "Surat 4"

Meskipun pembina yang dipilih ada 4 (Wali + Kaprodi + Waka + Kepsek), **template PDF tetap menggunakan format "Full" klasik** yang hanya menampilkan:
- Waka Kesiswaan (kiri)
- Wali Kelas (kanan)
- Kepala Sekolah (bawah, centered dengan "Mengetahui")

**Kaprodi TIDAK DITAMPILKAN** karena format surat resmi sekolah tidak accommodrate 4 tanda tangan.

### Logic di Blade:
```php
@if($pihakTerlibat['kepala_sekolah'])
    // Template Full (tanpa Kaprodi)
    <tr>
        <td>Waka Kesiswaan</td>
        <td>Wali Kelas</td>
    </tr>
    <tr>
        <td colspan="2">Kepala Sekolah (Mengetahui)</td>
    </tr>
@endif
```

---

## 🔧 Files Changed

### 1. Migration
- **File**: `2025_12_23_161740_update_tipe_surat_enum_to_include_surat_4.php`
- **Change**: Add `'Surat 4'` to ENUM values

### Database Schema (After):
```sql
CREATE TABLE surat_panggilan (
    ...
    tipe_surat ENUM('Surat 1', 'Surat 2', 'Surat 3', 'Surat 4') NOT NULL,
    ...
);
```

---

## ✅ Verification

### Check ENUM Values
```sql
SHOW COLUMNS FROM surat_panggilan WHERE Field = 'tipe_surat';
```

**Expected Output:**
```
Field: tipe_surat
Type: enum('Surat 1','Surat 2','Surat 3','Surat 4')
```

### Check Data
```sql
SELECT id, tipe_surat, pembina_roles, status 
FROM surat_panggilan 
WHERE tipe_surat = 'Surat 4'
LIMIT 5;
```

**Expected**: Rows dengan `tipe_surat = 'Surat 4'`

---

## 📌 Summary

### Problem:
- ENUM constraint terlalu restrictive (hanya 3 values)
- MySQL reject insert 'Surat 4'
- Pelanggaran gagal tercatat (silent failure)

### Solution:
- ✅ Update ENUM: add `'Surat 4'`
- ✅ Migration executed successfully

### Impact:
- ✅ Sistem sekarang support 4 tipe surat
- ✅ Kombinasi 4 pembina bisa tercatat
- ✅ PDF template tetap menggunakan format "Full" (3 tanda tangan)

**Status**: ✅ FIXED & TESTED
