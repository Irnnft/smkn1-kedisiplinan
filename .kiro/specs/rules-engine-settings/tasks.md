# Implementation Plan: Rules Engine Settings Management

## Task List

- [x] 1. Setup Database & Models ✅ DONE

- [x] 1.1 Create migration for rules_engine_settings table ✅
  - Create migration file dengan schema lengkap (id, key, value, label, description, category, data_type, validation_rules, display_order, timestamps)
  - Add indexes untuk key, category, display_order
  - _Requirements: Requirement 1, 10_

- [x] 1.2 Create migration for rules_engine_settings_history table ✅
  - Create migration file dengan schema lengkap (id, setting_id, old_value, new_value, changed_by, timestamps)
  - Add foreign key ke users table dan settings table
  - Add indexes untuk setting_id, changed_by
  - _Requirements: Requirement 7_

- [x] 1.3 Create seeder for default settings ✅
  - Create RulesEngineSettingsSeeder dengan 8 default values
  - Seed data: surat_2_min_poin (100), surat_2_max_poin (500), surat_3_min_poin (501)
  - Seed data: akumulasi_sedang_min (55), akumulasi_sedang_max (300), akumulasi_kritis (301)
  - Seed data: frekuensi_atribut (10), frekuensi_alfa (4)
  - _Requirements: Requirement 1, 8_

- [x] 1.4 Create RulesEngineSetting model ✅
  - Define fillable attributes
  - Create relationship histories() ke RulesEngineSettingHistory model
  - Create scopes: byCategory(), ordered()
  - Add helper methods: asInt(), asFloat(), asBool()
  - Add static helpers: getValue(), getIntValue(), setValue()
  - _Requirements: Requirement 1_

- [x] 1.5 Create RulesEngineSettingHistory model ✅
  - Define fillable attributes
  - Create relationship setting() ke RulesEngineSetting model
  - Create relationship user() ke User model
  - Create scopes: latest(), bySetting(), byUser()
  - _Requirements: Requirement 7_

- [x] 1.6 Run migrations and seed database ✅
  - Execute: php artisan migrate
  - Execute: php artisan db:seed --class=RulesEngineSettingsSeeder
  - Verify data di database (8 settings loaded)
  - _Requirements: Requirement 1_

---

- [x] 2. Create Service Layer ✅ DONE
- [x] 2.1 Create RulesEngineSettingsService ✅
  - Create service file di app/Services/RulesEngineSettingsService.php
  - Inject Cache facade untuk caching
  - Define cache key constant: 'rules_engine_settings'
  - Define cache duration: 3600 seconds (1 hour)
  - _Requirements: Requirement 10_

- [x] 2.2 Implement getAllGrouped() & getAll() methods ✅
  - Read all settings from database
  - getAllGrouped() returns grouped by category
  - getAll() returns flat array
  - Implement caching dengan Cache::remember()
  - Handle database errors dengan try-catch
  - _Requirements: Requirement 10_

- [x] 2.3 Implement get(), getInt(), getFloat(), getBool() methods ✅
  - Accept $key and optional $default parameter
  - Read from cache first
  - Return specific setting value with type casting
  - Fallback to $default if not found
  - _Requirements: Requirement 10_

- [x] 2.4 Implement validateConsistency() method ✅
  - Validate surat_2_min_poin < surat_2_max_poin < surat_3_min_poin
  - Validate akumulasi_sedang_min < akumulasi_sedang_max < akumulasi_kritis
  - Return array of errors (empty if valid)
  - _Requirements: Requirement 2, 3, 5_

- [x] 2.5 Implement update() & bulkUpdate() methods ✅
  - Accept key/value or array of settings dan user ID
  - Validate value dengan validation_rules dari database
  - Loop through settings dan update database
  - Create history record untuk setiap perubahan
  - Clear cache setelah update
  - Return true jika berhasil
  - _Requirements: Requirement 2, 3, 4, 7_

- [x] 2.6 Implement reset() & resetAll() methods ✅
  - Get default values dari hardcoded array
  - Update settings ke nilai default
  - Create history record
  - Clear cache
  - _Requirements: Requirement 8_

- [x] 2.7 Implement getHistory() & clearCache() methods ✅
  - getHistory() returns last N changes for a setting
  - clearCache() clears specific or all cache
  - _Requirements: Requirement 7_
  - Map keys ke konstanta di PelanggaranRulesEngine
  - _Requirements: Requirement 8_

- [ ] 2.8 Implement clearCache() method
  - Clear cache dengan Cache::forget()
  - _Requirements: Requirement 10_

