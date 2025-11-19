<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatPelanggaran;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\JenisPelanggaran;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // 1. Siapkan Data untuk Dropdown Filter
        $allJurusan = Jurusan::all();
        $allKelas = Kelas::all();
        // Ambil daftar kategori pelanggaran/nama pelanggaran jika perlu (opsional)
        
        // 2. Mulai Query Dasar
        $query = RiwayatPelanggaran::with(['siswa.kelas.jurusan', 'jenisPelanggaran.kategoriPelanggaran', 'guruPencatat']);

        // --- LOGIKA FILTER CANGGIH ---

        // A. Filter Waktu (Selalu Jalan)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_kejadian', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_kejadian', '<=', $request->end_date);
        }

        // B. Filter Hierarki Sekolah (Prioritas Kelas)
        if ($request->filled('kelas_id')) {
            // Jika Kelas dipilih, ABAIKAN Jurusan dan Angkatan
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        } else {
            // Jika Kelas KOSONG, baru cek Jurusan & Angkatan
            
            if ($request->filled('jurusan_id')) {
                $query->whereHas('siswa.kelas', function($q) use ($request) {
                    $q->where('jurusan_id', $request->jurusan_id);
                });
            }

            if ($request->filled('angkatan')) {
                $query->whereHas('siswa.kelas', function($q) use ($request) {
                    $q->where('nama_kelas', 'like', $request->angkatan . ' %');
                });
            }
        }

        // E. Pencarian Nama Siswa
        if ($request->filled('cari_siswa')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->cari_siswa . '%');
            });
        }

        // 3. Eksekusi & Pagination
        $riwayat = $query->latest('tanggal_kejadian')->paginate(20)->withQueryString();

        return view('riwayat.index', compact('riwayat', 'allJurusan', 'allKelas'));
    }
}