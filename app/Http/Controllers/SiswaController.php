<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR SISWA
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->nama_role;

        $allJurusan = Jurusan::all();
        $allKelas = Kelas::orderBy('nama_kelas')->get();

        $query = Siswa::with('kelas.jurusan');

        // --- LOGIKA DATA SCOPING ---
        if ($role == 'Wali Kelas') {
            $kelasBinaan = $user->kelasDiampu;
            if ($kelasBinaan) {
                $query->where('kelas_id', $kelasBinaan->id);
            } else {
                $query->where('id', 0); 
            }
        }
        elseif ($role == 'Kaprodi') {
            $jurusanBinaan = $user->jurusanDiampu;
            if ($jurusanBinaan) {
                $query->whereHas('kelas', function($q) use ($jurusanBinaan) {
                    $q->where('jurusan_id', $jurusanBinaan->id);
                });
            } else {
                $query->where('id', 0);
            }
        }

        // --- LOGIKA FILTER ---
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->cari . '%')
                  ->orWhere('nisn', 'like', '%' . $request->cari . '%');
            });
        }

        if ($role != 'Wali Kelas' && $request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if (!in_array($role, ['Wali Kelas', 'Kaprodi']) && $request->filled('jurusan_id')) {
             $query->whereHas('kelas', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('nama_kelas', 'like', $request->tingkat . ' %');
            });
        }

        $siswa = $query->orderBy('kelas_id')->orderBy('nama_siswa')
                       ->paginate(20)->withQueryString();

        return view('siswa.index', compact('siswa', 'allJurusan', 'allKelas'));
    }

    /**
     * TAMPILKAN FORM TAMBAH SISWA
     */
    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        // [UPDATE] Ambil daftar user dengan role 'Orang Tua' untuk dropdown
        $orangTua = User::whereHas('role', function($q){
            $q->where('nama_role', 'Orang Tua');
        })->orderBy('nama')->get();

        return view('siswa.create', compact('kelas', 'orangTua'));
    }

    /**
     * SIMPAN DATA SISWA BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|numeric|unique:siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'nomor_hp_ortu' => 'nullable|numeric',
            // [UPDATE] Validasi opsional untuk orang tua
            'orang_tua_user_id' => 'nullable|exists:users,id',
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Ditambahkan');
    }

    /**
     * TAMPILKAN FORM EDIT
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        
        $orangTua = User::whereHas('role', function($q){
            $q->where('nama_role', 'Orang Tua');
        })->orderBy('nama')->get();

        return view('siswa.edit', compact('siswa', 'kelas', 'orangTua'));
    }

    /**
     * UPDATE DATA SISWA
     */
    public function update(Request $request, Siswa $siswa)
    {
        $role = Auth::user()->role->nama_role;

        // Jika Wali Kelas, Validasi lebih longgar (Cuma HP)
        if ($role == 'Wali Kelas') {
            $request->validate([
                'nomor_hp_ortu' => 'nullable|numeric',
            ]);
            
            $siswa->update([
                'nomor_hp_ortu' => $request->nomor_hp_ortu
            ]);
        } 
        // Jika Operator, Validasi Ketat
        else {
            $request->validate([
                'nisn' => 'required|numeric|unique:siswa,nisn,' . $siswa->id,
                'nama_siswa' => 'required|string|max:255',
                'kelas_id' => 'required|exists:kelas,id',
                'nomor_hp_ortu' => 'nullable|numeric',
                'orang_tua_user_id' => 'nullable|exists:users,id',
            ]);
            
            $siswa->update($request->all());
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * HAPUS SISWA
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Dihapus');
    }
}