- [ ] 2.9 Implement getHistory() method
  - Accept optional filters (date range, setting key)
  - Query RulesEngineSettingHistory dengan filters
  - Eager load changedBy relationship
  - Order by changed_at DESC
  - Return collection
  - _Requirements: Requirement 7_

---

- [x] 3. Update PelanggaranRulesEngine ✅ DONE
- [x] 3.1 Inject RulesEngineSettingsService ✅
  - Add protected property $settingsService
  - Update constructor untuk inject service
  - _Requirements: Requirement 10_

- [x] 3.2 Create getThreshold() private method ✅
  - Accept $key and $fallback parameter
  - Call $this->settingsService->getInt() dengan try-catch
  - Return fallback value jika error
  - _Requirements: Requirement 10_

- [x] 3.3 Update tentukanBerdasarkanPoin() method ✅
  - Replace constants dengan getThreshold() calls
  - surat_2_min_poin, surat_2_max_poin, surat_3_min_poin
  - _Requirements: Requirement 10_

- [x] 3.4 Update tentukanTipeSuratDanStatus() method ✅
  - Replace constants dengan getThreshold() calls
  - akumulasi_sedang_min, akumulasi_sedang_max, akumulasi_kritis
  - _Requirements: Requirement 10_

- [x] 3.5 Update cekFrekuensiSpesifik() method ✅
  - Replace constants dengan getThreshold() calls
  - frekuensi_atribut, frekuensi_alfa
  - _Requirements: Requirement 10_

- [x] 3.6 Keep constants for backward compatibility ✅
  - Konstanta tetap ada sebagai FALLBACK VALUES
  - Add comment bahwa konstanta digunakan sebagai fallback
  - _Requirements: Requirement 10_

---

- [x] 4. Create Controller & Routes ✅ DONE
- [x] 4.1 Create RulesEngineSettingsController ✅
  - Create controller file di app/Http/Controllers/
  - Inject RulesEngineSettingsService via constructor
  - _Requirements: Requirement 1_

- [x] 4.2 Implement index() method ✅
  - Get settings grouped by category dari service
  - Pass data ke view
  - Return view 'rules-engine-settings.index'
  - _Requirements: Requirement 1_

- [x] 4.3 Implement update() method ✅
  - Validate consistency dengan service->validateConsistency()
  - Jika ada error, return back dengan errors
  - Call service->bulkUpdate() dengan Auth::id()
  - Redirect dengan success message
  - _Requirements: Requirement 2, 3, 4, 5_

- [x] 4.4 Implement preview() method ✅
  - Accept request dengan new values
  - Get current settings dari service
  - Compare old vs new values
  - Validate consistency
  - Return JSON response dengan comparison data dan errors
  - _Requirements: Requirement 6_

- [x] 4.5 Implement reset() & resetAll() methods ✅
  - reset() untuk single setting
  - resetAll() untuk semua settings
  - Call service methods dengan Auth::id()
  - Redirect dengan success message
  - _Requirements: Requirement 8_

- [x] 4.6 Implement history() method ✅
  - Get history untuk specific key
  - Call service->getHistory() dengan limit 20
  - Return JSON response
  - _Requirements: Requirement 7_

- [x] 4.7 Add routes to web.php ✅
  - Add route group dengan middleware 'role:Operator Sekolah'
  - Routes: index (GET), update (POST), preview (POST), reset (POST), resetAll (POST), history (GET)
  - _Requirements: Requirement 1_
  - Add route GET /rules-engine-settings => index
  - Add route PUT /rules-engine-settings => update
  - Add route POST /rules-engine-settings/preview => preview
  - Add route POST /rules-engine-settings/reset => reset
  - Add route GET /rules-engine-settings/history => history
  - _Requirements: Requirement 1_

---

- [x] 5. Create Views & UI ✅ DONE
- [x] 5.1 Create main settings view ✅
  - Create file resources/views/rules-engine-settings/index.blade.php
  - Extend layouts.app
  - Add page title "⚙️ Pengaturan Rules Engine"
  - Add subtitle "Kelola threshold poin dan frekuensi pelanggaran"
  - _Requirements: Requirement 1_

- [x] 5.2 Create form section: Threshold Poin Surat ✅
  - Create card dengan header bg-primary "Threshold Poin Surat"
  - Add input untuk surat_2_min_poin, surat_2_max_poin, surat_3_min_poin
  - Add label, description, dan unit (poin)
  - Add history button untuk setiap field
  - Add validation error display
  - _Requirements: Requirement 2, 9_

- [x] 5.3 Create form section: Threshold Akumulasi ✅
  - Create card dengan header bg-warning "Threshold Akumulasi Poin"
  - Add input untuk akumulasi_sedang_min, akumulasi_sedang_max, akumulasi_kritis
  - Add label, description, dan unit (poin)
  - Add history button untuk setiap field
  - _Requirements: Requirement 3, 9_

