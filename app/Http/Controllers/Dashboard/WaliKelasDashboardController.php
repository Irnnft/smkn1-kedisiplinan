<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Import Model
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TindakLanjut;
use App\Models\RiwayatPelanggaran;

// ... (namespace & import sama) ...

class WaliKelasDashboardController extends Controller
{
    public function index(Request $request) // Tambahkan Request
    {
        $user = Auth::user();
        $kelas = $user->kelasDiampu;

        if (!$kelas) {
            return view('dashboards.walikelas_no_data');
        }

        $siswaIds = $kelas->siswa->pluck('id');

        // FILTER WAKTU (Default: Bulan Ini)
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // 4. KASUS (Tidak perlu filter waktu, karena kasus sifatnya status hidup/mati)
        $kasusBaru = TindakLanjut::with(['siswa', 'suratPanggilan'])
            ->whereIn('siswa_id', $siswaIds)
            ->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui', 'Ditangani'])
            ->latest()
            ->get();

        // 5. RIWAYAT (Kena Filter Waktu)
        $riwayatTerbaru = RiwayatPelanggaran::with(['siswa', 'jenisPelanggaran'])
            ->whereIn('siswa_id', $siswaIds)
            ->whereDate('tanggal_kejadian', '>=', $startDate)
            ->whereDate('tanggal_kejadian', '<=', $endDate)
            ->latest('tanggal_kejadian')
            ->take(20) // Bisa ambil lebih banyak jika difilter
            ->get();

        return view('dashboards.walikelas', compact(
            'kelas', 'kasusBaru', 'riwayatTerbaru', 'startDate', 'endDate'
        ));
    }
}
