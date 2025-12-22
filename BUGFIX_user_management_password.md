# 🐛 **BUG REPORT: User Management Issues**

**Date**: 2025-12-22  
**Severity**: **CRITICAL** (Password update tidak berfungsi)  
**Status**: **IDENTIFIED** ✅

---

## 📋 **SUMMARY - 3 Issues Ditemukan**

| # | Issue | Severity | Status |
|---|-------|----------|--------|
| 1 | **Update Password User Tidak Berfungsi** | 🔴 CRITICAL | Identified |
| 2 | **Password Default Kaprodi Auto-Generated** | 🟡 INFO | Documented |
| 3 | **Password Default Wali Kelas Auto-Generated** | 🟡 INFO | Documented |
| 4 | **Password Manual Create User** | 🟡 INFO | Documented |

---

## 1️⃣ **BUG: Update Password User Tidak Berfungsi**

### **Problem**:
```
Operator → Manajemen User → Edit User →   
Input "Password Baru" → Update User →
❌ GAGAL LOGIN dengan password baru!
```

### **Root Cause**:

**Form Edit TIDAK PUNYA field password!**

```
File: resources/views/users/edit.blade.php
❌ TIDAK ADA input field "password"
```

**Request Validation TIDAK accept password:**

```php
// File: app/Http/Requests/User/UpdateUserRequest.php
public function rules(): array
{
    return [
        'role_id' => ['sometimes', 'exists:roles,id'],
        'nama' => ['required', 'string', 'max:255'],
        'username' => ['required', ...],
        'email' => ['required', 'email', ...],
        'phone' => ['nullable', 'string', 'max:20'],
        'nip' => ['nullable', 'string', 'max:20'],
        'nuptk' => ['nullable', 'string', 'max:20'],
        'is_active' => ['boolean'],
        
        // ❌ TIDAK ADA 'password' field!
        // ❌ TIDAK ADA 'password_confirmation' field!
    ];
}
```

**Backend SIAP, tapi form tidak kirim data!**

```php
// File: app/Services/User/UserService.php::updateUser()
// ✅ Logic sudah benar:
if ($data->password) {
    $updateData['password'] = Hash::make($data->password);
    $updateData['password_changed_at'] = now();
}

// ❌ Tapi $data->password SELALU NULL karena form tidak kirim!
```

---

### **Solution**:

#### **1. Tambahkan Field Password di Form Edit**

**File**: `resources/views/users/edit.blade.php`

Tambahkan setelah field email:

```blade
<!-- Password Baru (Optional) -->
<div class="col-span-2">
    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Password Baru (Opsional)
    </label>
    <input 
        type="password" 
        name="password" 
        id="password"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
        placeholder="Kosongkan jika tidak ingin mengubah password"
        autocomplete="new-password"
    >
    @error('password')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
</div>

<!-- Konfirmasi Password -->
<div class="col-span-2">
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Konfirmasi Password Baru
    </label>
    <input 
        type="password" 
        name="password_confirmation" 
        id="password_confirmation"
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
        placeholder="Ulangi password baru"
        autocomplete="new-password"
    >
</div>
```

#### **2. Update Validation Rules**

**File**: `app/Http/Requests/User/UpdateUserRequest.php`

Tambahkan di method `rules()`:

```php
public function rules(): array
{
    $userId = $this->route('user');

    return [
        'role_id' => ['sometimes', 'exists:roles,id'],
        'nama' => ['required', 'string', 'max:255'],
        'username' => [
            'required',
            'string',
            'max:50',
            Rule::unique('users', 'username')->ignore($userId),
        ],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($userId),
        ],
        'phone' => ['nullable', 'string', 'max:20'],
        'nip' => ['nullable', 'string', 'max:20'],
        'nuptk' => ['nullable', 'string', 'max:20'],
        'is_active' => ['boolean'],
        
        // Role-specific assignments
        'kelas_id' => ['nullable', 'exists:kelas,id'],
        'jurusan_id' => ['nullable', 'exists:jurusan,id'],
        'siswa_ids' => ['nullable', 'array'],
        'siswa_ids.*' => ['exists:siswa,id'],
        
        // ✅ ADD THIS:
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ];
}
```

#### **3. Update Custom Attributes**

Tambahkan di `attributes()`:

```php
public function attributes(): array
{
    return [
        'role_id' => 'Role',
        'nama' => 'Nama Lengkap',
        'username' => 'Username',
        'email' => 'Email',
        'phone' => 'Nomor HP',
        'nip' => 'NIP',
        'nuptk' => 'NUPTK',
        'is_active' => 'Status Aktif',
        'password' => 'Password Baru',           // ✅ ADD
        'password_confirmation' => 'Konfirmasi Password', // ✅ ADD
    ];
}
```

---

## 2️⃣ **INFO: Password Default Kaprodi Auto-Generated**

### **Scenario**: 
```
Operator → Buat Jurusan Baru →
☑️ Checklist "Generate Akun Kaprodi Otomatis" →
Simpan
```

### **Password yang Di-generate**:

**Ada 2 KONDISI berbeda:**