- [x] 5.4 Create form section: Frekuensi Spesifik ✅
  - Create card dengan header bg-danger "Threshold Frekuensi Pelanggaran"
  - Add input untuk frekuensi_atribut, frekuensi_alfa
  - Add label, description, dan unit (kali)
  - Add history button untuk setiap field
  - _Requirements: Requirement 4, 9_

- [x] 5.5 Add action buttons ✅
  - Add button "Preview Perubahan" (info, trigger preview modal)
  - Add button "Simpan Perubahan" (primary, submit form)
  - Add button "Reset Semua" (warning, confirm dialog)
  - Add button "Bantuan" (outline-secondary, trigger help modal)
  - Add button "Batal" (secondary, back to dashboard)
  - _Requirements: Requirement 6, 8_

- [x] 5.6 Create modals ✅
  - History Modal: Display change history dengan table (waktu, old value, new value, user)
  - Preview Modal: Display comparison table dengan status badge (berubah/tidak berubah)
  - Help Modal: Panduan lengkap dengan penjelasan setiap parameter dan warning
  - _Requirements: Requirement 6, 7, 9_

- [x] 5.7 Add JavaScript functionality ✅
  - showHistory(): Fetch dan display history via AJAX
  - previewChanges(): Fetch dan display preview via AJAX
  - confirmResetAll(): Confirm dialog untuk reset semua
  - submitForm(): Submit form setelah preview
  - _Requirements: Requirement 6, 7, 8_

- [x] 5.8 Add sidebar link ✅
  - Add link "Rules Engine" di sidebar Operator
  - Icon: fas fa-cogs text-warning
  - Active state detection
  - _Requirements: Requirement 1_
  - _Requirements: Requirement 9_

- [ ] 5.8 Create preview modal
  - Create modal dengan title "Preview Perubahan"
  - Add table comparison (Parameter | Nilai Lama | Nilai Baru)
  - Highlight changed values dengan warna
  - Add warning untuk perubahan signifikan (>50%)
  - Add contoh dampak perubahan
  - Add buttons: "Batal" dan "Konfirmasi & Simpan"
  - _Requirements: Requirement 6_

- [ ] 5.9 Create history tab/section
  - Add tab "History Perubahan" di bawah form
  - Create timeline view untuk history
  - Display: timestamp, username, changes (old → new)
  - Add filter: date range, setting key
  - Add pagination
  - _Requirements: Requirement 7_

- [ ] 5.10 Add real-time validation with JavaScript
  - Add event listener untuk input change
  - Validate: value > 0
  - Validate: surat_2_max > surat_2_min
  - Validate: surat_3_min > surat_2_max
  - Validate: akumulasi_sedang_max > akumulasi_sedang_min
  - Validate: akumulasi_kritis > akumulasi_sedang_max
  - Display inline error messages
  - Enable/disable "Simpan" button based on validation
  - _Requirements: Requirement 5_

- [ ] 5.11 Add preview functionality with AJAX
  - Add click handler untuk button "Preview Perubahan"
  - Collect form data
  - Send AJAX POST ke /rules-engine-settings/preview
  - Populate modal dengan response data
  - Show modal
  - _Requirements: Requirement 6_

- [ ] 5.12 Add reset confirmation dialog
  - Add click handler untuk button "Reset ke Default"
  - Show SweetAlert2 confirm dialog
  - Display warning message
  - If confirmed, submit form ke /rules-engine-settings/reset
  - _Requirements: Requirement 8_

- [ ] 5.13 Style with AdminLTE theme
  - Use AdminLTE card components
  - Use AdminLTE form styling
  - Use AdminLTE button styles
  - Use AdminLTE modal styling
  - Ensure responsive design (mobile-friendly)
  - _Requirements: Requirement 1_

---

- [ ] 6. Testing & Quality Assurance
- [ ] 6.1 Test database migrations
  - Run migrations di fresh database
  - Verify table structure
  - Verify indexes
  - Verify foreign keys
  - _Requirements: Requirement 1_

- [ ] 6.2 Test seeder
  - Run seeder
  - Verify 8 records created
  - Verify values match defaults
  - _Requirements: Requirement 1_

- [ ] 6.3 Test RulesEngineSettingsService
  - Test getSettings() returns all settings
  - Test getSetting() returns correct value
  - Test getSetting() returns default when not found
  - Test validateSettings() detects inconsistencies
  - Test updateSettings() saves to database
  - Test updateSettings() creates history
  - Test updateSettings() clears cache
  - Test resetToDefaults() restores values
  - _Requirements: Requirement 2, 3, 4, 5, 7, 8, 10_

