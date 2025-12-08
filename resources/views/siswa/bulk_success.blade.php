@extends('layouts.app')

@section('title', 'Proses Bulk Create Berhasil')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg mt-5">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                    </div>
                    
                    <h2 class="card-title text-success font-weight-bold mb-3">Proses Bulk Create Berhasil!</h2>
                    
                    <div class="alert alert-light border-left-success mb-4" style="border-left: 4px solid #28a745;">
                        <p class="mb-2"><strong>Ringkasan:</strong></p>
                        <ul class="mb-0 text-left">
                            <li><strong>{{ $totalCreated }}</strong> siswa baru telah ditambahkan ke sistem.</li>
                            @if($totalWaliCreated > 0)
                                <li><strong>{{ $totalWaliCreated }}</strong> akun Wali Murid otomatis telah dibuat.</li>
                                <li>File Excel kredensial (<code>bulk_wali_credentials_*.xlsx.csv</code>) otomatis diunduh ke device Anda.</li>
                                <li><strong>Format file:</strong> NISN | Username | Password (tab-separated, siap buka di Excel).</li>
                            @else
                                <li>Tidak ada akun Wali Murid yang dibuat (opsi tidak dicentang).</li>
                            @endif
                        </ul>
                    </div>

                    <div class="alert alert-info mb-4">
                        <strong><i class="fas fa-info-circle mr-2"></i> Catatan Penting:</strong>
                        <ul class="mb-0 text-left small">
                            <li>Bagikan kredensial Wali Murid dengan aman ke pihak yang bersangkutan.</li>
                            <li>Sarankan kepada Wali Murid untuk mengubah password setelah login pertama.</li>
                            <li>Simpan file Excel kredensial di tempat yang aman.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('siswa.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-list mr-2"></i> Kembali ke Daftar Siswa
                        </a>
                        <a href="{{ route('siswa.bulk-create') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-plus mr-2"></i> Tambah Batch Lagi
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow mt-3 mb-5">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-file-excel mr-2 text-success"></i> Detail Kredensial Wali Murid</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Username</th>
                                    <th>Password (Sampel)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($waliCreated as $idx => $wali)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><code>{{ $wali['nisn'] }}</code></td>
                                        <td><code>{{ $wali['username'] }}</code></td>
                                        <td><code style="color: #999;">{{ substr($wali['password'], 0, 3) }}****</code></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted text-center py-3">Tidak ada kredensial wali yang dibuat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-trigger download jika file tersedia di session
        @if($autoDownloadFile)
        setTimeout(function () {
            const link = document.createElement('a');
            link.href = '{{ $autoDownloadFile }}';
            link.download = true;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 500);
        @endif
    });
</script>
@endpush
