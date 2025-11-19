<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\TindakLanjut;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;

class KepsekDashboardController extends Controller
{
    public function index(): View
    {
        // 1. STATISTIK RINGKAS (Executive Summary)
        $totalSiswa = Siswa::count();
        $pelanggaranBulanIni = RiwayatPelanggaran::whereMonth('tanggal_kejadian', now()->month)->count();
        
        // 2. TUGAS UTAMA: KASUS YANG MENUNGGU PERSETUJUAN
        // Ini query paling penting untuk Kepsek
        $listPersetujuan = TindakLanjut::with(['siswa.kelas', 'suratPanggilan'])
            ->where('status', 'Menunggu Persetujuan')
            ->orderBy('created_at', 'asc') // Yang lama didahulukan
            ->get();

        return view('dashboards.kepsek', [
            'totalSiswa' => $totalSiswa,
            'pelanggaranBulanIni' => $pelanggaranBulanIni,
            'listPersetujuan' => $listPersetujuan
        ]);
    }
}