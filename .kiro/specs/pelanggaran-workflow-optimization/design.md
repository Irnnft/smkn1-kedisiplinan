# Design Document - Sprint 1: Critical UX Improvements

## Overview
Sprint 1 fokus pada peningkatan user experience kritis dalam proses pencatatan pelanggaran dengan 3 fitur utama:
1. Preview & Konfirmasi Sebelum Submit
2. Feedback yang Informatif
3. Jam Kejadian Wajib

---

## Feature 1: Preview & Konfirmasi Sebelum Submit

### Technical Design

#### Flow Diagram
```
[Form Pencatatan] 
    ↓ (Submit)
[Validasi Input]
    ↓ (Valid)
[Preview Page] ← NEW
    ↓ (Konfirmasi)
[Process & Save]
    ↓
[Success Page dengan Detail]
```

#### Implementation Plan

**1. Route Changes**
```php
// routes/web.php
Route::post('/pelanggaran/preview', [PelanggaranController::class, 'preview'])
    ->name('pelanggaran.preview');
Route::post('/pelanggaran/confirm', [PelanggaranController::class, 'confirm'])
    ->name('pelanggaran.confirm');
```

**2. Controller Methods**
```php
// app/Http/Controllers/PelanggaranController.php

public function preview(Request $request)
{
    // Validasi input
    // Hitung dampak untuk setiap siswa
    // Cek apakah akan trigger surat
    // Return view preview dengan data
}

public function confirm(Request $request)
{
    // Validasi token/session
    // Proses pencatatan (existing store logic)
    // Return success dengan detail
}
```

**3. Service Layer**
```php
// app/Services/PelanggaranPreviewService.php (NEW)

class PelanggaranPreviewService
{
    public function calculateImpact(array $siswaIds, array $pelanggaranIds): array
    {
        // Untuk setiap siswa:
        // - Hitung poin yang akan ditambah
        // - Hitung total akumulasi setelah pencatatan
        // - Cek apakah akan trigger surat (simulasi rules engine)
        // - Return array dengan detail per siswa
    }
    
    public function willTriggerSurat(int $siswaId, int $newPoints, int $totalPoints): ?array
    {
        // Simulasi rules engine tanpa save
        // Return [tipeSurat, pemicu] atau null
    }
}
```

**4. View Structure**
```
resources/views/pelanggaran/
├── create.blade.php (existing - update form action)
├── preview.blade.php (NEW)
└── success.blade.php (NEW - replace redirect)
```

---

## Feature 2: Feedback yang Informatif

### Technical Design

#### Success Page Layout
```
┌─────────────────────────────────────────┐
│ ✓ Pencatatan Berhasil                   │
│ 5 siswa, 2 jenis pelanggaran = 10 record│
├─────────────────────────────────────────┤
│ Detail Per Siswa:                        │
│                                          │
│ ┌─────────────────────────────────────┐ │
│ │ 🔴 Arigumilang (X AKL 1)            │ │
│ │ Poin ditambah: +150                  │ │
│ │ Total akumulasi: 280 poin            │ │
│ │ ⚠️ TRIGGER: Surat 2 dibuat           │ │
│ │ [Lihat Profil] [Lihat Kasus]        │ │
│ └─────────────────────────────────────┘ │
│                                          │
│ ┌─────────────────────────────────────┐ │
│ │ Budi Santoso (X AKL 1)              │ │
│ │ Poin ditambah: +150                  │ │
│ │ Total akumulasi: 180 poin            │ │
│ │ Status: Normal                       │ │
│ │ [Lihat Profil]                       │ │
│ └─────────────────────────────────────┘ │
│                                          │
│ [Catat Lagi] [Ke Dashboard]             │
└─────────────────────────────────────────┘
```

