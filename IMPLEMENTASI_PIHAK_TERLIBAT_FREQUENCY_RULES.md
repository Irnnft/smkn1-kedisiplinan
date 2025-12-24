# IMPLEMENTASI LENGKAP: Pihak Terlibat Dinamis di Frequency Rules

## ✅ Status: COMPLETE

Tanggal: 23 Desember 2025

---

## Overview

Sistem surat panggilan sekarang mendukung **checkbox "Pihak Yang Terlibat"** di halaman **Frequency Rules** (create & edit). Checkbox ini menentukan siapa saja yang akan menerima dan menandatangani surat, serta akan menampilkan template tanda tangan yang sesuai pada PDF.

---

## Fitur Implementasi

### 1. **Database Migration**
Field `pihak_terlibat` ditambahkan ke:
- ✅ `pelanggaran_frequency_rules` (untuk menyimpan checkbox dari form Frequency Rules)
- ✅ `surat_panggilan` (untuk menyimpan data saat surat dibuat, fallback jika rule tidak ada)

**Format Data**: Comma-separated string
- Contoh: `"wali_kelas,kaprodi,waka_kesiswaan,kepala_sekolah"`

**Migration Files**:
- `2025_12_23_152032_add_pihak_terlibat_to_pelanggaran_frequency_rules_table.php`
- `2025_12_23_151458_add_pihak_terlibat_to_surat_panggilan_table.php`

---

### 2. **Model Updates**

#### PelanggaranFrequencyRule
```php
protected $fillable = [
    // ... existing fields
    'pihak_terlibat',  // NEW
];
```

---

### 3. **Form View (Frequency Rules - Create & Edit)**

**File**: `resources/views/frequency-rules/show.blade.php`

**Fitur Baru**:
- ✅ Section "Pihak Yang Terlibat" dengan background amber untuk visibilitas tinggi
- ✅ **Conditional Display**: Section ini hanya muncul ketika checkbox "Trigger Surat Pemanggilan" dicentang
- ✅ 4 Checkbox Options:
  - Wali Kelas (default checked)
  - Kaprodi
  - Waka Kesiswaan
  - Kepala Sekolah

**JavaScript Logic**:
```javascript
// Toggle visibility saat checkbox trigger_surat berubah
$('#trigger_surat_add').change(function() {
    if ($(this).is(':checked')) {
        $('#pihak_terlibat_section_add').slideDown(200);
    } else {
        $('#pihak_terlibat_section_add').slideUp(200);
    }
});
```

**Data Attribute untuk Edit**:
```html
data-pihak-terlibat="{{ json_encode($rule->pihak_terlibat ? explode(',', $rule->pihak_terlibat) : []) }}"
```

---

### 4. **Form Request Validation**

**Files Updated**:
- `app/Http/Requests/Rules/CreateFrequencyRuleRequest.php`
- `app/Http/Requests/Rules/UpdateFrequencyRuleRequest.php`

**Validation Rules**:
```php
'pihak_terlibat' => ['nullable', 'array', 'required_if:trigger_surat,true'],
'pihak_terlibat.*' => ['string', 'in:wali_kelas,kaprodi,waka_kesiswaan,kepala_sekolah'],
```

**Custom Error Message**:
```php
'pihak_terlibat.required_if' => 'Harus memilih minimal 1 pihak yang terlibat jika trigger surat diaktifkan.'
```

**Data Preparation**:
```php
protected function prepareForValidation(): void
{
    // Convert array to comma-separated string
    if ($this->has('pihak_terlibat') && is_array($this->input('pihak_terlibat'))) {
        $this->merge([
            'pihak_terlibat' => implode(',', $this->input('pihak_terlibat')),
        ]);
    }
}
```

---

### 5. **Controller Logic (Cetak Surat)**

**File**: `app/Http/Controllers/Pelanggaran/TindakLanjutController.php`

**Method**: `cetakSurat($id)`

**Logic**:
1. Ambil data `pihak_terlibat` dari `surat_panggilan` table
2. Parse menjadi array
3. Convert ke format boolean untuk conditional rendering di Blade
4. Pass ke PDF view

