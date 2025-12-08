@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/siswa/create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- HEADER -->
    <div class="row mb-3 pt-2">
        <div class="col-sm-6">
            <h4 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-user-plus text-primary mr-2"></i> Tambah Siswa Baru
            </h4>
            <p class="text-muted small mb-0">Pastikan NISN valid dan belum terdaftar sebelumnya.</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary btn-sm border rounded mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Siswa
            </a>
            <a href="{{ route('siswa.bulk-create') }}" class="btn btn-outline-primary btn-sm border rounded">
                <i class="fas fa-copy mr-1"></i> Tambah Banyak
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card card-primary card-outline shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold text-dark">Formulir Data Siswa</h3>
                </div>
                
                <form action="{{ route('siswa.store') }}" method="POST">
                    @csrf
                    <div class="card-body bg-light">
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">NISN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-id-card text-secondary"></i></span>
                                        </div>
                                        <input type="text" name="nisn" class="form-control form-control-clean @error('nisn') is-invalid @enderror" 
                                               placeholder="Nomor Induk Siswa Nasional (minimal 8 digit)" value="{{ old('nisn') }}" required
                                               pattern="[0-9]{8,}" title="NISN harus numeric minimal 8 digit">
                                    </div>
                                    @error('nisn') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white"><i class="fas fa-user text-secondary"></i></span>
                                        </div>
                                        <input type="text" name="nama_siswa" class="form-control form-control-clean @error('nama_siswa') is-invalid @enderror" 
                                               placeholder="Sesuai Ijazah/Rapor" value="{{ old('nama_siswa') }}" required>
                                    </div>
                                    @error('nama_siswa') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-muted">Kelas <span class="text-danger">*</span></label>
                                    <select name="kelas_id" class="form-control form-control-clean @error('kelas_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-primary">Nomor HP Wali Murid (WA)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-primary text-success"><i class="fab fa-whatsapp"></i></span>
                                        </div>
                                             <input type="text" name="nomor_hp_wali_murid" class="form-control form-control-clean border-primary" 
                                                 placeholder="Contoh: 081234567890" value="{{ old('nomor_hp_wali_murid') }}">
                                    </div>
                                    <small class="text-muted font-italic">Wajib diisi untuk fitur notifikasi otomatis.</small>
                                    @error('nomor_hp_wali_murid') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label class="form-label text-muted">Akun Login Wali Murid (Opsional)</label>

                            <select name="wali_murid_user_id" class="form-control form-control-clean @error('wali_murid_user_id') is-invalid @enderror">
                                <option value="">-- Cari Nama Wali Murid --</option>
                                @foreach($waliMurid as $wali)
                                    <option value="{{ $wali->id }}" {{ old('wali_murid_user_id') == $wali->id ? 'selected' : '' }}>
                                        {{ $wali->nama }} ({{ $wali->username }})
                                    </option>
                                @endforeach
                            </select>
                            
                            <small class="text-muted font-italic">
                                <i class="fas fa-info-circle"></i> Pilih jika akun Wali Murid sudah dibuat sebelumnya di menu Manajemen User.
                            </small>
                            @error('wali_murid_user_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mt-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="create_wali" name="create_wali" value="1" {{ old('create_wali') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="create_wali">Buat akun Wali Murid otomatis jika belum ada</label>
                            </div>

                            <small class="text-muted d-block mt-1">Jika dicentang dan Anda tidak memilih akun wali yang sudah ada, sistem akan membuat akun baru untuk wali dari siswa ini.</small>

                            <div id="wali-preview" class="mt-2 p-2 bg-white border rounded d-none">
                                <strong>Preview akun yang akan dibuat:</strong>
                                <div class="mt-2">
                                    <div><strong>Username:</strong> <span id="wali-preview-username">-</span></div>
                                    <div><strong>Password:</strong> <em>Akan ditampilkan setelah disimpan</em></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white d-flex justify-content-end py-3">
                        <a href="{{ route('siswa.index') }}" class="btn btn-default mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/siswa/create.js') }}"></script>
@endpush