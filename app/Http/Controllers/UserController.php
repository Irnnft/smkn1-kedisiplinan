<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan Daftar User
     */
    public function index(Request $request)
    {
        // 1. Siapkan Data untuk Dropdown Filter
        $roles = Role::all();

        // 2. Query Dasar (Eager load role agar performa ringan)
        $query = User::with('role');

        // --- PERBAIKAN LOGIKA FILTER ---

        // A. Pencarian Keyword (Nama / Username / Email)
        // Kita BUNGKUS dalam function($q) agar logika AND/OR tidak bocor
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('username', 'like', '%' . $request->cari . '%')
                  ->orWhere('email', 'like', '%' . $request->cari . '%');
            });
        }

        // B. Filter Role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // 3. Eksekusi & Pagination
        // Urutkan berdasarkan Role dulu, baru Nama
        $users = $query->orderBy('role_id')
                       ->orderBy('nama')
                       ->paginate(10)
                       ->withQueryString(); // PENTING: Agar filter tetap ada saat klik Halaman 2

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Form Tambah User
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Simpan User Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Form Edit User
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Hapus User
     */
    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}