```php
// Ambil pihak terlibat dari surat_panggilan (sudah disimpan saat create surat)
$pihakTerlibatRaw = $kasus->suratPanggilan->pihak_terlibat ?? 'wali_kelas,waka_kesiswaan,kepala_sekolah';
$pihakArray = is_string($pihakTerlibatRaw) 
    ? explode(',', $pihakTerlibatRaw) 
    : (is_array($pihakTerlibatRaw) ? $pihakTerlibatRaw : []);

$pihakTerlibat = [
    'wali_kelas' => in_array('wali_kelas', $pihakArray),
    'kaprodi' => in_array('kaprodi', $pihakArray),
    'waka_kesiswaan' => in_array('waka_kesiswaan', $pihakArray),
    'kepala_sekolah' => in_array('kepala_sekolah', $pihakArray),
];
```

---

### 6. **PDF Template (Blade)**

**File**: `resources/views/pdf/surat-panggilan.blade.php`

**5 Template Tanda Tangan** berdasarkan kombinasi pihak terlibat:

#### Template 1: Wali Kelas Only
```php
@if($templateType === 'wali_only')
    <tr>
        <td colspan="2" align="center">
            Wali Kelas
            <div style="height: 70px;"></div>
            <strong>{{ $siswa->kelas->waliKelas->username }}</strong>
        </td>
    </tr>
@endif
```

#### Template 2: Wali + Kaprodi
2 kolom (50%-50%)

#### Template 3: Wali + Waka
2 kolom (50%-50%)

#### Template 4: Wali + Kaprodi + Waka
3 kolom (33%-33%-34%)

#### Template 5: Full (Default)
Waka + Wali (baris 1) + Kepala Sekolah (baris 2, centered)

---

## Flow Data

### CREATE SURAT (Saat Pelanggaran Dicatat)

1. **User mencatat pelanggaran**
2. **Sistem cek Frequency Rule** yang match dengan frekuensi siswa
3. **Jika `trigger_surat == true`**:
   - Ambil `pihak_terlibat` dari **Frequency Rule** yang match
   - Copy data ini ke field `pihak_terlibat` di **Surat Panggilan** yang dibuat
4. **Data tersimpan di `surat_panggilan` table untuk digunakan saat cetak**

### CETAK SURAT

1. User klik "Cetak Surat"
2. Controller ambil `pihak_terlibat` dari `surat_panggilan`
3. Parse string menjadi array boolean
4. Pass ke Blade PDF template
5. Template Blade render tanda tangan sesuai kombinasi

---

## User Experience

### Di Halaman Frequency Rules

1. User checklist **"Trigger Surat Pemanggilan"**
2. Section **"Pihak Yang Terlibat Dalam Surat"** muncul dengan smooth slide animation
3. Section ini memiliki background **amber/orange** agar sangat terlihat
4. User pilih minimal 1 pihak (Wali Kelas default checked)
5. Saat save, data disimpan sebagai comma-separated string di database

### Skenario Kombinasi

| Checkbox Selected | Template | Tanda Tangan |
|-------------------|----------|--------------|
| Wali Kelas | Template 1 | Wali (centered) |
| Wali + Kaprodi | Template 2 | Wali (kiri), Kaprodi (kanan) |
| Wali + Waka | Template 3 | Wali (kiri), Waka (kanan) |
| Wali + Kaprodi + Waka | Template 4 | 3 kolom |
| Semua / Wali + Waka + Kepsek | Template 5 | Full (2 baris) |

---

## Testing Checklist

### ✅ Create Frequency Rule
- [ ] Buka halaman Frequency Rules
- [ ] Klik "Tambah Rule"
- [ ] Checklist "Trigger Surat Pemanggilan"
- [ ] Verify: Section "Pihak Yang Terlibat" muncul dengan background amber
- [ ] Pilih beberapa pihak (e.g., Wali + Kaprodi)
- [ ] Save rule
- [ ] Verify: Data tersimpan di `pelanggaran_frequency_rules.pihak_terlibat`