#### Data Structure
```php
// Return dari controller ke view
[
    'summary' => [
        'total_siswa' => 5,
        'total_pelanggaran' => 2,
        'total_records' => 10,
    ],
    'details' => [
        [
            'siswa_id' => 601,
            'siswa_nama' => 'Arigumilang',
            'siswa_kelas' => 'X AKL 1',
            'poin_ditambah' => 150,
            'total_akumulasi' => 280,
            'trigger_surat' => true,
            'tipe_surat' => 'Surat 2',
            'kasus_id' => 123,
        ],
        // ... siswa lainnya
    ]
]
```

---

## Feature 3: Jam Kejadian Wajib

### Technical Design

#### Form Changes
```html
<!-- Before -->
<input type="time" name="jam_kejadian" class="form-control">

<!-- After -->
<input type="time" 
       name="jam_kejadian" 
       class="form-control" 
       value="{{ old('jam_kejadian', now()->format('H:i')) }}"
       required>
```

#### Validation Rules
```php
// app/Http/Controllers/PelanggaranController.php

$request->validate([
    'tanggal_kejadian' => 'required|date|before_or_equal:today',
    'jam_kejadian' => 'required|date_format:H:i',
    // ... other rules
]);

// Custom validation
$this->validateDateTime($request->tanggal_kejadian, $request->jam_kejadian);

private function validateDateTime($tanggal, $jam)
{
    $datetime = Carbon::parse("$tanggal $jam");
    
    if ($datetime->isFuture()) {
        throw ValidationException::withMessages([
            'jam_kejadian' => 'Waktu kejadian tidak boleh di masa depan.'
        ]);
    }
}
```

---

## Implementation Steps

### Step 1: Create Preview Service (30 min)
```bash
php artisan make:service PelanggaranPreviewService
```

### Step 2: Update Controller (45 min)
- Add preview() method
- Add confirm() method  
- Refactor store() logic to be reusable
- Add validation for datetime

### Step 3: Create Routes (5 min)
- Add preview route
- Add confirm route

### Step 4: Create Views (60 min)
- Create preview.blade.php
- Create success.blade.php
- Update create.blade.php form action

### Step 5: Update Form Validation (15 min)
- Make jam_kejadian required
- Add default value
- Add future datetime validation

### Step 6: Testing (30 min)
- Test preview calculation
- Test confirmation flow
- Test validation rules
- Test success feedback

**Total Estimated Time: ~3 hours**

---

## Database Changes
**None required** - All changes are in application layer

---

## Breaking Changes
**None** - Backward compatible, only adds new flow

---

## Testing Checklist

### Preview Functionality
- [ ] Preview menampilkan semua siswa yang dipilih
- [ ] Preview menampilkan total poin yang akan ditambah
- [ ] Preview menampilkan akumulasi setelah pencatatan
- [ ] Preview menampilkan warning jika trigger surat
- [ ] Preview bisa kembali ke form untuk edit
- [ ] Preview bisa konfirmasi untuk lanjut

### Confirmation Flow
- [ ] Konfirmasi menyimpan data dengan benar
- [ ] Konfirmasi menjalankan rules engine
- [ ] Konfirmasi tidak bisa diulang (prevent double submit)
- [ ] Konfirmasi redirect ke success page

### Success Feedback
- [ ] Success page menampilkan summary
- [ ] Success page menampilkan detail per siswa
- [ ] Success page highlight siswa yang trigger surat
- [ ] Success page ada link ke profil siswa
- [ ] Success page ada link ke kasus (jika ada)

### Jam Kejadian
- [ ] Jam default ke waktu sekarang
- [ ] Jam wajib diisi
- [ ] Jam tidak boleh di masa depan
- [ ] Jam boleh di masa lalu (hari yang sama)
- [ ] Error message jelas jika validasi gagal

---

## Rollback Plan
Jika ada masalah:
1. Revert routes (hapus preview & confirm)
2. Revert controller (kembalikan ke store langsung)
3. Revert form (kembalikan action ke store)
4. Jam kejadian tetap required (improvement tetap dipertahankan)

---

## Next Steps (Sprint 2)
Setelah Sprint 1 selesai:
- Implementasi notifikasi WhatsApp/SMS
- Implementasi bukti foto opsional
- Implementasi bulk operations