- [ ] 6.4 Test PelanggaranRulesEngine integration
  - Test Rules Engine reads from database
  - Test Rules Engine falls back to constants on error
  - Test threshold changes affect new evaluations
  - Test cache is used for performance
  - _Requirements: Requirement 10_

- [ ] 6.5 Test controller access control
  - Test Operator can access settings page
  - Test non-Operator gets 403 error
  - Test Kepala Sekolah can view (if implemented)
  - _Requirements: Requirement 1_

- [ ] 6.6 Test validation scenarios
  - Test surat_3_min <= surat_2_max shows error
  - Test surat_2_min >= surat_2_max shows error
  - Test negative values show error
  - Test zero values show error
  - Test valid values save successfully
  - _Requirements: Requirement 2, 3, 5_

- [ ] 6.7 Test preview functionality
  - Test preview shows comparison table
  - Test preview detects significant changes
  - Test preview shows example impacts
  - _Requirements: Requirement 6_

- [ ] 6.8 Test reset functionality
  - Test reset restores all defaults
  - Test reset creates history record
  - Test reset shows success message
  - _Requirements: Requirement 8_

- [ ] 6.9 Test history functionality
  - Test history shows all changes
  - Test history filters work correctly
  - Test history pagination works
  - Test history displays username correctly
  - _Requirements: Requirement 7_

- [ ] 6.10 Test UI responsiveness
  - Test form layout di desktop
  - Test form layout di tablet
  - Test form layout di mobile
  - Test modal responsiveness
  - _Requirements: Requirement 1_

- [ ] 6.11 Test real-time validation
  - Test validation triggers on input change
  - Test error messages display correctly
  - Test "Simpan" button enables/disables correctly
  - _Requirements: Requirement 5_

- [ ] 6.12 Test end-to-end workflow
  - Login sebagai Operator
  - Akses halaman settings
  - Ubah beberapa threshold
  - Preview perubahan
  - Konfirmasi dan simpan
  - Verify settings tersimpan di database
  - Verify history tercatat
  - Verify cache cleared
  - Catat pelanggaran baru
  - Verify Rules Engine menggunakan threshold baru
  - _Requirements: All_

---

- [ ] 7. Documentation & Polish
- [ ] 7.1 Add inline code comments
  - Comment semua method di service
  - Comment logika validasi
  - Comment fallback mechanism
  - _Requirements: All_

- [ ] 7.2 Update README (if exists)
  - Document fitur Rules Engine Settings
  - Document cara mengakses halaman settings
  - Document cara reset ke default
  - _Requirements: All_

- [ ] 7.3 Add tooltips untuk semua input
  - Add tooltip untuk surat_2_min_poin
  - Add tooltip untuk surat_2_max_poin
  - Add tooltip untuk surat_3_min_poin
  - Add tooltip untuk akumulasi_sedang_min
  - Add tooltip untuk akumulasi_sedang_max
  - Add tooltip untuk akumulasi_kritis
  - Add tooltip untuk frekuensi_atribut
  - Add tooltip untuk frekuensi_alfa
  - _Requirements: Requirement 9_

- [ ] 7.4 Add success/error flash messages
  - Add success message setelah update
  - Add success message setelah reset
  - Add error message jika validasi gagal
  - Add error message jika database error
  - _Requirements: All_

- [ ] 7.5 Add loading indicators
  - Add loading spinner saat preview
  - Add loading spinner saat save
  - Add loading spinner saat reset
  - Disable buttons during loading
  - _Requirements: Requirement 1_

---

- [ ] 8. Final Checkpoint
- Ensure all tests pass, ask the user if questions arise.
- Verify semua fitur berjalan dengan baik
- Verify tidak ada bug atau error
- Verify UI user-friendly dan responsive
- Verify dokumentasi lengkap

---

## Summary

**Total Tasks:** 8 main tasks, 70+ sub-tasks
**Estimated Time:** 15-20 jam
**Priority:** High (Core business logic)

**Key Deliverables:**
1. ✅ Database tables untuk settings dan history
2. ✅ Service layer dengan caching dan validation
3. ✅ Updated Rules Engine yang baca dari database
4. ✅ Controller dengan access control
5. ✅ User-friendly UI dengan real-time validation
6. ✅ Preview dan confirmation workflow
7. ✅ Audit trail lengkap
8. ✅ Comprehensive testing

**Success Criteria:**
- Operator dapat mengubah threshold tanpa edit code
- Validasi mencegah konfigurasi yang tidak konsisten
- Rules Engine menggunakan settings dari database
- Sistem tetap berjalan jika database error (fallback)
- UI mudah digunakan dan responsive
- Semua perubahan tercatat di history
