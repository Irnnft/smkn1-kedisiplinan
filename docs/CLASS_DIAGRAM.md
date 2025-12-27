# 📊 CLASS DIAGRAM - SISTEM INFORMASI KEDISIPLINAN SISWA SMK NEGERI 1

## 🎯 RINGKASAN SISTEM

Sistem Informasi Kedisiplinan Siswa adalah aplikasi berbasis web yang dibangun menggunakan **Laravel Framework** untuk mengelola:
- Data pengguna berdasarkan role (Kepala Sekolah, Waka, Kaprodi, Wali Kelas, Guru, Wali Murid)
- Data siswa dan kelas  
- Pencatatan pelanggaran siswa dengan sistem frequency rules
- Tindak lanjut dan pembinaan internal
- Surat panggilan orang tua (4 tingkatan)

---

## 📋 CLASS DIAGRAM (UML Notation)

### 🔐 **MASTER DATA - USER & ROLE**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                               USER MANAGEMENT                                        │
└─────────────────────────────────────────────────────────────────────────────────────┘

                            ┌─────────────────────────────┐
                            │           Role              │
                            ├─────────────────────────────┤
                            │ - id: bigint (PK)           │
                            │ - nama_role: string         │
                            ├─────────────────────────────┤
                            │ + users(): HasMany          │
                            │ + findByName(): ?Role       │
                            └──────────────┬──────────────┘
                                           │ 1
                                           │
                                           │ *
                            ┌──────────────▼──────────────┐
                            │           User              │
                            ├─────────────────────────────┤
                            │ - id: bigint (PK)           │
                            │ - role_id: FK(roles)        │
                            │ - nama: string              │
                            │ - username: string (unique) │
                            │ - email: string (unique)    │
                            │ - password: string          │
                            │ - phone: string (nullable)  │
                            │ - nip: string (nullable)    │
                            │ - nuptk: string (nullable)  │
                            │ - is_active: boolean        │
                            │ - last_login_at: timestamp  │
                            │ - email_verified_at: ts     │
                            │ - password_changed_at: ts   │
                            │ - username_changed_at: ts   │
                            ├─────────────────────────────┤
                            │ + role(): BelongsTo         │
                            │ + jurusanDiampu(): HasOne   │  ← Kaprodi
                            │ + kelasDiampu(): HasOne     │  ← Wali Kelas
                            │ + anakWali(): HasMany       │  ← Wali Murid
                            │ + riwayatDicatat(): HasMany │  ← Guru
                            │ + tindakLanjutDisetujui()   │
                            │ + hasRole(roles): bool      │
                            │ + isTeacher(): bool         │
                            │ + isWaliKelas(): bool       │
                            │ + isKaprodi(): bool         │
                            │ + isWaliMurid(): bool       │
                            │ + isDeveloper(): bool       │
                            │ + canViewStudent(): bool    │
                            │ + canRecordFor(): bool      │
                            └─────────────────────────────┘
```

---

### 🏫 **MASTER DATA - SEKOLAH**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           STRUKTUR SEKOLAH                                           │
└─────────────────────────────────────────────────────────────────────────────────────┘

        ┌───────────────────────────┐          ┌───────────────────────────┐
        │         Jurusan           │          │          Kelas            │
        ├───────────────────────────┤          ├───────────────────────────┤
        │ - id: bigint (PK)         │    1   * │ - id: bigint (PK)         │
        │ - kaprodi_user_id: FK     │◄─────────│ - jurusan_id: FK          │
        │ - nama_jurusan: string    │          │ - wali_kelas_user_id: FK  │
        │ - kode_jurusan: string    │          │ - nama_kelas: string      │
        ├───────────────────────────┤          │ - tingkat: string(X/XI/XII)│
        │ + kaprodi(): BelongsTo    │          ├───────────────────────────┤
        │ + kelas(): HasMany        │          │ + jurusan(): BelongsTo    │
        │ + siswa(): HasManyThrough │          │ + waliKelas(): BelongsTo  │
        └───────────────────────────┘          │ + siswa(): HasMany        │
                                               └─────────────┬─────────────┘
                                                             │ 1
                                                             │
                                                             │ *
                                               ┌─────────────▼─────────────┐
                                               │          Siswa            │
                                               ├───────────────────────────┤
                                               │ - id: bigint (PK)         │
                                               │ - kelas_id: FK(kelas)     │
                                               │ - wali_murid_user_id: FK  │
                                               │ - nisn: string (unique)   │
                                               │ - nama_siswa: string      │
                                               │ - nomor_hp_wali_murid: str│
                                               │ - alasan_keluar: string   │
                                               │ - deleted_at: timestamp   │
                                               ├───────────────────────────┤
                                               │ + kelas(): BelongsTo      │
                                               │ + waliMurid(): BelongsTo  │
                                               │ + riwayatPelanggaran()    │
                                               │ + tindakLanjut(): HasMany │
                                               │ + getTotalPoinAttribute() │
                                               │ + scopeInKelas()          │
                                               │ + scopeInJurusan()        │
                                               │ + scopeSearch()           │
                                               │ + scopeWithViolations()   │
                                               └───────────────────────────┘
```

