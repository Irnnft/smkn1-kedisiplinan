# Design Document: Rules Engine Settings Management

## Overview

Fitur ini memungkinkan Operator Sekolah untuk mengkonfigurasi threshold dan frekuensi yang digunakan oleh Rules Engine tanpa perlu mengedit code. Sistem akan menyimpan konfigurasi di database dan Rules Engine akan membaca dari database dengan fallback ke nilai default jika terjadi error.

**Prinsip Design:**
- **Database-driven Configuration**: Semua threshold disimpan di database
- **Fallback Mechanism**: Jika database error, gunakan konstanta default
- **Non-Retroactive**: Perubahan hanya berlaku untuk evaluasi baru
- **Audit Trail**: Semua perubahan dicatat untuk transparansi
- **User-Friendly UI**: Form terorganisir dengan validasi real-time

---

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Operator Interface                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Settings Form (Blade View)                          │   │
│  │  - Threshold Poin Surat                              │   │
│  │  - Threshold Akumulasi                               │   │
│  │  - Frekuensi Spesifik                                │   │
│  │  - Preview & Validation                              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│              RulesEngineSettingsController                   │
│  - index(): Show settings form                               │
│  - update(): Validate & save settings                        │
│  - preview(): Show preview before save                       │
│  - reset(): Reset to default values                          │
│  - history(): Show change history                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│              RulesEngineSettingsService                      │
│  - getSettings(): Get all settings (with cache)             │
│  - getSetting($key): Get single setting                     │
│  - updateSettings($data): Update multiple settings          │
│  - validateSettings($data): Validate consistency            │
│  - resetToDefaults(): Reset all to default                  │
│  - clearCache(): Clear settings cache                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer                            │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  rules_engine_settings                               │   │
│  │  - Stores current threshold values                   │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  rules_engine_settings_history                       │   │
│  │  - Stores change history for audit                   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↑
┌─────────────────────────────────────────────────────────────┐
│              PelanggaranRulesEngine (Updated)                │
│  - Reads settings from RulesEngineSettingsService           │
│  - Falls back to constants if database error                │
│  - Uses cached settings for performance                     │
└─────────────────────────────────────────────────────────────┘
```

---

## Components and Interfaces

### 1. Model: RulesEngineSetting

**File:** `app/Models/RulesEngineSetting.php`

**Responsibilities:**
- Represent settings record in database
- Provide accessor/mutator for value
- Define relationships

**Interface:**
```php
class RulesEngineSetting extends Model
{
    // Fillable attributes
    protected $fillable = ['key', 'value', 'description', 'category', 'updated_by'];
    
    // Relationships
    public function updatedBy(): BelongsTo; // User who last updated
    
    // Scopes
    public function scopeByCategory($query, string $category);
    public function scopeByKey($query, string $key);
    
    // Accessors
    public function getValueAttribute($value): int;
    public function setValueAttribute($value): void;
}
```

---

### 2. Model: RulesEngineSettingHistory

**File:** `app/Models/RulesEngineSettingHistory.php`

**Responsibilities:**
- Store audit trail of setting changes
- Track who changed what and when

**Interface:**
```php
class RulesEngineSettingHistory extends Model
{
    // Fillable attributes
    protected $fillable = ['setting_key', 'old_value', 'new_value', 'changed_by', 'notes'];
    
    // Relationships
    public function changedBy(): BelongsTo; // User who made the change
    
    // Scopes
    public function scopeBySettingKey($query, string $key);
    public function scopeByDateRange($query, $startDate, $endDate);
}
```

---

### 3. Service: RulesEngineSettingsService

**File:** `app/Services/RulesEngineSettingsService.php`

**Responsibilities:**
- Centralized access to settings
- Caching for performance
- Validation logic
- History tracking

**Interface:**
```php
class RulesEngineSettingsService
{
    // Get all settings as associative array
    public function getSettings(): array;
    
    // Get single setting by key with fallback to default
    public function getSetting(string $key, int $default = null): int;
    
    // Update multiple settings with validation
    public function updateSettings(array $data, int $userId): bool;
    
