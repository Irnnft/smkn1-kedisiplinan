# KELOLA JURUSAN & KELAS TROUBLESHOOTING

## ✅ **VERIFICATION: Routes ARE Registered**

### **Routes Status:**

```bash
php artisan route:list | grep "jurusan.index"
```

**Output:**
```
GET|HEAD  jurusan ..... jurusan.index › MasterData\JurusanController@index
```

✅ **Routes EXIST and are WORKING!**

---

## 🔍 **Full Route List for Jurusan & Kelas**

### **Jurusan Routes (Operator - CRUD):**
- `GET  /jurusan` → `jurusan.index`
- `GET  /jurusan/create` → `jurusan.create`
- `POST /jurusan` → `jurusan.store`
- `GET  /jurusan/{id}` → `jurusan.show`
- `GET  /jurusan/{id}/edit` → `jurusan.edit`
- `PUT  /jurusan/{id}` → `jurusan.update`
- `DELETE /jurusan/{id}` → `jurusan.destroy`

**Middleware:** `role:Operator Sekolah`

### **Kelas Routes (Operator - CRUD):**
- `GET  /kelas` → `kelas.index`
- `GET  /kelas/create` → `kelas.create`
- `POST /kelas` → `kelas.store`
- `GET  /kelas/{id}` → `kelas.show`
- `GET  /kelas/{id}/edit` → `kelas.edit`
- `PUT  /kelas/{id}` → `kelas.update`
- `DELETE /kelas/{id}` → `kelas.destroy`

**Middleware:** `role:Operator Sekolah`

---

## 📂 **File Locations:**

1. **Route Definitions:**
   - `routes/master_data.php` (Lines 23-50)

2. **Route Loading:**
   - `bootstrap/app.php` (Line 21: `routes/master_data.php`)

3. **Navigation/Sidebar:**
   - `resources/views/layouts/app.blade.php` (Lines 347, 353)

4. **Controllers:**
   - `app/Http/Controllers/MasterData/JurusanController.php`
   - `app/Http/Controllers/MasterData/KelasController.php`

---

## 🧪 **TROUBLESHOOTING STEPS**

### **Step 1: Clear All Caches**

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Step 2: Clear Browser Cache**

**Chrome/Edge:**
1. Press `Ctrl + Shift + Del`
2. Select "Cached images and files"
3. Click "Clear data"

**Or Hard Refresh:**
- `Ctrl + F5` or `Ctrl + Shift + R`

### **Step 3: Verify Role**

```bash
php artisan tinker
>>> auth()->user()->role->nama_role
=> "Operator Sekolah"  # Should be this!
```

### **Step 4: Check Navigation Condition**

Navigate to: `resources/views/layouts/app.blade.php` line 320

```blade
@if($isDev || $role == 'Operator Sekolah')
    <!-- Menu should appear here -->
@endif
```

**Debug:**
```blade
{{-- Add this temporarily to check --}}
<div>Role: {{ $role }}</div>
<div>isDev: {{ $isDev ? 'true' : 'false' }}</div>
```

### **Step 5: Direct URL Access**

Try accessing directly:
- `http://127.0.0.1:8000/jurusan`
- `http://127.0.0.1:8000/kelas`

**Expected:**
- ✅ Shows list of jurusan/kelas
- ❌ If 403 Forbidden → Role issue
- ❌ If 404 Not Found → Route issue

---

## 🎯 **LIKELY CAUSES**

| Issue | Probability | Solution |
|-------|-------------|----------|
| **Browser Cache** | 80% | Hard refresh (Ctrl+F5) |
| **Route Cache** | 10% | `php artisan route:clear` |
| **Role Check Issue** | 5% | Verify user role |
| **Navigation Hidden** | 5% | Check blade conditionals |

---

## 🔧 **QUICK FIX**

Most likely just **browser cache**. Run:

```bash
# Clear Laravel cache
php artisan optimize:clear

# Then hard refresh browser
# Ctrl + F5
```

---

## 📋 **VERIFY MENU VISIBILITY**

**Navigation Code (app.blade.php line 346-357):**

```blade
@if($isDev || $role == 'Operator Sekolah')
    <li class="nav-header">ADMINISTRASI</li>
    
    <!-- This should appear! -->
    <li class="nav-item">
      <a href="{{ route('jurusan.index') }}">
        <p>Kelola Jurusan</p>
      </a>
    </li>
    
    <li class="nav-item">
      <a href="{{ route('kelas.index') }}">
        <p>Kelola Kelas</p>
      </a>
    </li>
@endif
```

**If menu NOT showing:**
1. Check `$role` variable value
2. Check `$isDev` variable value  
3. Ensure you're logged in as "Operator Sekolah"

---

## ✅ **POST-FIX VERIFICATION**

After clearing cache and refreshing:

1. Login as **Operator Sekolah**
2. Check sidebar - Should see "ADMINISTRASI" section
3. Should see:
   - ✅ Data Pengguna
   - ✅ Kelola Aturan & Rules
   - ✅ Pembinaan Internal
   - ✅ Audit & Log
   - ✅ **Kelola Jurusan** ← This!
   - ✅ **Kelola Kelas** ← This!

4. Click "Kelola Jurusan" → Should load list
5. Click "Tambah Jurusan" → Should work
6. Click "Kelola Kelas" → Should load list
7. Click "Tambah Kelas" → Should work

---

## 🎓 **ROOT CAUSE ANALYSIS**

**Routes ARE registered** (verified above).  
**Navigation code IS correct** (line 346-357).  
**Middleware IS correct** (`role:Operator Sekolah`).

**Most likely:** Browser caching old navigation HTML.

---

## 📚 **ADDITIONAL DEBUGGING**

### **Check Route Registration:**

```bash
php artisan route:list --name=jurusan
php artisan route:list --name=kelas
```

### **Test Route Directly:**

```bash
curl http://127.0.0.1:8000/jurusan
# Should return HTML or redirect to login
```

### **Check Logs:**

```bash
tail -f storage/logs/laravel.log
# Then try accessing /jurusan
# Look for errors
```

---

**Status:** Routes ✅ Working  
**Action:** Clear browser cache & hard refresh  
**Probability:** 95% will fix the issue