---

### ⚠️ **PELANGGARAN SYSTEM**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           PELANGGARAN & RULES                                        │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────┐
│    KategoriPelanggaran      │
├─────────────────────────────┤
│ - id: bigint (PK)           │
│ - nama_kategori: string     │
│ - tingkat_keseriusan: enum  │    (ringan, sedang, berat)
│ - created_at, updated_at    │
├─────────────────────────────┤
│ + jenisPelanggaran(): HM    │
│ + isSystemRequired(): bool  │
│ + getEnum(): ?Enum          │
│ + getColorAttribute(): str  │
│ + getIconAttribute(): str   │
└──────────────┬──────────────┘
               │ 1
               │
               │ *
┌──────────────▼──────────────┐          ┌───────────────────────────────────┐
│     JenisPelanggaran        │          │   PelanggaranFrequencyRule        │
├─────────────────────────────┤    1   * ├───────────────────────────────────┤
│ - id: bigint (PK)           │─────────▶│ - id: bigint (PK)                 │
│ - kategori_id: FK           │          │ - jenis_pelanggaran_id: FK        │
│ - nama_pelanggaran: string  │          │ - frequency_min: int              │
│ - poin: int                 │          │ - frequency_max: int (nullable)   │
│ - has_frequency_rules: bool │          │ - poin: int                       │
│ - is_active: boolean        │          │ - sanksi_description: text        │
│ - filter_category: string   │          │ - trigger_surat: boolean          │
│ - keywords: string          │          │ - pembina_roles: JSON             │
├─────────────────────────────┤          │ - display_order: int              │
│ + kategoriPelanggaran(): BT │          ├───────────────────────────────────┤
│ + riwayatPelanggaran(): HM  │          │ + jenisPelanggaran(): BelongsTo   │
│ + frequencyRules(): HasMany │          │ + matchesFrequency(int): bool     │
│ + usesFrequencyRules(): bool│          │ + getSuratType(): ?string         │
│ + getDisplayPoin(): string  │          └───────────────────────────────────┘
│ + getNumericPoin(): int     │
│ + isRecordable(): bool      │
│ + scopeSearchByKeyword()    │
│ + getKeywordsArray(): array │
└──────────────┬──────────────┘
               │ 1
               │
               │ *
