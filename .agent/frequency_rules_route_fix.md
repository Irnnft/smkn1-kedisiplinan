# FREQUENCY RULES ROUTE FIX

## 🔴 **PROBLEM**

**Error:**
```
ArgumentCountError: Too few arguments to function FrequencyRulesController::store(), 
1 passed and exactly 2 expected
```

**Root Cause:**
- Controller method signature: `store(Request $request, $jenisPelanggaranId)`
- Route was using standard resource route: `Route::resource('frequency-rules', ...)`
- Standard resource routes don't support nested parameters
- View was trying to pass parameter: `route('frequency-rules.store', $jenisPelanggaran->id)`

---

## ✅ **SOLUTION**

### **Changed From Resource Route to Custom Routes**

**Before (WRONG):**
```php
// Standard resource route - doesn't support nested parameters
Route::resource('frequency-rules', FrequencyRulesController::class)
    ->middleware('role:...');
```

**After (CORRECT):**
```php
Route::middleware('role:Operator Sekolah,Kepala Sekolah,Waka Kesiswaan')->group(function () {
    // Index: list all jenis pelanggaran
    Route::get('/frequency-rules', [FrequencyRulesController::class, 'index'])
        ->name('frequency-rules.index');
    
    // Show: view rules for specific jenis pelanggaran  
    Route::get('/frequency-rules/{jenisPelanggaran}', [FrequencyRulesController::class, 'show'])
        ->name('frequency-rules.show');
    
    // Store: create new rule for specific jenis pelanggaran
    Route::post('/frequency-rules/{jenisPelanggaran}', [FrequencyRulesController::class, 'store'])
        ->name('frequency-rules.store');
    
    // Update: update existing rule
    Route::put('/frequency-rules/rule/{rule}', [FrequencyRulesController::class, 'update'])
        ->name('frequency-rules.update');
    
    // Delete: delete rule
    Route::delete('/frequency-rules/rule/{rule}', [FrequencyRulesController::class, 'destroy'])
        ->name('frequency-rules.destroy');
});
```

---

## 🎯 **ROUTE STRUCTURE EXPLAINED**

### **Pattern: Nested Resource**

This is a **nested resource pattern** where:
- **Parent:** JenisPelanggaran
- **Child:** FrequencyRule

**URL Structure:**
```
GET  /frequency-rules                           → List all jenis pelanggaran
GET  /frequency-rules/{jenisPelanggaran}       → View rules for specific jenis
POST /frequency-rules/{jenisPelanggaran}       → Create new rule
PUT  /frequency-rules/rule/{rule}              → Update existing rule
DELETE /frequency-rules/rule/{rule}            → Delete rule
```

---

## 📝 **CONTROLLER METHOD SIGNATURES**

```php
// Index - no parameters
public function index(Request $request) { }

// Show - jenisPelanggaranId parameter
public function show($jenisPelanggaranId) { }

// Store - Request + jenisPelanggaranId
public function store(Request $request, $jenisPelanggaranId) { }

// Update - Request + ruleId
public function update(Request $request, $ruleId) { }

// Destroy - ruleId only
public function destroy($ruleId) { }
```

---

## 🔄 **HOW IT WORKS NOW**

### **1. View Form Submits:**
```blade
<form action="{{ route('frequency-rules.store', $jenisPelanggaran->id) }}" method="POST">
    <!-- Form fields -->
</form>
```

Generates URL: `POST /frequency-rules/1`

### **2. Route Matches:**
```php
Route::post('/frequency-rules/{jenisPelanggaran}', ...)
```

### **3. Controller Receives:**
```php
public function store(Request $request, $jenisPelanggaranId)
{
    // $jenisPelanggaranId = 1 ✅
    // Works!
}
```

---

## 🆚 **COMPARISON: Resource vs Custom Routes**

| Aspect | Resource Route | Custom Route |
|--------|----------------|--------------|
| **Setup** | Simple, one line | Verbose, multiple lines |
| **Flexibility** | Limited, standard only | Full control |
| **Nested Resources** | ❌ Not supported | ✅ Fully supported |
| **Custom Parameters** | ❌ Difficult | ✅ Easy |
| **Use Case** | Simple CRUD | Complex relationships |

---

## 📚 **WHEN TO USE EACH**

### **Use Resource Route When:**
- ✅ Simple CRUD operations
- ✅ No nested relationships
- ✅ Standard RESTful pattern
- ✅ Example: `Route::resource('users', UserController::class)`

### **Use Custom Routes When:**
- ✅ Nested resources (parent-child)
- ✅ Custom parameters needed
- ✅ Non-standard actions
- ✅ Example: Frequency rules (nested under JenisPelanggaran)

---

## 🧪 **TESTING**

**Verify Route Works:**

```bash
php artisan route:list --name=frequency

# Should show:
# POST frequency-rules/{jenisPelanggaran} ✅
```

**Test in Browser:**

1. Navigate to: **Kelola Aturan** → **Frequency Rules**
2. Click **Detail & Kelola** on any jenis pelanggaran
3. Click **Tambah Aturan**
4. Fill form:
   - Frekuensi Min: 1
   - Poin: 100
   - Sanksi: Panggilan Orang Tua
   - Trigger Surat: ✓
   - Pembina: Wali Kelas, Waka Kesiswaan
5. Click **Simpan**
6. Should redirect to show page ✅
7. New rule should appear in list ✅

---

## 🎓 **KEY LEARNINGS**

1. **Resource routes** are great for simple CRUD but don't support nested parameters
2. **Custom routes** give full control for complex patterns
3. **Nested resources** need explicit route definitions
4. **Route parameters** must match controller method signatures
5. **Middleware** can be applied to route groups for consistency

---

## 📋 **FILES MODIFIED**

- ✅ `routes/admin.php` - Changed from resource to custom routes

**No Controller Changes Needed!** Controller was already correct.

---

**Status:** ✅ **FIXED**  
**Impact:** Frequency rules creation now works  
**Testing:** Ready for verification  

Try adding frequency rule again!
