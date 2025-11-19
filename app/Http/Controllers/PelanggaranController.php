<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\RiwayatPelanggaran;
use App\Models\TindakLanjut; // <-- IMPORT MODEL INI
use Illuminate\Support\Facades\Auth; // <-- IMPORT AUTH
use Illuminate\Support\Facades\Storage; // <-- IMPORT STORAGE

class PelanggaranController extends Controller
{
    public function create()
    {
        // ... (kode create yang tadi, biarkan saja) ...
        $siswa = Siswa::with('kelas')->get()->sortBy(['kelas.nama_kelas', 'nama_siswa']);
        $jenisPelanggaran = JenisPelanggaran::with('kategoriPelanggaran')->get();

        return view('pelanggaran.create', [
            'daftarSiswa' => $siswa,
            'daftarPelanggaran' => $jenisPelanggaran
        ]);
    }

    /**
     * MENYIMPAN DATA & MENJALANKAN RULES ENGINE
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'tanggal_kejadian' => 'required|date',
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'keterangan' => 'nullable|string',
        ]);

        // 2. Proses Upload Foto
        // Foto akan disimpan di folder: storage/app/public/bukti_pelanggaran
        $pathFoto = $request->file('bukti_foto')->store('bukti_pelanggaran', 'public');

        // 3. Simpan ke Database (Riwayat Pelanggaran)
        RiwayatPelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'guru_pencatat_user_id' => Auth::id(), // ID Guru yang sedang login
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'keterangan' => $request->keterangan,
            'bukti_foto_path' => $pathFoto,
        ]);

        // ===========================================================
        // 🤖 RULES ENGINE (LOGIKA OTOMATISASI SANKSI)
        // ===========================================================
        
        $this->jalankanRulesEngine($request->siswa_id, $request->jenis_pelanggaran_id);

        // ===========================================================

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('pelanggaran.create')
            ->with('success', 'Pelanggaran berhasil dicatat! Sistem poin telah diperbarui.');
    }

    /**
     * Fungsi Logika Rules Engine (Sesuai Tata Tertib SMKN 1 Siak)
     */
    private function jalankanRulesEngine($siswaId, $pelanggaranIdBaru)
    {
        $siswa = Siswa::find($siswaId);
        $pelanggaranBaru = JenisPelanggaran::find($pelanggaranIdBaru);
        $namaPelanggaran = strtolower($pelanggaranBaru->nama_pelanggaran);
        $poinBaru = $pelanggaranBaru->poin;

        // A. HITUNG DATA PENTING
        // 1. Total Poin Akumulasi
        $totalPoin = RiwayatPelanggaran::where('siswa_id', $siswaId)
            ->join('jenis_pelanggaran', 'riwayat_pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->sum('jenis_pelanggaran.poin');

        // 2. Frekuensi Pelanggaran INI (Spesifik jenis ini)
        $frekuensi = RiwayatPelanggaran::where('siswa_id', $siswaId)
            ->where('jenis_pelanggaran_id', $pelanggaranIdBaru)
            ->count();

        // Variabel Penampung Keputusan
        $pemicu = null;
        $sanksi = null;
        $status = 'Baru'; // Default status
        $tipeSurat = null; // Surat 1, Surat 2, atau Surat 3

        // ==================================================================
        // 1. CEK PEMICU BERDASARKAN FREKUENSI (OFFENSE-TRIGGERED -> SURAT 1)
        // ==================================================================
        // Aturan: Pembina hanya Wali Kelas

        // a. Atribut/Seragam (Frek 10)
        if (str_contains($namaPelanggaran, 'atribut') || str_contains($namaPelanggaran, 'seragam')) {
            if ($frekuensi == 10) {
                $pemicu = "Pelanggaran Atribut (Frekuensi ke-10)";
                $tipeSurat = 'Surat 1';
            }
        }
        // b. Terlambat Apel (Frek 10)
        elseif (str_contains($namaPelanggaran, 'terlambat apel')) {
            if ($frekuensi == 10) {
                $pemicu = "Terlambat Apel (Frekuensi ke-10)";
                $tipeSurat = 'Surat 1';
            }
        }
        // c. Terlambat Tidak Apel (> 4, kita ambil start di 5)
        elseif (str_contains($namaPelanggaran, 'terlambat tidak apel')) {
            if ($frekuensi == 5) { // Pemicu awal di frekuensi 5
                $pemicu = "Terlambat Tidak Apel (Frekuensi ke-5)";
                $tipeSurat = 'Surat 1';
            }
        }
        // d. Alfa (Frek 4)
        elseif (str_contains($namaPelanggaran, 'alfa')) {
            if ($frekuensi == 4) {
                $pemicu = "Alfa (Frekuensi ke-4)";
                $tipeSurat = 'Surat 1';
            }
        }
        // e. Cabut (Frek 2)
        elseif (str_contains($namaPelanggaran, 'cabut')) {
            if ($frekuensi == 2) {
                $pemicu = "Cabut (Frekuensi ke-2)";
                $tipeSurat = 'Surat 1';
            }
        }
        // f. Sholat (Frek > 4, start di 5)
        elseif (str_contains($namaPelanggaran, 'sholat')) {
            if ($frekuensi == 5) {
                $pemicu = "Tidak Sholat (Frekuensi ke-5)";
                $tipeSurat = 'Surat 1';
            }
        }
        // g. Membawa HP (Frek 3 -> Sanksi Sita & Panggil Ortu)
        elseif (str_contains($namaPelanggaran, 'hp') || str_contains($namaPelanggaran, 'elektronik')) {
            if ($frekuensi == 3) {
                $pemicu = "Membawa HP/Elektronik (Frekuensi ke-3)";
                $tipeSurat = 'Surat 1';
            }
        }


        // ==================================================================
        // 2. CEK PEMICU BERDASARKAN BERAT PELANGGARAN (OFFENSE-TRIGGERED -> SURAT 2/3)
        // ==================================================================
        
        // Jika belum kena Surat 1, kita cek apakah ini pelanggaran berat
        if (!$tipeSurat) {
            
            // a. Pelanggaran Berat (100 Poin) & (200 Poin) -> SURAT 2
            // (Merokok, Bullying, Sajam, Mencuri, Berkelahi, Alkohol, Pornografi)
            // Aturan: Pembina melibatkan Kaprodi atau Waka
            if ($poinBaru >= 100 && $poinBaru <= 200) {
                $pemicu = "Pelanggaran Berat: " . $pelanggaranBaru->nama_pelanggaran;
                $tipeSurat = 'Surat 2';
            }

            // b. Pelanggaran Sangat Berat (> 500 Poin) -> SURAT 3
            // (Narkoba, Kriminal)
            // Aturan: Pembina melibatkan Kepala Sekolah
            elseif ($poinBaru > 500) {
                $pemicu = "Pelanggaran SANGAT BERAT: " . $pelanggaranBaru->nama_pelanggaran;
                $tipeSurat = 'Surat 3';
                $status = 'Menunggu Persetujuan'; // Wajib diketahui Kepsek
            }
        }


        // ==================================================================
        // 3. CEK PEMICU BERDASARKAN AKUMULASI POIN (ACCUMULATION-TRIGGERED)
        // ==================================================================
        // Logika akumulasi bisa menimpa logika pelanggaran tunggal jika levelnya lebih tinggi
        
        // Range 55 - 300 -> SURAT 2
        // (Wali+Kaprodi atau Wali+Kaprodi+Waka)
        if ($totalPoin >= 55 && $totalPoin <= 300) {
            // Jika sebelumnya belum ada surat, ATAU surat sebelumnya cuma Surat 1
            // Maka eskalasi ke Surat 2
            if (!$tipeSurat || $tipeSurat == 'Surat 1') {
                $pemicu = "Akumulasi Poin Mencapai $totalPoin (Batas 55-300)";
                $tipeSurat = 'Surat 2';
            }
        }
        
        // Range 305 - 500 -> SURAT 3
        // (Wali+Kaprodi+Waka+Kepsek)
        elseif ($totalPoin >= 305 && $totalPoin <= 500) {
            // Eskalasi ke Surat 3
            $pemicu = "Akumulasi Poin Mencapai $totalPoin (Batas 305-500)";
            $tipeSurat = 'Surat 3';
            $status = 'Menunggu Persetujuan';
        }

        // Range > 500 -> SURAT 3 / DROP OUT
        elseif ($totalPoin > 500) {
            $pemicu = "Akumulasi Poin MELEBIHI 500 (Dikembalikan ke Orang Tua)";
            $tipeSurat = 'Surat 3';
            $status = 'Menunggu Persetujuan';
        }


        // ==================================================================
        // 4. EKSEKUSI: BUAT ATAU UPDATE KASUS (ESKALASI)
        // ==================================================================
        
        if ($tipeSurat) {
            $sanksi = "Pemanggilan Orang Tua ($tipeSurat)";

            // Cek kasus aktif
            $kasusAktif = TindakLanjut::with('suratPanggilan')
                ->where('siswa_id', $siswaId)
                ->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui', 'Ditangani'])
                ->latest()
                ->first();

            if (!$kasusAktif) {
                // SKENARIO A: Belum ada kasus aktif -> BUAT BARU
                $tindakLanjut = TindakLanjut::create([
                    'siswa_id' => $siswaId,
                    'pemicu' => $pemicu,
                    'sanksi_deskripsi' => $sanksi,
                    'status' => $status,
                ]);

                $tindakLanjut->suratPanggilan()->create([
                    'nomor_surat' => 'DRAFT/' . date('Y') . '/' . rand(1000, 9999),
                    'tipe_surat' => $tipeSurat,
                    'tanggal_surat' => now(),
                ]);
                
                } else {
                // SKENARIO B: Sudah ada kasus aktif -> CEK APAKAH PERLU ESKALASI?
                
                // Kita butuh cara membandingkan level surat. 
                // Surat 3 > Surat 2 > Surat 1.
                $levelLama = 0;
                if ($kasusAktif->suratPanggilan) {
                    $strLama = $kasusAktif->suratPanggilan->tipe_surat; // "Surat 1"
                    $levelLama = (int) filter_var($strLama, FILTER_SANITIZE_NUMBER_INT); 
                }
                
                $levelBaru = (int) filter_var($tipeSurat, FILTER_SANITIZE_NUMBER_INT);

                // Jika Level Baru LEBIH TINGGI dari Level Lama (Contoh: 3 > 2)
                // Maka kita UPDATE kasus yang ada agar menjadi lebih berat.
                if ($levelBaru > $levelLama) {
                    
                    $kasusAktif->update([
                        'pemicu' => $pemicu . " (Eskalasi dari Level $levelLama)", // Update pemicu
                        'sanksi_deskripsi' => $sanksi, // Update sanksi jadi lebih berat
                        'status' => $status, // Update status (misal jadi Menunggu Persetujuan)
                    ]);

                    // Update juga tipe suratnya di tabel surat
                    if ($kasusAktif->suratPanggilan) {
                        $kasusAktif->suratPanggilan()->update([
                            'tipe_surat' => $tipeSurat
                        ]);
                    }
                }
            }
        }
    }
}