┌──────────────▼──────────────┐
│    RiwayatPelanggaran       │
├─────────────────────────────┤
│ - id: bigint (PK)           │
│ - siswa_id: FK(siswa)       │
│ - jenis_pelanggaran_id: FK  │
│ - guru_pencatat_user_id: FK │
│ - tanggal_kejadian: date    │
│ - keterangan: text          │
│ - bukti_foto_path: string   │
│ - deleted_at: timestamp     │
├─────────────────────────────┤
│ + siswa(): BelongsTo        │
│ + jenisPelanggaran(): BT    │
│ + guruPencatat(): BelongsTo │
│ + scopeFromDate()           │
│ + scopeToDate()             │
│ + scopeBySiswa()            │
│ + scopeInKelas()            │
│ + scopeInJurusan()          │
└─────────────────────────────┘
```

---

### 📝 **TINDAK LANJUT & SURAT PANGGILAN**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           TINDAK LANJUT SYSTEM                                       │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────┐
│           TindakLanjut                │
├───────────────────────────────────────┤
│ - id: bigint (PK)                     │
│ - siswa_id: FK(siswa)                 │───────────────────────▶ Siswa
│ - pemicu: text                        │
│ - sanksi_deskripsi: text              │
│ - pembina_roles: JSON                 │    ['Wali Kelas', 'Kaprodi', ...]
│ - denda_deskripsi: text               │
│ - status: enum                        │    (Baru, Menunggu Persetujuan, 
│ - tanggal_tindak_lanjut: date         │     Disetujui, Ditangani, Selesai)
│ - penyetuju_user_id: FK(users)        │───────────────────────▶ User (Penyetuju)
│ - ditangani_oleh_user_id: FK          │───────────────────────▶ User
│ - ditangani_at: timestamp             │
│ - diselesaikan_oleh_user_id: FK       │───────────────────────▶ User
│ - diselesaikan_at: timestamp          │
│ - deleted_at: timestamp               │
├───────────────────────────────────────┤
│ + siswa(): BelongsTo                  │
│ + penyetuju(): BelongsTo              │
│ + suratPanggilan(): HasOne            │
│ + ditanganiOleh(): BelongsTo          │
│ + diselesaikanOleh(): BelongsTo       │
│ + scopePendingApproval()              │
│ + scopeApproved()                     │
│ + scopeInProgress()                   │
│ + scopeCompleted()                    │
│ + scopeActive()                       │
│ + scopeForPembina(string $role)       │
│ + scopeBySiswa()                      │
│ + scopeInKelas()                      │
│ + scopeInJurusan()                    │
└───────────────────┬───────────────────┘
                    │ 1
                    │
                    │ 1
┌───────────────────▼───────────────────┐
│          SuratPanggilan               │
├───────────────────────────────────────┤
│ - id: bigint (PK)                     │
│ - tindak_lanjut_id: FK                │
│ - nomor_surat: string                 │
│ - lampiran: string (nullable)         │
│ - hal: string                         │
│ - tipe_surat: string                  │    ('Surat 1', 'Surat 2', 'Surat 3', 'Surat 4')
│ - pembina_data: JSON                  │
│ - pembina_roles: JSON                 │
│ - tanggal_surat: date                 │
│ - tanggal_pertemuan: date             │
│ - waktu_pertemuan: string             │
│ - tempat_pertemuan: string            │
│ - keperluan: text                     │
│ - file_path_pdf: string               │
│ - deleted_at: timestamp               │
├───────────────────────────────────────┤
│ + tindakLanjut(): BelongsTo           │
│ + printLogs(): HasMany                │
│ + getLastPrintedAttribute()           │
│ + getPrintCountAttribute(): int       │
└───────────────────┬───────────────────┘
                    │ 1
                    │
                    │ *
┌───────────────────▼───────────────────┐
│     SuratPanggilanPrintLog            │
├───────────────────────────────────────┤
│ - id: bigint (PK)                     │
│ - surat_panggilan_id: FK              │
│ - user_id: FK(users)                  │───────────────────────▶ User
│ - printed_at: timestamp               │
│ - ip_address: string                  │
│ - user_agent: string                  │
├───────────────────────────────────────┤
│ + suratPanggilan(): BelongsTo         │
│ + user(): BelongsTo                   │
└───────────────────────────────────────┘
```

---

### 🎓 **PEMBINAAN INTERNAL**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           PEMBINAAN INTERNAL                                         │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────┐
│      PembinaanInternalRule            │
├───────────────────────────────────────┤
│ - id: bigint (PK)                     │
│ - poin_min: int                       │
│ - poin_max: int (nullable)            │
│ - pembina_roles: JSON                 │    ['Wali Kelas', 'Kaprodi', ...]
│ - keterangan: text                    │
│ - display_order: int                  │
├───────────────────────────────────────┤
│ + matchesPoin(int): bool              │
│ + getRangeText(): string              │
└───────────────────┬───────────────────┘
                    │ 1
                    │
                    │ *
