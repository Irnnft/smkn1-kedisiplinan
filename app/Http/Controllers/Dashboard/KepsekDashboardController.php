<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Traits\HasStatistics;
use Illuminate\View\View;
use App\Models\TindakLanjut;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\JenisPelanggaran;
use App\Services\Pelanggaran\PelanggaranRulesEngine;
use Illuminate\Support\Facades\DB;

class KepsekDashboardController extends Controller
{
    use HasStatistics;

    protected $rulesEngine;

    public function __construct(PelanggaranRulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    public function index(): View
    {
        // ========== 1. STATISTIK UTAMA (Executive Summary) ==========
        $totalSiswa = Siswa::count();
        $pelanggaranBulanIni = RiwayatPelanggaran::whereMonth('tanggal_kejadian', now()->month)->count();
        $pelanggaranSemesterIni = RiwayatPelanggaran::whereYear('tanggal_kejadian', now()->year)->count();
        $tindakanTerbuka = TindakLanjut::whereNotIn('status', ['Selesai', 'Ditolak'])->count();
        
        // ========== 2. KASUS YANG MENUNGGU PERSETUJUAN (Priority) ==========
        $listPersetujuan = TindakLanjut::with(['siswa.kelas', 'suratPanggilan'])
            ->where('status', 'Menunggu Persetujuan')
            ->orderBy('created_at', 'asc')
            ->get();

        // ========== 3. TREN PELANGGARAN (Last 7 Days) ==========
        $trendData = RiwayatPelanggaran::selectRaw('DATE(tanggal_kejadian) as tanggal, COUNT(*) as total')
            ->where('tanggal_kejadian', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // ========== 4. TOP JENIS PELANGGARAN ==========
        $topViolations = RiwayatPelanggaran::selectRaw('jenis_pelanggaran_id, COUNT(*) as jumlah')
            ->groupBy('jenis_pelanggaran_id')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->jenisPelanggaran = $item->jenisPelanggaran;
                return $item;
            });
        
        // Load relations for top violations
        $violationIds = $topViolations->pluck('jenis_pelanggaran_id')->toArray();
        $jenisPelanggaranMap = JenisPelanggaran::whereIn('id', $violationIds)->get()->keyBy('id');
        
        $topViolations = $topViolations->map(function($item) use ($jenisPelanggaranMap) {
            $item->jenisPelanggaran = $jenisPelanggaranMap[$item->jenis_pelanggaran_id] ?? null;
            return $item;
        });

        // ========== 5. BREAKDOWN PER JURUSAN (OPTIMIZED) ==========
        // OPTIMIZATION: Batch fetch all counts in single queries instead of N queries
        
        // Get siswa count per jurusan using subquery
        $siswaCountPerJurusan = DB::table('kelas')
            ->join('siswa', 'kelas.id', '=', 'siswa.kelas_id')
            ->selectRaw('kelas.jurusan_id, COUNT(DISTINCT siswa.id) as siswa_count')
            ->groupBy('kelas.jurusan_id')
            ->pluck('siswa_count', 'jurusan_id');
        
        // Get pelanggaran count per jurusan using subquery
        $pelanggaranCountPerJurusan = DB::table('riwayat_pelanggaran')
            ->join('siswa', 'riwayat_pelanggaran.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->selectRaw('kelas.jurusan_id, COUNT(*) as pelanggaran_count')
            ->groupBy('kelas.jurusan_id')
            ->pluck('pelanggaran_count', 'jurusan_id');
        
        // Get active tindak lanjut count per jurusan
        $tindakLanjutCountPerJurusan = DB::table('tindak_lanjut')
            ->join('siswa', 'tindak_lanjut.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->whereNotIn('tindak_lanjut.status', ['Selesai', 'Ditolak'])
            ->selectRaw('kelas.jurusan_id, COUNT(*) as tindakan_count')
            ->groupBy('kelas.jurusan_id')
            ->pluck('tindakan_count', 'jurusan_id');
        
        // Get jurusan with kelas count
        $jurusanStats = Jurusan::withCount('kelas')
            ->get()
            ->map(function($jurusan) use ($siswaCountPerJurusan, $pelanggaranCountPerJurusan, $tindakLanjutCountPerJurusan) {
                return (object) [
                    'id' => $jurusan->id,
                    'nama' => $jurusan->nama_jurusan,
                    'siswa_count' => $siswaCountPerJurusan[$jurusan->id] ?? 0,
                    'pelanggaran_count' => $pelanggaranCountPerJurusan[$jurusan->id] ?? 0,
                    'tindakan_terbuka' => $tindakLanjutCountPerJurusan[$jurusan->id] ?? 0,
                ];
            });

        // ========== 6. STATISTIK APPROVAL STATUS ==========
        $statusStats = TindakLanjut::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // ========== 7. SISWA PERLU PEMBINAAN (Top 5 Highest Points) ==========
        $siswaPerluPembinaan = $this->rulesEngine->getSiswaPerluPembinaan()
            ->take(5); // Only top 5 for dashboard widget

        return view('dashboards.kepsek', [
            'totalSiswa' => $totalSiswa,
            'pelanggaranBulanIni' => $pelanggaranBulanIni,
            'pelanggaranSemesterIni' => $pelanggaranSemesterIni,
            'tindakanTerbuka' => $tindakanTerbuka,
            'listPersetujuan' => $listPersetujuan,
            'trendData' => $trendData,
            'topViolations' => $topViolations,
            'jurusanStats' => $jurusanStats,
            'statusStats' => $statusStats,
            'siswaPerluPembinaan' => $siswaPerluPembinaan,
        ]);
    }
}