    // Validate settings consistency
    public function validateSettings(array $data): array; // Returns errors array
    
    // Reset all settings to default values
    public function resetToDefaults(int $userId): bool;
    
    // Clear cache
    public function clearCache(): void;
    
    // Get change history
    public function getHistory(array $filters = []): Collection;
    
    // Get default values
    public function getDefaults(): array;
}
```

---

### 4. Controller: RulesEngineSettingsController

**File:** `app/Http/Controllers/RulesEngineSettingsController.php`

**Responsibilities:**
- Handle HTTP requests for settings management
- Validate input
- Return views with data

**Interface:**
```php
class RulesEngineSettingsController extends Controller
{
    // Show settings form
    public function index(): View;
    
    // Update settings
    public function update(Request $request): RedirectResponse;
    
    // Show preview before save
    public function preview(Request $request): JsonResponse;
    
    // Reset to defaults
    public function reset(): RedirectResponse;
    
    // Show change history
    public function history(Request $request): View;
}
```

---

### 5. Updated: PelanggaranRulesEngine

**File:** `app/Services/PelanggaranRulesEngine.php`

**Changes:**
- Inject `RulesEngineSettingsService` via constructor
- Replace all `self::CONSTANT` with `$this->getSetting('key')`
- Keep constants as fallback defaults

**New Methods:**
```php
private function getSetting(string $key): int
{
    return $this->settingsService->getSetting($key, $this->getDefaultValue($key));
}

private function getDefaultValue(string $key): int
{
    // Map keys to constants for fallback
    return match($key) {
        'surat_2_min_poin' => self::THRESHOLD_SURAT_2_MIN,
        'surat_2_max_poin' => self::THRESHOLD_SURAT_2_MAX,
        'surat_3_min_poin' => self::THRESHOLD_SURAT_3_MIN,
        'akumulasi_sedang_min' => self::THRESHOLD_AKUMULASI_SEDANG_MIN,
        'akumulasi_sedang_max' => self::THRESHOLD_AKUMULASI_SEDANG_MAX,
        'akumulasi_kritis' => self::THRESHOLD_AKUMULASI_KRITIS,
        'frekuensi_atribut' => self::FREKUENSI_ATRIBUT,
        'frekuensi_alfa' => self::FREKUENSI_ALFA,
        default => 0,
    };
}
```

---

## Data Models

### Database Schema

#### Table: `rules_engine_settings`

```sql
CREATE TABLE rules_engine_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Setting key (e.g., surat_2_min_poin)',
    `value` INT NOT NULL COMMENT 'Threshold value',
    description TEXT NULL COMMENT 'Human-readable description',
    category VARCHAR(50) NOT NULL COMMENT 'Category: poin_surat, akumulasi, frekuensi',
    updated_by BIGINT UNSIGNED NULL COMMENT 'User ID who last updated',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Initial Data (Seeder):**
```php
[
    // Threshold Poin Surat
    ['key' => 'surat_2_min_poin', 'value' => 100, 'category' => 'poin_surat', 
     'description' => 'Poin minimum untuk trigger Surat 2'],
    ['key' => 'surat_2_max_poin', 'value' => 500, 'category' => 'poin_surat',
     'description' => 'Poin maksimum untuk Surat 2 (di atas ini = Surat 3)'],
    ['key' => 'surat_3_min_poin', 'value' => 501, 'category' => 'poin_surat',
     'description' => 'Poin minimum untuk trigger Surat 3'],
    
    // Threshold Akumulasi
    ['key' => 'akumulasi_sedang_min', 'value' => 55, 'category' => 'akumulasi',
     'description' => 'Akumulasi poin minimum untuk eskalasi ke Surat 2'],
    ['key' => 'akumulasi_sedang_max', 'value' => 300, 'category' => 'akumulasi',
     'description' => 'Akumulasi poin maksimum untuk Surat 2'],
    ['key' => 'akumulasi_kritis', 'value' => 301, 'category' => 'akumulasi',
     'description' => 'Akumulasi poin untuk trigger Surat 3 (kritis)'],
    
    // Frekuensi Spesifik
    ['key' => 'frekuensi_atribut', 'value' => 10, 'category' => 'frekuensi',
     'description' => 'Jumlah pelanggaran atribut untuk trigger Surat 1'],
    ['key' => 'frekuensi_alfa', 'value' => 4, 'category' => 'frekuensi',
     'description' => 'Jumlah pelanggaran alfa untuk trigger Surat 1'],
]
```

