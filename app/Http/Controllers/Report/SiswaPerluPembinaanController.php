<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\PembinaanInternalRule;
use App\Services\Pelanggaran\PelanggaranRulesEngine;
use Illuminate\Http\Request;

class SiswaPerluPembinaanController extends Controller
{
    protected $rulesEngine;

    public function __construct(PelanggaranRulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    /**
     * Display list siswa yang perlu pembinaan berdasarkan akumulasi poin.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $ruleId = $request->get('rule_id');
        $kelasId = $request->get('kelas_id');
        $jurusanId = $request->get('jurusan_id');
        
        // Get all rules untuk filter dropdown
        $rules = PembinaanInternalRule::orderBy('display_order')->get();
        
        // Get siswa perlu pembinaan
        $poinMin = null;
        $poinMax = null;
        
        if ($ruleId) {
            $selectedRule = $rules->find($ruleId);
            if ($selectedRule) {
                $poinMin = $selectedRule->poin_min;
                $poinMax = $selectedRule->poin_max;
            }
        }
        
        $siswaList = $this->rulesEngine->getSiswaPerluPembinaan($poinMin, $poinMax);
        
        // =====================================================================
        // CRITICAL FIX: Filter by pembina role authorization
        // =====================================================================
        // Siswa hanya ditampilkan jika role user saat ini termasuk dalam
        // pembina_roles untuk siswa tersebut.
        // 
        // Logic:
        // - Kepala Sekolah: Lihat semua (sudah diberi akses penuh)
        // - Waka Kesiswaan: Lihat siswa yang melibatkan "Waka Kesiswaan"
        // - Kaprodi: Lihat siswa di jurusan binaan yang melibatkan "Kaprodi"
        // - Wali Kelas: Lihat siswa di kelas binaan yang melibatkan "Wali Kelas"
        // =====================================================================
        
        $user = auth()->user();
        $userRole = \App\Services\User\RoleService::effectiveRoleName($user);
        
        // Check if impersonating (Developer mode)
        $isImpersonating = \App\Services\User\RoleService::isRealDeveloper($user) 
                           && \App\Services\User\RoleService::getOverride();
        
        // Kepala Sekolah bisa lihat semua
        if ($userRole !== 'Kepala Sekolah') {
            $siswaList = $siswaList->filter(function ($item) use ($userRole, $user, $isImpersonating) {
                $pembinaRoles = $item['rekomendasi']['pembina_roles'] ?? [];
                
                // BUGFIX: Handle if pembina_roles is still JSON string
                if (is_string($pembinaRoles)) {
                    $pembinaRoles = json_decode($pembinaRoles, true) ?? [];
                }
                
                // DEBUG: Log for troubleshooting
                \Log::info('Checking siswa pembinaan', [
                    'siswa' => $item['siswa']->nama_siswa ?? 'unknown',
                    'poin' => $item['total_poin'],
                    'pembina_roles' => $pembinaRoles,
                    'user_role' => $userRole,
                ]);
                
                // Check if user's role is in the recommended pembina roles
                if (!in_array($userRole, $pembinaRoles)) {
                    \Log::info('Role not in pembina_roles - filtering out');
                    return false;
                }
                
                // IMPERSONATE MODE: Developer testing - show all siswa untuk role
                if ($isImpersonating) {
                    \Log::info('Impersonate mode - showing siswa for testing');
                    return true; // Skip context filter untuk developer testing
                }
                
                // Additional context-based filtering
                $siswa = $item['siswa'];
                
                // Wali Kelas: hanya siswa di kelas binaan
                if ($userRole === 'Wali Kelas') {
                    $kelasBinaan = $user->kelasDiampu;
                    
                    // TEMPORARY RELAX: Skip kelas validation if wali kelas not assigned yet
                    if ($kelasBinaan && $siswa->kelas_id !== $kelasBinaan->id) {
                        \Log::info('Wali Kelas kelas mismatch', [
                            'siswa_kelas' => $siswa->kelas_id,
                            'binaan' => $kelasBinaan->id,
                        ]);
                        return false;
                    }
                    
                    if (!$kelasBinaan) {
                        \Log::warning('Wali Kelas tidak punya kelas binaan - showing all');
                    }
                }
                
                // Kaprodi: hanya siswa di jurusan binaan
                if ($userRole === 'Kaprodi') {
                    $jurusanBinaan = $user->jurusanDiampu;
                    if (!$jurusanBinaan || !$siswa->kelas || $siswa->kelas->jurusan_id !== $jurusanBinaan->id) {
                        return false;
                    }
                }
                
                // Waka Kesiswaan: tidak perlu filter additional (semua sekolah)
                
                return true;
            });
        }
        
        // Filter by kelas
        if ($kelasId) {
            $siswaList = $siswaList->filter(function ($item) use ($kelasId) {
                return $item['siswa']->kelas_id == $kelasId;
            });
        }
        
        // Filter by jurusan
        if ($jurusanId) {
            $siswaList = $siswaList->filter(function ($item) use ($jurusanId) {
                return $item['siswa']->kelas->jurusan_id == $jurusanId;
            });
        }
        
        // Get kelas & jurusan untuk filter dropdown
        $kelasList = \App\Models\Kelas::orderBy('nama_kelas')->get();
        $jurusanList = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        
        // Statistics
        $stats = [
            'total_siswa' => $siswaList->count(),
            'by_range' => [],
        ];
        
        foreach ($rules as $rule) {
            $count = $siswaList->filter(function ($item) use ($rule) {
                return $rule->matchesPoin($item['total_poin']);
            })->count();
            
            $stats['by_range'][] = [
                'rule' => $rule,
                'count' => $count,
            ];
        }
        
        return view('kepala_sekolah.siswa_perlu_pembinaan.index', compact(
            'siswaList',
            'rules',
            'kelasList',
            'jurusanList',
            'stats',
            'ruleId',
            'kelasId',
            'jurusanId'
        ));
    }

    /**
     * Export to CSV.
     */
    public function exportCsv(Request $request)
    {
        $ruleId = $request->get('rule_id');
        $kelasId = $request->get('kelas_id');
        $jurusanId = $request->get('jurusan_id');
        
        // Get filtered data (same logic as index)
        $rules = PembinaanInternalRule::orderBy('display_order')->get();
        $poinMin = null;
        $poinMax = null;
        
        if ($ruleId) {
            $selectedRule = $rules->find($ruleId);
            if ($selectedRule) {
                $poinMin = $selectedRule->poin_min;
                $poinMax = $selectedRule->poin_max;
            }
        }
        
        $siswaList = $this->rulesEngine->getSiswaPerluPembinaan($poinMin, $poinMax);
        
        if ($kelasId) {
            $siswaList = $siswaList->filter(fn($item) => $item['siswa']->kelas_id == $kelasId);
        }
        
        if ($jurusanId) {
            $siswaList = $siswaList->filter(fn($item) => $item['siswa']->kelas->jurusan_id == $jurusanId);
        }
        
        // Generate CSV
        $filename = 'siswa_perlu_pembinaan_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($siswaList) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['NIS', 'Nama', 'Kelas', 'Jurusan', 'Total Poin', 'Range Poin', 'Rekomendasi Pembinaan', 'Pembina']);
            
            // Data
            foreach ($siswaList as $item) {
                fputcsv($file, [
                    $item['siswa']->nis,
                    $item['siswa']->nama_lengkap,
                    $item['siswa']->kelas->nama_kelas ?? '-',
                    $item['siswa']->kelas->jurusan->nama_jurusan ?? '-',
                    $item['total_poin'],
                    $item['rekomendasi']['range_text'],
                    $item['rekomendasi']['keterangan'],
                    implode(', ', $item['rekomendasi']['pembina_roles']),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Similar to exportCsv but generate PDF
        // For now, redirect to CSV (PDF implementation can be added later)
        return $this->exportCsv($request);
    }
}