┌───────────────────▼───────────────────┐
│         PembinaanStatus               │
├───────────────────────────────────────┤
│ - id: bigint (PK)                     │
│ - siswa_id: FK                        │───────────────────────▶ Siswa
│ - pembinaan_rule_id: FK               │
│ - total_poin_saat_trigger: int        │
│ - range_text: string                  │
│ - keterangan_pembinaan: text          │
│ - pembina_roles: JSON                 │
│ - status: enum                        │    (PERLU_PEMBINAAN, SEDANG_DIBINA, SELESAI)
│ - dibina_oleh_user_id: FK             │───────────────────────▶ User
│ - dibina_at: timestamp                │
│ - diselesaikan_oleh_user_id: FK       │───────────────────────▶ User
│ - selesai_at: timestamp               │
│ - catatan_pembinaan: text             │
│ - hasil_pembinaan: text               │
├───────────────────────────────────────┤
│ + siswa(): BelongsTo                  │
│ + rule(): BelongsTo                   │
│ + dibinaOleh(): BelongsTo             │
│ + diselesaikanOleh(): BelongsTo       │
│ + scopeActive()                       │
│ + scopeCompleted()                    │
│ + scopeForSiswa(int)                  │
│ + scopeForRule(int)                   │
│ + isActive(): bool                    │
│ + isCompleted(): bool                 │
│ + isPembinaForRole(string): bool      │
│ + mulaiPembinaan(int): bool           │
│ + selesaikanPembinaan(int, str): bool │
│ + hasRecordForSiswaAndRule(): static  │
│ + createIfNotExists(): static         │
└───────────────────────────────────────┘
```

---

### ⚙️ **RULES ENGINE SETTINGS**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           RULES ENGINE                                               │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────┐          ┌───────────────────────────────────┐
│      RulesEngineSetting               │    1   * │    RulesEngineSettingHistory      │
├───────────────────────────────────────┤─────────▶├───────────────────────────────────┤
│ - id: bigint (PK)                     │          │ - id: bigint (PK)                 │
│ - key: string (unique)                │          │ - setting_id: FK                  │
│ - value: string                       │          │ - old_value: string               │
│ - label: string                       │          │ - new_value: string               │
│ - description: text                   │          │ - changed_by: FK(users)           │─▶ User
│ - category: string                    │          │ - created_at: timestamp           │
│ - data_type: string                   │          ├───────────────────────────────────┤
│ - validation_rules: string            │          │ + setting(): BelongsTo            │
│ - display_order: int                  │          │ + user(): BelongsTo               │
├───────────────────────────────────────┤          │ + scopeLatest()                   │
│ + histories(): HasMany                │          │ + scopeBySetting(int)             │
│ + scopeByCategory(str)                │          │ + scopeByUser(int)                │
│ + scopeOrdered()                      │          └───────────────────────────────────┘
│ + asInt(): int                        │
│ + asFloat(): float                    │
│ + asBool(): bool                      │
│ + getValue(str, default): static      │
│ + getIntValue(str, int): static       │
│ + setValue(str, val, int): static     │
└───────────────────────────────────────┘
```

---

## 🔗 RINGKASAN RELASI

| # | Model A | Relasi | Model B | Deskripsi |
|---|---------|--------|---------|-----------|
| 1 | Role | 1:M | User | Satu Role memiliki banyak User |
| 2 | User (Kaprodi) | 1:1 | Jurusan | Kaprodi mengelola satu Jurusan |
| 3 | User (Wali Kelas) | 1:1 | Kelas | Wali Kelas mengelola satu Kelas |
| 4 | User (Wali Murid) | 1:M | Siswa | Wali Murid bisa punya banyak anak |
| 5 | Jurusan | 1:M | Kelas | Satu Jurusan punya banyak Kelas |
| 6 | Kelas | 1:M | Siswa | Satu Kelas punya banyak Siswa |
| 7 | KategoriPelanggaran | 1:M | JenisPelanggaran | Satu kategori punya banyak jenis |
| 8 | JenisPelanggaran | 1:M | PelanggaranFrequencyRule | Satu jenis punya banyak rules |
| 9 | JenisPelanggaran | 1:M | RiwayatPelanggaran | Satu jenis tercatat di banyak riwayat |
| 10 | Siswa | 1:M | RiwayatPelanggaran | Satu siswa punya banyak riwayat |
| 11 | User (Guru) | 1:M | RiwayatPelanggaran | Guru mencatat banyak pelanggaran |
| 12 | Siswa | 1:M | TindakLanjut | Satu siswa punya banyak kasus |
| 13 | TindakLanjut | 1:1 | SuratPanggilan | Satu kasus punya satu surat |
| 14 | SuratPanggilan | 1:M | SuratPanggilanPrintLog | Surat bisa dicetak berkali-kali |
| 15 | PembinaanInternalRule | 1:M | PembinaanStatus | Satu rule punya banyak status |
| 16 | Siswa | 1:M | PembinaanStatus | Satu siswa punya banyak status pembinaan |
| 17 | RulesEngineSetting | 1:M | RulesEngineSettingHistory | Satu setting punya banyak history |

---

## 📊 HIERARKI ROLE

```
                    ┌─────────────────┐
                    │  Kepala Sekolah │  ← Approval final, melihat semua data
                    └────────┬────────┘
                             │
            ┌────────────────┼────────────────┐
            ▼                ▼                ▼
    ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
    │Waka Kesiswaan│  │ Waka Sarana  │  │   Kaprodi    │   ← Mengelola jurusan
    └──────┬───────┘  └──────────────┘  └──────┬───────┘
           │                                    │
           └────────────────┬───────────────────┘
                            ▼
                    ┌──────────────┐
                    │  Wali Kelas  │  ← Mengelola kelas & siswa di kelasnya
                    └──────┬───────┘
                           │
            ┌──────────────┼──────────────┐
            ▼              ▼              ▼
    ┌─────────────┐  ┌───────────┐  ┌──────────────┐
    │    Guru     │  │   Siswa   │  │  Wali Murid  │
    │  (Pencatat) │  │  (Data)   │  │ (Monitoring) │
    └─────────────┘  └───────────┘  └──────────────┘
```