#### Table: `rules_engine_settings_history`

```sql
CREATE TABLE rules_engine_settings_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL COMMENT 'Key of setting that was changed',
    old_value INT NOT NULL COMMENT 'Previous value',
    new_value INT NOT NULL COMMENT 'New value',
    changed_by BIGINT UNSIGNED NOT NULL COMMENT 'User ID who made the change',
    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When change occurred',
    notes TEXT NULL COMMENT 'Optional notes about the change',
    
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_setting_key (setting_key),
    INDEX idx_changed_at (changed_at),
    INDEX idx_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Error Handling

### Validation Errors

**Scenario 1: Threshold Tidak Konsisten**
```php
// Example: Surat 3 min <= Surat 2 max
if ($surat3Min <= $surat2Max) {
    return [
        'surat_3_min_poin' => "Threshold Surat 3 minimum ({$surat3Min}) harus lebih besar dari Surat 2 maksimum ({$surat2Max})"
    ];
}
```

**Scenario 2: Range Overlap**
```php
// Example: Surat 2 min >= Surat 2 max
if ($surat2Min >= $surat2Max) {
    return [
        'surat_2_max_poin' => "Threshold Surat 2 maksimum ({$surat2Max}) harus lebih besar dari minimum ({$surat2Min})"
    ];
}
```

**Scenario 3: Invalid Input**
```php
// Example: Negative or zero value
if ($value <= 0) {
    return [
        $key => "Nilai threshold harus berupa angka positif (lebih dari 0)"
    ];
}
```

### Database Errors

**Fallback Mechanism:**
```php
try {
    $value = RulesEngineSetting::where('key', $key)->value('value');
    return $value ?? $this->getDefaultValue($key);
} catch (\Exception $e) {
    \Log::error("Failed to read setting {$key}: " . $e->getMessage());
    return $this->getDefaultValue($key);
}
```

---

## Testing Strategy

### Unit Tests

**Test File:** `tests/Unit/Services/RulesEngineSettingsServiceTest.php`

**Test Cases:**
1. `test_get_settings_returns_all_settings()`
2. `test_get_setting_returns_value_from_database()`
3. `test_get_setting_returns_default_when_not_found()`
4. `test_update_settings_validates_consistency()`
5. `test_update_settings_creates_history_record()`
6. `test_reset_to_defaults_restores_original_values()`
7. `test_validate_settings_detects_inconsistencies()`
8. `test_cache_is_cleared_after_update()`

**Test File:** `tests/Unit/Services/PelanggaranRulesEngineTest.php`

**Test Cases:**
1. `test_rules_engine_reads_from_database()`
2. `test_rules_engine_falls_back_to_constants_on_error()`
3. `test_rules_engine_uses_cached_settings()`
4. `test_threshold_changes_affect_new_evaluations()`

### Feature Tests

**Test File:** `tests/Feature/RulesEngineSettingsControllerTest.php`

**Test Cases:**
1. `test_operator_can_access_settings_page()`
2. `test_non_operator_cannot_access_settings_page()`
3. `test_operator_can_update_settings()`
4. `test_validation_prevents_inconsistent_settings()`
5. `test_preview_shows_comparison()`
6. `test_reset_restores_defaults()`
7. `test_history_shows_changes()`

---

## UI/UX Design

### Page Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Pengaturan Rules Engine                          [?] Help   │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  📊 Threshold Poin untuk Surat                      │    │
│  │                                                      │    │
│  │  Surat 2 (Pelanggaran Berat)                        │    │
│  │  ┌─────────────┐  s/d  ┌─────────────┐             │    │
│  │  │ Min: [100 ] │  ───  │ Max: [500 ] │  poin       │    │
│  │  └─────────────┘       └─────────────┘             │    │
│  │  ℹ️ Siswa dengan poin 100-500 akan mendapat Surat 2│    │
│  │                                                      │    │
│  │  Surat 3 (Sangat Berat)                             │    │
│  │  ┌─────────────┐                                    │    │
│  │  │ Min: [501 ] │  poin                              │    │
│  │  └───────────���─┘                                    │    │
│  │  ℹ️ Siswa dengan poin >501 akan mendapat Surat 3   │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  📈 Threshold Akumulasi Poin                        │    │
│  │                                                      │    │
│  │  Akumulasi Sedang (Eskalasi ke Surat 2)             │    │
│  │  ┌───────────��─┐  s/d  ┌─────────────┐             │    │
│  │  │ Min: [ 55 ] │  ───  │ Max: [300 ] │  poin       │    │
│  │  └─────────────┘       └─────────────┘             │    │
│  │                                                      │    │
│  │  Akumulasi Kritis (Trigger Surat 3)                 │    │
│  │  ┌─────────────┐                                    │    │
│  │  │ Min: [301 ] │  poin                              │    │
│  │  └─────────────┘                                    │    │
│  │  ℹ️ Total poin dari semua pelanggaran siswa        │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  🔢 Frekuensi Pelanggaran Spesifik                  │    │
│  │                                                      │    │
│  │  Pelanggaran Atribut                                │    │
│  │  ┌─────────────┐                                    │    │
│  │  │ [ 10 ] kali │  → Trigger Surat 1                │    │
│  │  └─────────────┘                                    │    │
│  │                                                      │    │
│  │  Pelanggaran Alfa (Tidak Masuk)                     │    │
│  │  ┌─────────────┐                                    │    │
│  │  │ [  4 ] kali │  → Trigger Surat 1                │    │
│  │  └─────────────┘                                    │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ⚠️ Perubahan hanya berlaku untuk pelanggaran baru          │
│     (tidak retroaktif untuk kasus yang sudah ada)            │
│                                                               │
│  [Preview Perubahan]  [Simpan]  [Reset ke Default]          │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  📜 History Perubahan (Tab)                         │    │
│  │  ┌───────────────────────────────────────────────┐  │    │
│  │  │ 05 Des 2025 14:30 - operator1                 │  │    │
│  │  │ • surat_2_min_poin: 100 → 150                 │  │    │
│  │  │ • frekuensi_atribut: 10 → 8                   │  │    │
│  │  └───────────────────────────────────────────────┘  │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

### Preview Modal

```
┌─────────────────────────────────────────────────────────────┐
│  Preview Perubahan                                    [X]    │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Anda akan mengubah threshold berikut:                       │
│                                                               │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Parameter              │ Nilai Lama │ Nilai Baru    │    │
│  ├────────────────────────┼────────────┼───────────────┤    │
│  │ Surat 2 Min Poin       │    100     │    150  ⚠️    │    │
│  │ Frekuensi Atribut      │     10     │      8        │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ⚠️ Perubahan signifikan terdeteksi:                         │
│  • Surat 2 Min Poin naik 50% (100 → 150)                    │
│                                                               │
│  Contoh Dampak:                                               │
│  • Siswa dengan poin 120 sebelumnya: Surat 2                │
│  • Siswa dengan poin 120 setelah perubahan: Tidak ada surat │
│                                                               │
│  [Batal]  [Konfirmasi & Simpan]                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Implementation Plan