#### **A. Saat Create Jurusan Baru (Store)**:
```php
// File: app/Services/MasterData/JurusanService.php
// Line: 260

$password = Str::random(10); // ❌ RANDOM 10 karakter!
```

**❌ PROBLEM**: Password acak, operator tidak tahu passwordnya!

**Example**:
```
Jurusan: Akuntansi (Kode: AKL)
Username: kaprodi.akl
Password: Xy9mK2pQv1  ← Random, tidak predictable!
Email: kaprodi.akl@no-reply.local
```

**Credentials di-flash ke session**:
```php
session()->flash('kaprodi_created', [
    'username' => 'kaprodi.akl',
    'password' => 'Xy9mK2pQv1'
]);
```

**⚠️ Operator HARUS catat password saat itu juga!**

---

#### **B. Saat Update Jurusan (Re-generate Kaprodi)**:
```php
// File: app/Services/MasterData/JurusanService.php
// Line: 257-259

// Different logic for update!
$password = 'smkn1.kaprodi.' . $cleanKode;
```

**✅ Standardized password!**

**Example**:
```
Jurusan: Akuntansi (Kode: AKL)
Username: kaprodi.akl
Password: smkn1.kaprodi.akl  ← Predictable!
Email: kaprodi.akl@no-reply.local
```

---

### **Rekomendasi Fix**:

**Gunakan standardized password untuk SEMUA kondisi:**

```php
// Ganti line 257-262 menjadi:
// ALWAYS use standardized password (consistent)
$password = 'smkn1.kaprodi.' . $cleanKode;
```

**Benefit**:
- ✅ Password predictable
- ✅ Operator tidak perlu catat
- ✅ Mudah di-reset jika lupa (tinggal rekon struksi)

---

## 3️⃣ **INFO: Password Default Wali Kelas Auto-Generated**

### **Scenario**:
```
Operator → Buat Kelas Baru →
☑️ Checklist "Generate Akun Wali Kelas Otomatis" →
Simpan
```

### **Password yang Di-generate**:

```php
// File: app/Services/MasterData/KelasService.php
// Line: 315

$password = 'smkn1.walikelas.' . $tingkat . $kodeSafe . $nomor;
```

**✅ ALWAYS standardized!**

**Example**:
```
Kelas: X AKL 1
Tingkat: 10
Jurusan: AKL
Nomor: 1

Username: walikelas.10.akl1
Password: smkn1.walikelas.10akl1  ← Predictable!
Email: walikelas.10.akl1@no-reply.local
```

**✅ Consistent & Predictable!**

---

## 4️⃣ **INFO: Password Manual Create User**

### **Scenario**:
```
Operator → Manajemen User → Tambah User Baru →
Pilih Role → Isi Form → Simpan
```

### **Password**:

```php
// File: app/Services/User/UserService.php
// Line: 92

'password' => Hash::make($data['password']),
```

**✅ Operator WAJIB input password sendiri!**

**Form validation**:
```php
// File: app/Http/Requests/User/CreateUserRequest.php
'password' => ['required', 'string', 'min:8', 'confirmed'],
```

**Nama & Username**:
```php
// Auto-generated by UserNamingService based on role:

// Wali Kelas + Kelas binaan:
nama: "Wali Kelas X AKL 1"
username: "walikelas.10.akl1"

// Kaprodi + Jurusan binaan:
nama: "Kaprodi Akuntansi"
username: "kaprodi.akl"

// Generic role (no assignment):
nama: "Operator Sekolah"
username: "operator"
```

---

## ✅ **ACTION ITEMS**

### **Priority 1 - CRITICAL** 🔴:
- [ ] Tambahkan field password di `resources/views/users/edit.blade.php`
- [ ] Update validation `app/Http/Requests/User/UpdateUserRequest.php`
- [ ] Test update password user

### **Priority 2 - RECOMMENDED** 🟡:
- [ ] Standardize Kaprodi password (gunakan format predictable untuk create)
- [ ] Dokumentasikan password default di user manual
- [ ] Tambahkan info "Password Default" di UI saat generate akun

### **Priority 3 - NICE TO HAVE** 🟢:
- [ ] Buat halaman "Reset Password" bulk untuk admin
- [ ] Email notification saat akun auto-generated
- [ ] Password strength indicator di form

---

## 📝 **Password Format Summary**

| Akun | Kondisi | Format Password | Example |
|------|---------|-----------------|---------|
| **Kaprodi** | Auto (Create) | ❌ `Str::random(10)` | `Xy9mK2pQv1` |
| **Kaprodi** | Auto (Update) | ✅ `smkn1.kaprodi.{kode}` | `smkn1.kaprodi.akl` |
| **Wali Kelas** | Auto | ✅ `smkn1.walikelas.{tingkat}{kode}{nomor}` | `smkn1.walikelas.10akl1` |
| **Manual** | Operator input | ✅ Operator tentukan | `password123` |
| **Update** | Edit user | ❌ **TIDAK BISA** (BUG!) | N/A |

---

**Compiled by**: AI Assistant  
**Review Status**: Pending User Testing
