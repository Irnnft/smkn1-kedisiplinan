# Template Tanda Tangan Dinamis - Surat Panggilan

## Overview
Sistem surat panggilan sekarang mendukung **5 template tanda tangan** yang berbeda berdasarkan pihak yang terlibat dalam penanganan kasus.

## Template yang Tersedia

### 1. **Template: Hanya Wali Kelas**
**Kondisi:** Checkbox yang dipilih: `Wali Kelas` saja
**Tanda Tangan:**
- Wali Kelas (centered)

---

### 2. **Template: Wali Kelas + Kaprodi**
**Kondisi:** Checkbox yang dipilih: `Wali Kelas` + `Kaprodi`
**Tanda Tangan:**
- Wali Kelas (kiri)
- Ketua Program Keahlian (kanan)

---

### 3. **Template: Wali Kelas + Waka Kesiswaan**
**Kondisi:** Checkbox yang dipilih: `Wali Kelas` + `Waka Kesiswaan`
**Tanda Tangan:**
- Wali Kelas (kiri)
- Waka Kesiswaan (kanan)

---

### 4. **Template: Wali Kelas + Kaprodi + Waka Kesiswaan**
**Kondisi:** Checkbox yang dipilih: `Wali Kelas` + `Kaprodi` + `Waka Kesiswaan`
**Tanda Tangan:**
- Wali Kelas (kiri)
- Ketua Program Keahlian (tengah)
- Waka Kesiswaan (kanan)

---

### 5. **Template: FULL (Default)**
**Kondisi:** 
- Semua pihak terlibat (Wali Kelas + Kaprodi + Waka + Kepsek)
- Atau kombinasi lain yang tidak masuk kategori 1-4

**Tanda Tangan:**
- Baris 1: Waka Kesiswaan (kiri) + Wali Kelas (kanan)
- Baris 2: Kepala Sekolah (centered, "Mengetahui")

**CATATAN:** Template Full tidak menampilkan Kaprodi dalam tanda tangan sesuai format surat resmi sekolah.

---

## Field Database

### Tabel: `surat_panggilan`
Field baru yang ditambahkan:

```sql
pihak_terlibat VARCHAR(255) NULLABLE
```

**Format Value:** Comma-separated string
**Contoh:**
- `"wali_kelas"` → Template 1
- `"wali_kelas,kaprodi"` → Template 2
- `"wali_kelas,waka_kesiswaan"` → Template 3
- `"wali_kelas,kaprodi,waka_kesiswaan"` → Template 4
- `"wali_kelas,waka_kesiswaan,kepala_sekolah"` → Template 5 (Full)

---

## Implementasi di Form Input Pelanggaran

Untuk mengimplementasikan ini di form input, tambahkan **checkbox** pada form "Trigger Surat Panggilan":

```blade
<div class="form-group">
    <label>Pihak Yang Terlibat:</label>
    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="pihak_terlibat[]" value="wali_kelas" checked>
            Wali Kelas
        </label>
    </div>
    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="pihak_terlibat[]" value="kaprodi">
            Ketua Program Keahlian (Kaprodi)
        </label>
    </div>
    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="pihak_terlibat[]" value="waka_kesiswaan">
            Wakil Kepala Sekolah Bidang Kesiswaan
        </label>
    </div>
    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="pihak_terlibat[]" value="kepala_sekolah">
            Kepala Sekolah
        </label>
    </div>
</div>
```

---

## Xử lý di Controller (Saat Menyimpan Surat)

Ketika menyimpan surat panggilan, convert array checkbox menjadi string:

```php
$pihakTerlibat = $request->input('pihak_terlibat', []);

SuratPanggilan::create([
    // ... field lainnya
    'pihak_terlibat' => implode(',', $pihakTerlibat),
]);
```

---

## Akses Berdasarkan Role

Sistem sudah mendukung akses dashboard berdasarkan pihak yang terlibat:

- **Jika hanya Wali Kelas** → Surat muncul di Dashboard Wali Kelas
- **Jika Wali Kelas + Kaprodi** → Surat muncul di Dashboard Wali Kelas & Dashboard Kaprodi
- **Jika melibatkan Waka/Kepsek** → Surat muncul di Dashboard Admin (Waka Kesiswaan)

Logic untuk ini perlu ditambahkan di query dashboard masing-masing.