### Phase 1: Database & Models (2-3 jam)
1. Create migration for `rules_engine_settings` table
2. Create migration for `rules_engine_settings_history` table
3. Create seeder with default values
4. Create `RulesEngineSetting` model
5. Create `RulesEngineSettingHistory` model
6. Run migrations and seed data

### Phase 2: Service Layer (3-4 jam)
1. Create `RulesEngineSettingsService`
2. Implement `getSettings()` with caching
3. Implement `getSetting()` with fallback
4. Implement `updateSettings()` with validation
5. Implement `validateSettings()` logic
6. Implement `resetToDefaults()`
7. Implement history tracking
8. Write unit tests for service

### Phase 3: Update Rules Engine (2-3 jam)
1. Inject `RulesEngineSettingsService` into `PelanggaranRulesEngine`
2. Replace all constants with `getSetting()` calls
3. Implement `getDefaultValue()` method
4. Test Rules Engine with database settings
5. Test fallback mechanism
6. Write unit tests for updated Rules Engine

### Phase 4: Controller & Routes (2 jam)
1. Create `RulesEngineSettingsController`
2. Implement `index()` method
3. Implement `update()` method with validation
4. Implement `preview()` method
5. Implement `reset()` method
6. Implement `history()` method
7. Add routes to `routes/web.php`
8. Add middleware for Operator-only access

