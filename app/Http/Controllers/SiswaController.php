<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User; // Untuk Wali Murid
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * TAMPILKAN DAFTAR SISWA (DENGAN FILTER LENGKAP)
     */
    public function index(Request $request)
    {
        // 1. Siapkan Data untuk Dropdown Filter
        $allJurusan = \App\Models\Jurusan::all();
        $allKelas = \App\Models\Kelas::orderBy('nama_kelas')->get();

        // 2. Query Dasar (Eager Load Kelas & Jurusan biar ringan)
        $query = Siswa::with('kelas.jurusan');

        // ... (Kode sebelumnya tetap sama) ...

        // --- LOGIKA FILTER CERDAS (HIERARKI) ---

        // 1. Pencarian Keyword (Selalu Jalan)
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->cari . '%')
                  ->orWhere('nisn', 'like', '%' . $request->cari . '%');
            });
        }

        // 2. Logika Prioritas Kelas
        if ($request->filled('kelas_id')) {
            
            // SKENARIO A: User memilih Kelas Spesifik
            // Kita langsung filter berdasarkan ID Kelas.
            // Kita ABAIKAN 'jurusan_id' dan 'tingkat' agar tidak terjadi konflik logika.
            $query->where('kelas_id', $request->kelas_id);

        } else {
            
            // SKENARIO B: User TIDAK memilih Kelas (Filter lebih umum)
            // Baru kita cek Jurusan dan Tingkat
            
            // Filter Jurusan
            if ($request->filled('jurusan_id')) {
                $query->whereHas('kelas', function($q) use ($request) {
                    $q->where('jurusan_id', $request->jurusan_id);
                });
            }

            // Filter Tingkat (X, XI, XII)
            if ($request->filled('tingkat')) {
                $query->whereHas('kelas', function($q) use ($request) {
                    $q->where('nama_kelas', 'like', $request->tingkat . ' %');
                });
            }
        }
        
        // ... (Pagination dan return view tetap sama) ...
        // 3. Eksekusi & Pagination
        // withQueryString() PENTING agar saat klik Page 2, filter tidak hilang
        $siswa = $query->orderBy('kelas_id')->orderBy('nama_siswa')
                       ->paginate(20)
                       ->withQueryString(); 

        return view('siswa.index', compact('siswa', 'allJurusan', 'allKelas'));
    }
    /**
     * 2. TAMPILKAN FORM TAMBAH SISWA
     */
    public function create()
    {
        $kelas = Kelas::all();
        // Ambil user yang rolenya 'Orang Tua'
        $orangTua = User::whereHas('role', function($q){
            $q->where('nama_role', 'Orang Tua');
        })->get();

        return view('siswa.create', compact('kelas', 'orangTua'));
    }

    /**
     * 3. SIMPAN DATA SISWA BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama_siswa' => 'required',
            'kelas_id' => 'required',
            'nomor_hp_ortu' => 'nullable|numeric',
            // 'orang_tua_user_id' opsional
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Ditambahkan');
    }

    /**
     * 4. TAMPILKAN FORM EDIT
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        $orangTua = User::whereHas('role', function($q){
            $q->where('nama_role', 'Orang Tua');
        })->get();

        return view('siswa.edit', compact('siswa', 'kelas', 'orangTua'));
    }

    /**
     * 5. UPDATE DATA SISWA
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn,' . $siswa->id, // Abaikan ID ini saat cek unique
            'nama_siswa' => 'required',
            'kelas_id' => 'required',
        ]);

        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Diupdate');
    }

    /**
     * 6. HAPUS SISWA
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Dihapus');
    }
}