---

## Testing

### Test Case 1: Hanya Wali Kelas
1. Buat pelanggaran baru
2. Trigger surat panggilan
3. Pilih checkbox: ✅ Wali Kelas
4. Cetak surat → Harus tampil template dengan 1 tanda tangan (Wali Kelas centered)

### Test Case 2: Wali + Kaprodi
1. Pilih checkbox: ✅ Wali Kelas, ✅ Kaprodi
2. Cetak surat → Harus tampil template dengan 2 tanda tangan (Wali kiri, Kaprodi kanan)

### Test Case 3: Wali + Waka
1. Pilih checkbox: ✅ Wali Kelas, ✅ Waka Kesiswaan
2. Cetak surat → Harus tampil template dengan 2 tanda tangan (Wali kiri, Waka kanan)

### Test Case 4: Wali + Kaprodi + Waka
1. Pilih checkbox: ✅ Wali Kelas, ✅ Kaprodi, ✅ Waka Kesiswaan
2. Cetak surat → Harus tampil template dengan 3 tanda tangan (3 kolom)

### Test Case 5: Full Template
1. Pilih checkbox: ✅ Wali Kelas, ✅ Waka Kesiswaan, ✅ Kepala Sekolah
2. Cetak surat → Harus tampil template penuh (2 baris: Waka+Wali, Kepsek)

---

## Relasi yang Diperlukan

Pastikan relasi berikut tersedia di Model:

### Model: Siswa
```php
public function kelas() {
    return $this->belongsTo(Kelas::class);
}
```

### Model: Kelas
```php
public function waliKelas() {
    return $this->belongsTo(User::class, 'wali_kelas_id');
}

public function jurusan() {
    return $this->belongsTo(Jurusan::class);
}
```

### Model: Jurusan
```php
public function kaprodi() {
    return $this->belongsTo(User::class, 'kaprodi_id');
}
```

---

## Troubleshooting

### Problem: PDF menampilkan template kosong
**Solution:** Pastikan field `pihak_terlibat` ada isi. Jika null, sistem akan fallback ke template full.

### Problem: Username tidak muncul
**Solution:** Pastikan relasi `waliKelas` dan `kaprodi` sudah eager loaded di controller (`TindakLanjutController@cetakSurat`)

### Problem: Error saat generate PDF
**Solution:** Cek apakah relasi chain (`$siswa->kelas->jurusan->kaprodi`) tidak null. Gunakan `??` operator untuk fallback.

---

## Migration Log

**File:** `2025_12_23_151458_add_pihak_terlibat_to_surat_panggilan_table.php`

**Status:** ✅ Sudah di-migrate (23 Desember 2025)

**SQL Generated:**
```sql
ALTER TABLE `surat_panggilan` 
ADD COLUMN `pihak_terlibat` VARCHAR(255) NULL AFTER `keperluan`;
```

---

## Update Log

### 23 Desember 2025
- ✅ Menambahkan field `pihak_terlibat` di tabel `surat_panggilan`
- ✅ Update `TindakLanjutController` untuk parse pihak terlibat
- ✅ Update Blade template dengan 5 variasi tanda tangan
- ✅ Mengganti field `nama` menjadi `username` untuk tanda tangan
- ✅ Bidang keahlian sekarang dinamis sesuai jurusan siswa
- ✅ Format tanggal menggunakan Bahasa Indonesia
- ✅ Format waktu hanya menampilkan HH:MM (tanpa detik)

---

## Next Steps (Untuk Developer)

1. **Update Form Input Pelanggaran:**
   - Tambahkan checkbox `pihak_terlibat[]` di form create/edit pelanggaran
   - Save array checkbox sebagai comma-separated string

2. **Update Dashboard Logic:**
   - Filter surat di Dashboard Wali Kelas: `WHERE pihak_terlibat LIKE '%wali_kelas%'`
   - Filter surat di Dashboard Kaprodi: `WHERE pihak_terlibat LIKE '%kaprodi%'`
   - Dst.

3. **Validation:**
   - Minimal 1 pihak harus dipilih
   - Wali Kelas wajib ada (tidak bisa kosong)

4. **UI/UX:**
   - Tambahkan tooltip/helper text untuk menjelaskan perbedaan template
   - Preview template berdasarkan checkbox yang dipilih (optional)