---

## 🔄 ALUR SISTEM PELANGGARAN

```
┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐
│  1. PENCATATAN   │    │  2. RULES CHECK  │    │ 3. TINDAK LANJUT │    │ 4. SURAT         │
│                  │    │                  │    │                  │    │                  │
│  Guru mencatat   │───▶│  Cek frequency   │───▶│  Buat kasus      │───▶│  Generate surat  │
│  pelanggaran     │    │  rules           │    │  tindak lanjut   │    │  panggilan       │
│  siswa           │    │                  │    │                  │    │                  │
└──────────────────┘    └──────────────────┘    └──────────────────┘    └──────────────────┘
        │                       │                       │                       │
        ▼                       ▼                       ▼                       ▼
┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐
│ RiwayatPelanggaran│   │ Hitung poin dari │    │ Status:          │    │ Tipe Surat:      │
│ record created   │    │ matched rule     │    │ • Baru           │    │ • Surat 1        │
│                  │    │                  │    │ • Menunggu       │    │ • Surat 2        │
│                  │    │ Tentukan pembina │    │ • Disetujui      │    │ • Surat 3        │
│                  │    │ yang terlibat    │    │ • Ditangani      │    │ • Surat 4        │
│                  │    │                  │    │ • Selesai        │    │                  │
└──────────────────┘    └──────────────────┘    └──────────────────┘    └──────────────────┘
```

---

## 📁 DAFTAR MODEL

| # | Model | File | Tabel | Deskripsi |
|---|-------|------|-------|-----------|
| 1 | Role | `app/Models/Role.php` | `roles` | Role/jabatan pengguna |
| 2 | User | `app/Models/User.php` | `users` | Pengguna sistem |
| 3 | Jurusan | `app/Models/Jurusan.php` | `jurusan` | Jurusan/program studi |
| 4 | Kelas | `app/Models/Kelas.php` | `kelas` | Kelas siswa |
| 5 | Siswa | `app/Models/Siswa.php` | `siswa` | Data siswa |
| 6 | KategoriPelanggaran | `app/Models/KategoriPelanggaran.php` | `kategori_pelanggaran` | Kategori tingkat pelanggaran |
| 7 | JenisPelanggaran | `app/Models/JenisPelanggaran.php` | `jenis_pelanggaran` | Jenis pelanggaran spesifik |
| 8 | PelanggaranFrequencyRule | `app/Models/PelanggaranFrequencyRule.php` | `pelanggaran_frequency_rules` | Aturan frekuensi pelanggaran |
| 9 | RiwayatPelanggaran | `app/Models/RiwayatPelanggaran.php` | `riwayat_pelanggaran` | Riwayat pelanggaran siswa |
| 10 | TindakLanjut | `app/Models/TindakLanjut.php` | `tindak_lanjut` | Kasus tindak lanjut |
| 11 | SuratPanggilan | `app/Models/SuratPanggilan.php` | `surat_panggilan` | Surat panggilan orang tua |
| 12 | SuratPanggilanPrintLog | `app/Models/SuratPanggilanPrintLog.php` | `surat_panggilan_print_log` | Log cetak surat |
| 13 | PembinaanInternalRule | `app/Models/PembinaanInternalRule.php` | `pembinaan_internal_rules` | Aturan pembinaan internal |
| 14 | PembinaanStatus | `app/Models/PembinaanStatus.php` | `pembinaan_status` | Status pembinaan siswa |
| 15 | RulesEngineSetting | `app/Models/RulesEngineSetting.php` | `rules_engine_settings` | Pengaturan rules engine |
| 16 | RulesEngineSettingHistory | `app/Models/RulesEngineSettingHistory.php` | `rules_engine_settings_history` | History perubahan setting |

---

## 📌 CATATAN PENTING

### Tipe Data JSON di Database:
- `pembina_roles`: Array role yang bertanggung jawab membina
- `pembina_data`: Data lengkap pembina untuk template surat

### Status Enum:
- **StatusTindakLanjut**: `Baru`, `Menunggu Persetujuan`, `Disetujui`, `Ditangani`, `Selesai`
- **StatusPembinaan**: `PERLU_PEMBINAAN`, `SEDANG_DIBINA`, `SELESAI`
- **KategoriPelanggaran**: `ringan`, `sedang`, `berat`

### Soft Deletes:
- Siswa, RiwayatPelanggaran, TindakLanjut, SuratPanggilan menggunakan soft deletes

---

**Dokumen ini dibuat pada: 27 Desember 2024**
**Diverifikasi dari source code aktual sistem**