### Phase 5: Views & UI (4-5 jam)
1. Create `resources/views/rules-engine-settings/index.blade.php`
2. Implement form with organized sections
3. Add real-time validation with JavaScript
4. Create preview modal
5. Create history tab
6. Add help panel with documentation
7. Add tooltips for each parameter
8. Style with AdminLTE theme

### Phase 6: Testing & Polish (2-3 jam)
1. Write feature tests for controller
2. Test all validation scenarios
3. Test preview functionality
4. Test reset functionality
5. Test history tracking
6. Test UI responsiveness
7. Fix bugs and polish UX

**Total Estimated Time: 15-20 jam**

---

## Security Considerations

### Access Control
- ✅ Only Operator Sekolah can modify settings
- ✅ Kepala Sekolah can view settings (read-only)
- ✅ Other roles cannot access settings page

### Input Validation
- ✅ Server-side validation for all inputs
- ✅ Type checking (must be positive integers)
- ✅ Consistency validation (no overlaps, correct hierarchy)
- ✅ SQL injection prevention (Eloquent ORM)

### Audit Trail
- ✅ All changes logged with user ID and timestamp
- ✅ Old and new values recorded
- ✅ Cannot delete or modify history records

### Error Handling
- ✅ Graceful degradation (fallback to defaults)
- ✅ Error logging for debugging
- ✅ User-friendly error messages

---

## Performance Optimization

### Caching Strategy
```php
// Cache settings for 5 minutes
Cache::remember('rules_engine_settings', 300, function () {
    return RulesEngineSetting::all()->pluck('value', 'key')->toArray();
});

// Clear cache after update
Cache::forget('rules_engine_settings');
```

### Database Indexing
- ✅ Index on `key` column for fast lookup
- ✅ Index on `category` for filtering
- ✅ Index on `changed_at` for history queries

### Query Optimization
- ✅ Eager load relationships when needed
- ✅ Use `pluck()` for key-value pairs
- ✅ Limit history queries with pagination

---

## Rollback Plan

Jika terjadi masalah setelah deployment:

1. **Rollback Database:**
   ```bash
   php artisan migrate:rollback --step=2
   ```

2. **Revert Code Changes:**
   - Restore `PelanggaranRulesEngine.php` to use constants
   - Remove `RulesEngineSettingsService`
   - Remove controller and routes

3. **Clear Cache:**
   ```bash
   php artisan cache:clear
   ```

4. **Verify System:**
   - Test pencatatan pelanggaran
   - Verify Rules Engine still works with constants

---

## Future Enhancements (Out of Scope)

- Email notification saat settings diubah
- Approval workflow untuk perubahan kritis
- Export/import settings dalam JSON
- Rollback ke versi settings sebelumnya
- A/B testing untuk threshold berbeda
- Machine learning untuk rekomendasi threshold optimal

---

## Conclusion

Design ini memberikan solusi yang robust, user-friendly, dan maintainable untuk mengelola threshold Rules Engine. Dengan fallback mechanism dan audit trail yang lengkap, sistem tetap reliable meskipun terjadi error. UI yang terorganisir dan validasi real-time memastikan Operator tidak membuat konfigurasi yang salah.
