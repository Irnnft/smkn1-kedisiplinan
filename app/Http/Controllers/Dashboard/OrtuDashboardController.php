<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatPelanggaran;
use App\Models\TindakLanjut;

class OrtuDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil User Orang Tua yang login
        $user = Auth::user();

        // 2. Ambil Data Anak (Relasi 'anakWali' di Model User)
        $siswa = $user->anakWali;

        // Cek jika data anak belum di-link
        if (!$siswa) {
            return view('dashboards.ortu_no_data');
        }

        // 3. Ambil Statistik Poin
        // Kita hitung manual dari riwayat biar akurat
        $totalPoin = RiwayatPelanggaran::where('siswa_id', $siswa->id)
            ->join('jenis_pelanggaran', 'riwayat_pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->sum('jenis_pelanggaran.poin');

        // 4. Ambil Riwayat Pelanggaran (Lengkap)
        $riwayat = RiwayatPelanggaran::with('jenisPelanggaran')
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal_kejadian')
            ->get();

        // 5. Ambil Kasus / Sanksi yang pernah diterima
        $kasus = TindakLanjut::where('siswa_id', $siswa->id)
            ->orderByDesc('created_at')
            ->get();

        return view('dashboards.ortu', [
            'siswa' => $siswa,
            'totalPoin' => $totalPoin,
            'riwayat' => $riwayat,
            'kasus' => $kasus
        ]);
    }
}