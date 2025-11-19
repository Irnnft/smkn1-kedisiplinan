<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjut;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TindakLanjutController extends Controller
{
    /**
     * Menampilkan Halaman Detail/Kelola Kasus
     */
    public function edit($id)
    {
        // 1. Ambil data kasus beserta relasinya (Siswa, Surat)
        $kasus = TindakLanjut::with(['siswa.kelas', 'suratPanggilan'])->findOrFail($id);

        // (Opsional: Tambahkan validasi keamanan di sini)
        // Misal: Pastikan yang membuka adalah Wali Kelas yang berhak
        
        return view('tindaklanjut.edit', [
            'kasus' => $kasus
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'status' => 'required|in:Baru,Ditangani,Selesai,Menunggu Persetujuan,Disetujui', 
            'denda_deskripsi' => 'nullable|string',
            'tanggal_tindak_lanjut' => 'required|date',
        ]);

        // 2. Ambil Data Kasus & User
        $kasus = TindakLanjut::findOrFail($id);
        $userRole = Auth::user()->role->nama_role;
        $statusLama = $kasus->status;
        $statusBaru = $request->status;

        // ===========================================================
        // 🛡️ LOGIC GUARD (PENJAGA KEAMANAN STATUS)
        // ===========================================================

        // ATURAN 1: JIKA KASUS SUDAH DISETUJUI KEPSEK
        // Tidak boleh dikembalikan ke "Baru" atau "Menunggu Persetujuan" oleh SIAPAPUN.
        // Hanya boleh maju ke "Ditangani" atau "Selesai".
        if ($statusLama == 'Disetujui') {
            if (in_array($statusBaru, ['Baru', 'Menunggu Persetujuan'])) {
                return back()->withErrors(['status' => 'ILLEGAL ACTION: Kasus yang sudah disetujui Kepala Sekolah tidak bisa dikembalikan ke status awal! Silakan lanjutkan ke proses penanganan.']);
            }
        }

        // ATURAN 2: JIKA STATUS SEDANG "MENUNGGU PERSETUJUAN"
        // Hanya "Kepala Sekolah" yang boleh mengubahnya.
        // Wali Kelas / Waka tidak boleh menyentuh statusnya sampai Kepsek bertindak.
        if ($statusLama == 'Menunggu Persetujuan') {
            if ($userRole != 'Kepala Sekolah') {
                return back()->withErrors(['status' => 'AKSES DITOLAK: Kasus ini sedang menunggu persetujuan Kepala Sekolah. Anda tidak dapat mengubah statusnya saat ini.']);
            }
        }

        // ATURAN 3: PROTEKSI STATUS "DISITUJU" (Hanya Kepsek yang bisa set "Disetujui")
        // Jangan sampai Wali Kelas iseng mengubah status "Baru" langsung jadi "Disetujui" tanpa lewat Kepsek.
        if ($statusBaru == 'Disetujui' && $userRole != 'Kepala Sekolah') {
             return back()->withErrors(['status' => 'AKSES DITOLAK: Hanya Kepala Sekolah yang berhak memberikan status Disetujui.']);
        }

        // ATURAN 4: JIKA KASUS SUDAH "SELESAI"
        // Kasus yang sudah ditutup TIDAK BOLEH dibuka kembali.
        // (Kecuali mau fitur 'Reopen Case', tapi biasanya butuh prosedur khusus).
        if ($statusLama == 'Selesai') {
             return back()->withErrors(['status' => 'FINAL: Kasus ini sudah ditutup (Selesai). Anda tidak dapat mengubah statusnya lagi.']);
        }

        // ===========================================================

        // 3. Siapkan Data Update
        $dataUpdate = [
            'status' => $statusBaru,
            'denda_deskripsi' => $request->denda_deskripsi,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
        ];

        // Catat siapa yang menyetujui jika statusnya Disetujui
        if ($statusBaru == 'Disetujui') {
            $dataUpdate['penyetuju_user_id'] = Auth::id();
        }

        // 4. Eksekusi Update
        $kasus->update($dataUpdate);


        // 5. Redirect Dinamis
        if ($userRole == 'Kepala Sekolah') {
            return redirect()->route('dashboard.kepsek')->with('success', 'Dokumen berhasil disetujui!');
        } elseif ($userRole == 'Waka Kesiswaan' || $userRole == 'Operator Sekolah') {
            return redirect()->route('dashboard.admin')->with('success', 'Kasus berhasil diperbarui!');
        } else {
            return redirect()->route('dashboard.walikelas')->with('success', 'Kasus berhasil diperbarui!');
        }
    }
    /**
     * Generate PDF Surat Panggilan
     */
    public function cetakSurat($id)
    {
        $kasus = TindakLanjut::with(['siswa.kelas.jurusan', 'suratPanggilan', 'siswa.orangTua', 'siswa.kelas.waliKelas'])
            ->findOrFail($id);

        // 1. CEGAH CETAK JIKA BELUM DI-ACC (Khusus Surat 3 / Kasus Berat)
        if ($kasus->status == 'Menunggu Persetujuan') {
            return back()->with('error', 'DITOLAK: Surat tidak dapat dicetak karena kasus belum disetujui oleh Kepala Sekolah.');
        }

        // 2. CEK DATA SURAT
        if (!$kasus->suratPanggilan) {
            return back()->with('error', 'Draft surat belum tersedia.');
        }

        // 3. OTOMATISASI STATUS: "Baru/Disetujui" -> "Ditangani"
        // Logika: Jika surat dicetak, artinya proses pemanggilan dimulai.
        if ($kasus->status == 'Baru' || $kasus->status == 'Disetujui') {
            $kasus->update([
                'status' => 'Ditangani',
                // Opsional: Catat tanggal mulai ditangani otomatis hari ini
                'tanggal_tindak_lanjut' => now() 
            ]);
        }

        // 2. Siapkan data untuk View PDF
        $data = [
            'kasus' => $kasus,
            'siswa' => $kasus->siswa,
            'surat' => $kasus->suratPanggilan,
            'sekolah' => [
                'nama' => 'SMK NEGERI 1 SIAK LUBUK DALAM',
                'alamat' => 'Jl. Sultan Syarif Qasim, Lubuk Dalam, Siak',
                'telp' => '(0764) 123456', // Ganti sesuai data real
                'kop' => public_path('images/kop_surat.png'), // Opsional jika ada logo
            ]
        ];

        // 3. Pilih Template View berdasarkan Tipe Surat
        // (Logika untuk memilih format surat 1, 2, atau 3)
        $viewName = 'surat.template_umum'; // Kita buat satu template dinamis saja agar efisien
        
        // 4. Generate PDF
        $pdf = Pdf::loadView($viewName, $data);
        
        // Set ukuran kertas F4/Legal atau A4
        $pdf->setPaper('a4', 'portrait');

        // 5. Download / Stream PDF
        // stream() akan membukanya di browser, download() akan langsung unduh
        return $pdf->stream('Surat_Panggilan_' . $kasus->siswa->nisn . '.pdf');
    }
}