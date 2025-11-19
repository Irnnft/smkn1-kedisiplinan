<?php

namespace App\Http\Controllers;

use App\Models\JenisPelanggaran;
use App\Models\KategoriPelanggaran;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisPelanggaran::with('kategoriPelanggaran');

        if ($request->has('cari')) {
            $query->where('nama_pelanggaran', 'like', '%' . $request->cari . '%');
        }

        $jenisPelanggaran = $query->orderBy('poin', 'asc')->paginate(10);

        return view('jenis_pelanggaran.index', compact('jenisPelanggaran'));
    }

    public function create()
    {
        // Kita butuh data kategori (Ringan/Sedang/Berat) untuk dropdown
        $kategori = KategoriPelanggaran::all();
        return view('jenis_pelanggaran.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_pelanggaran,id',
            'poin' => 'required|integer|min:0',
        ]);

        JenisPelanggaran::create($request->all());

        return redirect()->route('jenis-pelanggaran.index')
            ->with('success', 'Aturan pelanggaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jenisPelanggaran = JenisPelanggaran::findOrFail($id);
        $kategori = KategoriPelanggaran::all();
        return view('jenis_pelanggaran.edit', compact('jenisPelanggaran', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'kategori_id' => 'required',
            'poin' => 'required|integer|min:0',
        ]);

        $jenisPelanggaran = JenisPelanggaran::findOrFail($id);
        $jenisPelanggaran->update($request->all());

        return redirect()->route('jenis-pelanggaran.index')
            ->with('success', 'Aturan pelanggaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jenisPelanggaran = JenisPelanggaran::findOrFail($id);
        
        // Cek apakah pelanggaran ini sudah pernah dipakai di riwayat
        if ($jenisPelanggaran->riwayatPelanggaran()->exists()) {
            return back()->with('error', 'Gagal hapus! Pelanggaran ini sudah tercatat di riwayat siswa. (Hanya boleh diedit)');
        }

        $jenisPelanggaran->delete();
        return redirect()->route('jenis-pelanggaran.index')->with('success', 'Aturan berhasil dihapus.');
    }
}