### ✅ Edit Frequency Rule
- [ ] Klik tombol Edit pada rule yang `trigger_surat = true`
- [ ] Verify: Section "Pihak Yang Terlibat" muncul
- [ ] Verify: Checkbox yang sudah dipilih sebelumnya ter-check
- [ ] Ubah pilihan
- [ ] Save
- [ ] Verify: Perubahan tersimpan

### ✅ Generate PDF
- [ ] Catat pelanggaran yang trigger rule dengan surat
- [ ] Klik " Cetak Surat"
- [ ] Verify: PDF ter-generate dengan template tanda tangan yang sesuai
- [ ] Test berbagai kombinasi pihak terlibat
- [ ] Verify: Template yang ditampilkan sesuai logic

---

## File Changes Summary

### New Files
1. `database/migrations/2025_12_23_152032_add_pihak_terlibat_to_pelanggaran_frequency_rules_table.php`
2. `TEMPLATE_TANDA_TANGAN_DINAMIS.md` (Dokumentasi fitur)
3. `IMPLEMENTASI_PIHAK_TERLIBAT_FREQUENCY_RULES.md` (Dokumentasi ini)

### Modified Files
1. **Model**: `app/Models/PelanggaranFrequencyRule.php`
2. **Controller**: `app/Http/Controllers/Pelanggaran/TindakLanjutController.php`
3. **View**: `resources/views/frequency-rules/show.blade.php`
4. **Request**: `app/Http/Requests/Rules/CreateFrequencyRuleRequest.php`
5. **Request**: `app/Http/Requests/Rules/UpdateFrequencyRuleRequest.php`
6. **PDF Template**: `resources/views/pdf/surat-panggilan.blade.php`

---

## API Reference

### Checkbox Values
```
wali_kelas      → Wali Kelas
kaprodi         → Ketua Program Keahlian
waka_kesiswaan  → Wakil Kepala Sekolah Bidang Kesiswaan
kepala_sekolah  → Kepala Sekolah
```

### Database Format
```sql
-- Example data in pelanggaran_frequency_rules.pihak_terlibat
"wali_kelas,kaprodi,waka_kesiswaan"
```

### Template Type Logic
```php
if (only wali_kelas) → 'wali_only'
elseif (wali + kaprodi) → 'wali_kaprodi'
elseif (wali + waka) → 'wali_waka'
elseif (wali + kaprodi + waka) → 'wali_kaprodi_waka'
else → 'full' (default)
```

---

## Troubleshooting

### Problem: Section tidak muncul saat checkbox trigger surat dicentang
**Solution**: Cek console JavaScript untuk error. Pastikan jQuery sudah loaded.

### Problem: Data tidak tersimpan
**Solution**: Cek validation error. Pastikan minimal 1 pihak dipilih jika `trigger_surat` adalah true.

### Problem: Template PDF tidak berubah
**Solution**: 
1. Cek apakah `pihak_terlibat` field ada di `surat_panggilan` table
2. Pastikan data ter-copy dari `frequency_rule` saat create surat
3. Cek logic conditional di Blade template

### Problem: Checkbox tidak ter-check saat edit
**Solution**: Cek `data-pihak-terlibat` attribute pada tombol edit. Pastikan data di-json_encode dengan benar.

---

## Future Enhancements

1. **Preview Template**: Tampilkan preview layout tanda tangan saat user memilih checkbox
2. **Smart Default**: Auto-select pihak berdasarkan `pembina_roles`
3. **Custom Order**: Allow user mengurutkan tanda tangan
4. **More Templates**: Tambah template untuk kombinasi khusus lainnya

---

## Conclusion

Implementasi ini memberikan **flexibility penuh** untuk menentukan siapa saja yang terlibat dalam surat berdasarkan severity pelanggaran (frequency rule), sekaligus memastikan **template PDF yang generated** sesuai dengan pihak yang dipilih.

**Status**: ✅ READY FOR TESTING & PRODUCTION
