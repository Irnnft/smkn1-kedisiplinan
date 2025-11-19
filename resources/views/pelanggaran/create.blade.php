@extends('layouts.app')

@section('title', 'Catat Pelanggaran')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Formulir Pelanggaran Siswa</h3>
            </div>
            
            <form action="{{ route('pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Pilih Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Cari Nama / Kelas / NISN --</option>
                            @foreach($daftarSiswa as $siswa)
                                <option value="{{ $siswa->id }}">
                                    {{ $siswa->nama_siswa }} — {{ $siswa->kelas->nama_kelas }} (NISN: {{ $siswa->nisn }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Gunakan Ctrl+F atau ketik di dropdown (jika Select2 aktif) untuk mencari.</small>
                    </div>

                    <div class="form-group">
                        <label>Jenis Pelanggaran <span class="text-danger">*</span></label>
                        <select name="jenis_pelanggaran_id" class="form-control" required>
                            <option value="">-- Pilih Pelanggaran --</option>
                            @foreach($daftarPelanggaran as $jp)
                                <option value="{{ $jp->id }}">
                                    [{{ $jp->kategoriPelanggaran->nama_kategori }}] {{ $jp->nama_pelanggaran }} ({{ $jp->poin }} Poin)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Kejadian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kejadian" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Tidak memakai topi saat upacara..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Bukti Foto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="bukti_foto" class="custom-file-input" id="bukti_foto" accept="image/*" required>
                                <label class="custom-file-label" for="bukti_foto">Pilih file foto...</label>
                            </div>
                        </div>
                        <small>Format: JPG/PNG. Max: 2MB.</small>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> SIMPAN DATA PELANGGARAN</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        var fileName = document.getElementById("bukti_foto").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>

@endsection