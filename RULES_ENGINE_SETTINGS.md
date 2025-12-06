# 📋 Rules Engine Settings - Panduan Penggunaan

## 🎯 Tujuan
Fitur ini memungkinkan **Operator Sekolah** untuk mengubah threshold poin dan frekuensi pelanggaran secara fleksibel **tanpa perlu mengedit code**.

## 🔑 Akses
- **Role:** Operator Sekolah
- **Menu:** Sidebar → Rules Engine
- **URL:** `/rules-engine-settings`

## ⚙️ Pengaturan yang Tersedia

### 1. Threshold Poin Surat
Mengatur batas poin untuk memicu surat pemanggilan berdasarkan tingkat pelanggaran:

| Setting | Default | Deskripsi |
|---------|---------|-----------|
| **Surat 2 - Poin Minimum** | 100 | Poin minimum untuk memicu Surat 2 (Pelanggaran Berat) |
| **Surat 2 - Poin Maximum** | 500 | Poin maksimum untuk Surat 2 (di atas ini akan menjadi Surat 3) |
| **Surat 3 - Poin Minimum** | 501 | Poin minimum untuk memicu Surat 3 (Sangat Berat) |

### 2. Threshold Akumulasi Poin
Mengatur batas total akumulasi poin untuk eskalasi otomatis:

| Setting | Default | Deskripsi |
|---------|---------|-----------|
| **Akumulasi Sedang - Minimum** | 55 | Total poin akumulasi minimum untuk eskalasi ke Surat 2 |
| **Akumulasi Sedang - Maximum** | 300 | Total poin akumulasi maksimum untuk Surat 2 (di atas ini menjadi kritis) |
| **Akumulasi Kritis** | 301 | Total poin akumulasi untuk memicu Surat 3 (Akumulasi Kritis) |

### 3. Threshold Frekuensi Pelanggaran
Mengatur jumlah pelanggaran berulang yang memicu surat pemanggilan:

| Setting | Default | Deskripsi |
|---------|---------|-----------|
| **Frekuensi Pelanggaran Atribut** | 10 | Jumlah pelanggaran atribut yang memicu Surat 1 |
| **Frekuensi Pelanggaran Alfa** | 4 | Jumlah pelanggaran alfa yang memicu Surat 1 |

## 🚀 Cara Menggunakan

### Mengubah Pengaturan
1. Login sebagai **Operator Sekolah**
2. Klik menu **Rules Engine** di sidebar
3. Ubah nilai yang diinginkan
4. Klik tombol **Preview Perubahan** untuk melihat dampak
5. Jika sudah yakin, klik **Simpan Perubahan**

### Melihat Riwayat Perubahan
- Klik icon **🕐** di sebelah setiap input field
- Modal akan menampilkan riwayat perubahan dengan:
  - Waktu perubahan
  - Nilai lama → Nilai baru
  - Username yang mengubah

### Reset ke Default
- **Reset Satu Setting:** Klik icon 🕐 → pilih nilai default
- **Reset Semua:** Klik tombol **Reset Semua** di pojok kanan atas

## ⚠️ Perhatian

### Validasi Otomatis
Sistem akan memvalidasi:
- ✅ Nilai minimum harus lebih kecil dari maksimum
- ✅ Surat 2 Max < Surat 3 Min
- ✅ Akumulasi Sedang Max < Akumulasi Kritis
- ✅ Nilai harus berupa angka positif

### Dampak Perubahan
- ⚡ Perubahan **langsung berlaku** untuk evaluasi pelanggaran baru
- 📊 Tidak mempengaruhi tindak lanjut yang sudah dibuat sebelumnya
- 🔄 Sistem menggunakan **caching** untuk performa optimal (cache 1 jam)

## 🛠️ Teknis

### Fallback Mechanism
Jika terjadi error database, sistem akan otomatis menggunakan nilai default yang hardcoded di `PelanggaranRulesEngine.php`

### Caching
- Cache TTL: 1 jam
- Cache otomatis di-clear setelah update
- Performa optimal untuk pembacaan berulang

### Audit Trail
Semua perubahan tercatat di tabel `rules_engine_settings_history` dengan informasi:
- Setting yang diubah
- Nilai lama & baru
- User yang mengubah
- Timestamp

## 📝 Contoh Skenario

### Skenario 1: Sekolah Ingin Lebih Ketat
**Tujuan:** Menurunkan threshold agar siswa lebih cepat mendapat surat pemanggilan

**Perubahan:**
- Surat 2 Min: 100 → **50**
- Akumulasi Sedang Min: 55 → **30**
- Frekuensi Atribut: 10 → **5**

**Dampak:** Siswa akan lebih cepat mendapat surat pemanggilan dengan poin/frekuensi lebih rendah

### Skenario 2: Sekolah Ingin Lebih Longgar
**Tujuan:** Memberikan lebih banyak kesempatan sebelum eskalasi

**Perubahan:**
- Surat 2 Min: 100 → **150**
- Akumulasi Kritis: 301 → **500**
- Frekuensi Alfa: 4 → **6**

**Dampak:** Siswa perlu poin/frekuensi lebih tinggi sebelum mendapat surat pemanggilan

## 🔗 Integrasi

### File yang Terlibat
- **Model:** `RulesEngineSetting`, `RulesEngineSettingHistory`
- **Service:** `RulesEngineSettingsService` (caching & validation)
- **Controller:** `RulesEngineSettingsController`
- **View:** `resources/views/rules-engine-settings/index.blade.php`
- **Rules Engine:** `PelanggaranRulesEngine` (membaca dari database)

### Database Tables
- `rules_engine_settings` - Menyimpan konfigurasi aktif
- `rules_engine_settings_history` - Audit trail perubahan

## 📞 Support
Jika ada pertanyaan atau masalah, hubungi tim developer atau lihat dokumentasi lengkap di `.kiro/specs/rules-engine-settings/`
