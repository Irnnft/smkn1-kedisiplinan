<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kasus - {{ $kasus->siswa->nama_siswa }}</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        h1 { margin: 0; color: #333; font-size: 1.5rem; }
        
        .info-box { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { width: 150px; font-weight: bold; color: #555; }
        .info-val { font-weight: bold; color: #000; }
        .text-danger { color: #c62828; }

        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        select, textarea, input[type="date"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; box-sizing: border-box; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; text-decoration: none; display: inline-block; }
        .btn-print { background-color: #17a2b8; color: white; text-decoration: none; display: inline-block; margin-right: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Kelola Kasus: {{ $kasus->siswa->nama_siswa }}</h1>
        <small>Kelas: {{ $kasus->siswa->kelas->nama_kelas }} | NISN: {{ $kasus->siswa->nisn }}</small>
    </div>

    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Pemicu Kasus:</div>
            <div class="info-val">{{ $kasus->pemicu }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sanksi Sistem:</div>
            <div class="info-val text-danger">{{ $kasus->sanksi_deskripsi }}</div>
        </div>
        @if($kasus->suratPanggilan)
        <div class="info-row">
            <div class="info-label">Rekomendasi:</div>
            <div class="info-val">
                Cetak <strong>{{ $kasus->suratPanggilan->tipe_surat }}</strong>
            </div>
        </div>
        @endif
    </div>

    <div style="margin-bottom: 25px;">
        @if($kasus->suratPanggilan)
            
            @if($kasus->status == 'Selesai')
                <div style="display: inline-block; background: #eee; padding: 8px 15px; border-radius: 4px; color: #777; font-size: 0.9rem;">
                    📂 <strong>Arsip Surat:</strong> <a href="{{ route('kasus.cetak', $kasus->id) }}" target="_blank" style="color: #555;">Download Copy Surat</a>
                </div>
            
            @elseif($kasus->status == 'Menunggu Persetujuan')
                <button class="btn btn-secondary" disabled style="cursor: not-allowed; opacity: 0.6;">
                    ⏳ Menunggu Persetujuan Kepsek (Belum Bisa Cetak)
                </button>
            
            @else
                <a href="{{ route('kasus.cetak', $kasus->id) }}" target="_blank" class="btn btn-print">
                    🖨️ Cetak {{ $kasus->suratPanggilan->tipe_surat }}
                </a>
                <p class="text-muted" style="font-size: 0.85rem; margin-top: 5px;">
                    *Mencetak surat akan otomatis mengubah status kasus menjadi <strong>"Sedang Ditangani"</strong>.
                </p>
            @endif

        @endif
    </div>

    <hr>

    <form action="{{ route('kasus.update', $kasus->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h3>Hasil Penanganan / Tindak Lanjut</h3>

        @if($kasus->status == 'Selesai')
            
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
                ✅ <strong>KASUS DITUTUP</strong><br>
                Kasus ini dinyatakan selesai pada tanggal: <strong>{{ \Carbon\Carbon::parse($kasus->tanggal_tindak_lanjut)->format('d F Y') }}</strong>.
                Data tidak dapat diubah lagi.
            </div>

            <label>Tanggal Penanganan:</label>
            <input type="text" value="{{ $kasus->tanggal_tindak_lanjut }}" disabled style="background: #eee;">

            <label>Denda / Catatan:</label>
            <textarea rows="3" disabled style="background: #eee;">{{ $kasus->denda_deskripsi }}</textarea>
            
            <label>Status:</label>
            <input type="text" value="Selesai" disabled style="background: #eee; font-weight: bold; color: green;">

            <br><br>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>

        @else
            <label for="tanggal_tindak_lanjut">Tanggal Penanganan:</label>
            <input type="date" name="tanggal_tindak_lanjut" value="{{ $kasus->tanggal_tindak_lanjut ? \Carbon\Carbon::parse($kasus->tanggal_tindak_lanjut)->format('Y-m-d') : date('Y-m-d') }}" required>
    
            <label for="denda_deskripsi">Denda / Catatan Tambahan (Opsional):</label>
            <textarea name="denda_deskripsi" rows="3" placeholder="Contoh: Siswa diminta membawa 2 pot bunga.">{{ $kasus->denda_deskripsi }}</textarea>
    
            @if(Auth::user()->role->nama_role == 'Kepala Sekolah')
                <div style="background: #e8f5e9; padding: 15px; border: 1px solid #c8e6c9; border-radius: 5px; margin-bottom: 20px;">
                    <p style="margin-top: 0; font-weight: bold; color: #2e7d32;">Area Persetujuan Kepala Sekolah</p>
                    <label>
                        <input type="checkbox" name="status" value="Disetujui" required> 
                        Saya telah meninjau kasus ini dan menyetujui sanksi yang diberikan.
                    </label>
                </div>
            @else
                <label for="status">Status Kasus:</label>
                
                @if($kasus->status == 'Menunggu Persetujuan')
                    <div class="alert alert-warning" style="background: #fff3cd; padding: 10px; border: 1px solid #ffeeba; color: #856404; border-radius: 4px;">
                        🔒 <strong>Terkunci:</strong> Menunggu persetujuan Kepala Sekolah.
                    </div>
                    <input type="hidden" name="status" value="Menunggu Persetujuan">
                @else
                    <select name="status" required>
                        @if($kasus->status == 'Baru')
                            <option value="Baru" selected>Baru (Belum Selesai)</option>
                        @endif
                        
                        @if($kasus->status == 'Disetujui' || $kasus->status == 'Ditangani')
                            <option value="Disetujui" disabled>✅ Sudah Disetujui (Terkunci)</option>
                            @if($kasus->status == 'Disetujui') <option value="Disetujui" hidden selected>Disetujui</option> @endif
                        @endif
                        
                        <option value="Ditangani" {{ $kasus->status == 'Ditangani' ? 'selected' : '' }}>Sedang Ditangani</option>
                        <option value="Selesai">Selesai (Kasus Ditutup)</option>
                    </select>
                @endif
            @endif
    
            <br><br>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>

        @endif
    </form>
</div>

</body>
</html>