<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RiwayatPelanggaran;
use App\Models\TindakLanjut;
use App\Models\Kelas; // <-- Tambahkan ini

class KaprodiDashboardController extends Controller
{
    public function index(Request $request) // <-- Tambahkan Request $request
    {
        $user = Auth::user();
        $jurusan = $user->jurusanDiampu;

        if (!$jurusan) {
            return view('dashboards.kaprodi_no_data');
        }

        // 1. AMBIL INPUT FILTER (Default: Bulan Ini)
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $kelasId = $request->input('kelas_id'); // Kaprodi bisa filter per kelas

        // 2. SIAPKAN DATA KELAS UNTUK DROPDOWN (Hanya kelas di jurusan ini)
        $kelasJurusan = Kelas::where('jurusan_id', $jurusan->id)->get();

        // 3. STATISTIK UTAMA (Dengan Filter Waktu)
        $totalSiswa = $jurusan->siswa()->count(); // Total siswa biasanya statis

        // Pelanggaran (Filter Waktu & Kelas)
        $queryPelanggaran = RiwayatPelanggaran::whereHas('siswa.kelas', function($q) use ($jurusan, $kelasId) {
            $q->where('jurusan_id', $jurusan->id);
            if ($kelasId) $q->where('id', $kelasId);
        })->whereDate('tanggal_kejadian', '>=', $startDate)
          ->whereDate('tanggal_kejadian', '<=', $endDate);

        $pelanggaranBulanIni = $queryPelanggaran->count();

        // Kasus Aktif (Filter Kelas saja, waktu biasanya tidak relevan untuk kasus aktif)
        $kasusAktif = TindakLanjut::whereHas('siswa.kelas', function($q) use ($jurusan, $kelasId) {
            $q->where('jurusan_id', $jurusan->id);
            if ($kelasId) $q->where('id', $kelasId);
        })->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui'])->count();


        // 4. GRAFIK PERBANDINGAN KELAS (Juga kena filter waktu)
        // "Kelas mana yang paling nakal di rentang tanggal ini?"
        $statistikKelas = DB::table('riwayat_pelanggaran')
            ->join('siswa', 'riwayat_pelanggaran.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas.jurusan_id', $jurusan->id)
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '>=', $startDate)
            ->whereDate('riwayat_pelanggaran.tanggal_kejadian', '<=', $endDate)
            ->select('kelas.nama_kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas.nama_kelas')
            ->orderByDesc('total')
            ->get();

        $chartLabels = $statistikKelas->pluck('nama_kelas');
        $chartData = $statistikKelas->pluck('total');


        // 5. TABEL RIWAYAT TERBARU (Kena Filter Waktu & Kelas)
        $riwayatTerbaru = RiwayatPelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->whereHas('siswa.kelas', function($q) use ($jurusan, $kelasId) {
                $q->where('jurusan_id', $jurusan->id);
                if ($kelasId) $q->where('id', $kelasId);
            })
            ->whereDate('tanggal_kejadian', '>=', $startDate)
            ->whereDate('tanggal_kejadian', '<=', $endDate)
            ->latest('tanggal_kejadian')
            ->take(10)
            ->get();

        return view('dashboards.kaprodi', compact(
            'jurusan', 'totalSiswa', 'pelanggaranBulanIni', 'kasusAktif',
            'chartLabels', 'chartData', 'riwayatTerbaru',
            'kelasJurusan', 'startDate', 'endDate' // Kirim data filter ke view
        ));
    }
}