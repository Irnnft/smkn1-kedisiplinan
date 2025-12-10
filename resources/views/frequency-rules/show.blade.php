@extends('layouts.app')

@section('title', 'Detail Frequency Rules - ' . $jenisPelanggaran->nama_pelanggaran)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('frequency-rules.index') }}" class="btn btn-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h4><i class="fas fa-sliders-h mr-2"></i> Frequency Rules: {{ $jenisPelanggaran->nama_pelanggaran }}</h4>
            <p class="text-muted">
                @php
                    $kategoriNama = $jenisPelanggaran->kategoriPelanggaran->nama_kategori ?? 'Unknown';
                    $badgeClass = $kategoriNama == 'Ringan' ? 'info' : ($kategoriNama == 'Sedang' ? 'warning' : 'danger');
                @endphp
                Kategori: <span class="badge badge-{{ $badgeClass }}">{{ $kategoriNama }}</span>
                | Poin Default: <span class="badge badge-secondary">{{ $jenisPelanggaran->poin }} poin</span>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Info & Actions -->
    <div class="row mb-3">
        <div class="col-12">
            @if($jenisPelanggaran->frequencyRules->count() == 0)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Belum ada frequency rules untuk pelanggaran ini.</strong>
                    <br>Tambahkan rule pertama untuk mulai menggunakan sistem frequency-based point.
                    <br><small class="text-muted">Jika tidak ada frequency rules, sistem akan menggunakan poin default ({{ $jenisPelanggaran->poin }} poin) setiap kali pelanggaran tercatat.</small>
                </div>
            @endif
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddRule">
                <i class="fas fa-plus"></i> Tambah Rule
            </button>
        </div>
    </div>

    <!-- Table Rules -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Order</th>
                        <th width="12%">Frekuensi</th>
                        <th width="8%">Poin</th>
                        <th width="35%">Sanksi / Keterangan</th>
                        <th width="10%" class="text-center">Surat</th>
                        <th width="18%">Pembina</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisPelanggaran->frequencyRules as $rule)
                    <tr>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $rule->display_order }}</span>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-lg">
                                @if($rule->frequency_max)
                                    {{ $rule->frequency_min }}-{{ $rule->frequency_max }}x
                                @else
                                    {{ $rule->frequency_min }}+x
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($rule->poin > 0)
                                <span class="badge badge-danger badge-lg">+{{ $rule->poin }}</span>
                            @else
                                <span class="badge badge-secondary">0</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark">{{ $rule->sanksi_description }}</strong>
                        </td>
                        <td class="text-center">
                            @if($rule->trigger_surat)
                                <span class="badge badge-warning"><i class="fas fa-envelope"></i> Ya</span>
                            @else
                                <span class="badge badge-secondary">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @foreach($rule->pembina_roles as $role)
                                @if($role == 'Semua Guru & Staff')
                                    <span class="badge badge-primary mb-1"><i class="fas fa-users"></i> {{ $role }}</span>
                                @else
                                    <span class="badge badge-info mb-1">{{ $role }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning btn-edit-rule" 
                                    data-id="{{ $rule->id }}"
                                    data-frequency-min="{{ $rule->frequency_min }}"
                                    data-frequency-max="{{ $rule->frequency_max }}"
                                    data-poin="{{ $rule->poin }}"
                                    data-sanksi="{{ $rule->sanksi_description }}"
                                    data-trigger-surat="{{ $rule->trigger_surat }}"
                                    data-pembina-roles="{{ json_encode($rule->pembina_roles) }}"
                                    data-display-order="{{ $rule->display_order }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('frequency-rules.destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus rule ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada frequency rules. Klik "Tambah Rule" untuk menambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Rule -->
<div class="modal fade" id="modalAddRule" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('frequency-rules.store', $jenisPelanggaran->id) }}" method="POST" id="formAddRule">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Frequency Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @php
                        // Calculate smart defaults
                        $existingRules = $jenisPelanggaran->frequencyRules;
                        $suggestedFreqMin = 1;
                        $suggestedDisplayOrder = 1;
                        
                        if ($existingRules->isNotEmpty()) {
                            $highestMax = $existingRules->max('frequency_max');
                            if ($highestMax !== null) {
                                $suggestedFreqMin = $highestMax + 1;
                            } else {
                                // Open-ended rule exists, suggest after highest min
                                $highestMin = $existingRules->max('frequency_min');
                                $suggestedFreqMin = $highestMin + 1;
                            }
                            $suggestedDisplayOrder = $existingRules->max('display_order') + 1;
                        }
                    @endphp
                    
                    <div class="alert alert-info alert-sm">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Tentang Frekuensi:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Range (Min-Max):</strong> Rule trigger untuk beberapa frekuensi. Contoh: min=1, max=3 → trigger di 1x, 2x, OR 3x</li>
                            <li><strong>Exact (Min=Max):</strong> Rule trigger pada frekuensi spesifik. Contoh: min=3, max=3 → trigger ONLY di 3x</li>
                            <li><strong>Open-ended (Max kosong):</strong> Rule trigger dari frekuensi min ke atas. Contoh: min=7, max=(kosong) → trigger di 7x, 8x, 9x, ...</li>
                        </ul>
                        <div class="mt-2 p-2 bg-light rounded">
                            <strong>💡 Tip:</strong> Untuk sanksi progresif, gunakan range. Untuk sanksi specific, set min=max.
                        </div>
                    </div>
                    
                    <!-- Exact Frequency Helper -->
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="exactFrequencyMode">
                            <label class="custom-control-label" for="exactFrequencyMode">
                                <strong>Mode Frekuensi Exact</strong>
                                <br><small class="text-muted">Aktifkan untuk rule yang trigger pada frekuensi spesifik (min=max)</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frekuensi Min <span class="text-danger">*</span></label>
                                <input type="number" name="frequency_min" id="add_frequency_min" class="form-control" value="{{ old('frequency_min', $suggestedFreqMin) }}" min="1" required>
                                <small class="form-text text-success">
                                    <i class="fas fa-lightbulb"></i> Rekomendasi: {{ $suggestedFreqMin }} (dari rule yang ada)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frekuensi Max <small class="text-muted">(opsional)</small></label>
                                <input type="number" name="frequency_max" id="add_frequency_max" class="form-control" min="1" placeholder="Kosongkan untuk unlimited">
                                <small class="form-text text-muted" id="maxHelpText">Contoh: 1-3x, 4-10x, 11+x</small>
                                <small class="form-text text-info" id="exactHelpText" style="display:none;">
                                    <i class="fas fa-info-circle"></i> Mode exact: Max akan sama dengan Min
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Poin <span class="text-danger">*</span></label>
                        <input type="number" name="poin" class="form-control" required min="0" value="0">
                        <small class="text-muted">Isi 0 jika hanya pembinaan tanpa poin</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Sanksi / Keterangan <span class="text-danger">*</span></label>
                        <textarea name="sanksi_description" class="form-control" rows="3" required placeholder="Contoh: Dipotong rambutnya di sekolah, Pembinaan oleh wali kelas, dll"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="trigger_surat" value="1" class="custom-control-input" id="trigger_surat_add">
                            <label class="custom-control-label" for="trigger_surat_add">
                                <strong>Trigger Surat Pemanggilan Orang Tua</strong>
                                <br><small class="text-muted">Centang jika sanksi ini memerlukan pemanggilan orang tua</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Pembina yang Terlibat <span class="text-danger">*</span></label>
                        <div class="border rounded p-3">
                            <div class="custom-control custom-checkbox mb-2 border-bottom pb-2">
                                <input type="checkbox" name="pembina_roles[]" value="Semua Guru & Staff" class="custom-control-input" id="pembina_semua_add">
                                <label class="custom-control-label font-weight-bold text-primary" for="pembina_semua_add">
                                    <i class="fas fa-users"></i> Semua Guru & Staff
                                    <br><small class="text-muted font-weight-normal">Untuk pelanggaran yang bisa ditindaklanjuti oleh siapa saja yang melihat (contoh: atribut, kerapian)</small>
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Wali Kelas" class="custom-control-input" id="pembina_wali_add">
                                <label class="custom-control-label" for="pembina_wali_add">Wali Kelas</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Kaprodi" class="custom-control-input" id="pembina_kaprodi_add">
                                <label class="custom-control-label" for="pembina_kaprodi_add">Kaprodi</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Waka Kesiswaan" class="custom-control-input" id="pembina_waka_add">
                                <label class="custom-control-label" for="pembina_waka_add">Waka Kesiswaan</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Waka Sarana" class="custom-control-input" id="pembina_sarana_add">
                                <label class="custom-control-label" for="pembina_sarana_add">Waka Sarana</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Kepala Sekolah" class="custom-control-input" id="pembina_kepsek_add">
                                <label class="custom-control-label" for="pembina_kepsek_add">Kepala Sekolah</label>
                            </div>
                        </div>
                        <small class="text-muted">Pilih satu atau lebih pembina yang akan menangani sanksi ini</small>
                    </div>
                    
                    <!-- Display Order -->
                    <div class="form-group">
                        <label>Urutan Tampilan</label>
                        <input type="number" name="display_order" class="form-control" value="{{ $suggestedDisplayOrder }}" min="1">
                        <small class="form-text text-success">
                            <i class="fas fa-lightbulb"></i> Rekomendasi: {{ $suggestedDisplayOrder }}
                        </small>
                    </div>
                    
                    <!-- Examples -->
                    <div class="alert alert-secondary">
                        <strong><i class="fas fa-examples"></i> Contoh Penggunaan:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Progressive (range):</strong> Frek 1-3 → Teguran (10 poin), Frek 4-6 → Surat (30 poin), Frek 7+ → Panggil ortu (80 poin)</li>
                            <li><strong>Exact trigger:</strong> Frek 3 (min=3, max=3) → Skorsing 1 hari</li>
                            <li><strong>Single action:</strong> Frek 1 (min=1, max=1) → Langsung potong rambut (untuk rambut panjang)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Rule -->
<div class="modal fade" id="modalEditRule" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditRule" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Frequency Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frekuensi Min</label>
                                <input type="number" name="frequency_min" id="edit_frequency_min" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frekuensi Max <small class="text-muted">(opsional)</small></label>
                                <input type="number" name="frequency_max" id="edit_frequency_max" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Poin <span class="text-danger">*</span></label>
                        <input type="number" name="poin" id="edit_poin" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Sanksi / Keterangan <span class="text-danger">*</span></label>
                        <textarea name="sanksi_description" id="edit_sanksi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="trigger_surat" value="1" class="custom-control-input" id="edit_trigger_surat">
                            <label class="custom-control-label" for="edit_trigger_surat">
                                <strong>Trigger Surat Pemanggilan Orang Tua</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pembina yang Terlibat <span class="text-danger">*</span></label>
                        <div class="border rounded p-3">
                            <div class="custom-control custom-checkbox mb-2 border-bottom pb-2">
                                <input type="checkbox" name="pembina_roles[]" value="Semua Guru & Staff" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_semua">
                                <label class="custom-control-label font-weight-bold text-primary" for="edit_pembina_semua">
                                    <i class="fas fa-users"></i> Semua Guru & Staff
                                    <br><small class="text-muted font-weight-normal">Untuk pelanggaran yang bisa ditindaklanjuti oleh siapa saja yang melihat</small>
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Wali Kelas" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_wali">
                                <label class="custom-control-label" for="edit_pembina_wali">Wali Kelas</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Kaprodi" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_kaprodi">
                                <label class="custom-control-label" for="edit_pembina_kaprodi">Kaprodi</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Waka Kesiswaan" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_waka">
                                <label class="custom-control-label" for="edit_pembina_waka">Waka Kesiswaan</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Waka Sarana" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_sarana">
                                <label class="custom-control-label" for="edit_pembina_sarana">Waka Sarana</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="pembina_roles[]" value="Kepala Sekolah" class="custom-control-input pembina-checkbox-edit" id="edit_pembina_kepsek">
                                <label class="custom-control-label" for="edit_pembina_kepsek">Kepala Sekolah</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Exact Frequency Mode Toggle
    $('#exactFrequencyMode').change(function() {
        const isExact = $(this).is(':checked');
        const minInput = $('#add_frequency_min');
        const maxInput = $('#add_frequency_max');
        
        if (isExact) {
            // Exact mode: max = min
            maxInput.prop('readonly', true);
            maxInput.addClass('bg-light');
            $('#maxHelpText').hide();
            $('#exactHelpText').show();
            
            // Sync max with min
            maxInput.val(minInput.val());
        } else {
            // Range mode: max is editable
            maxInput.prop('readonly', false);
            maxInput.removeClass('bg-light');
            $('#maxHelpText').show();
            $('#exactHelpText').hide();
        }
    });
    
    //When min changes in exact mode, update max
    $('#add_frequency_min').on('input', function() {
        if ($('#exactFrequencyMode').is(':checked')) {
            $('#add_frequency_max').val($(this).val());
        }
    });
    
    // Reset form when modal closes
    $('#modalAddRule').on('hidden.bs.modal', function() {
        $('#formAddRule')[0].reset();
        $('#exactFrequencyMode').prop('checked', false).trigger('change');
        // Reset to suggested defaults
        $('#add_frequency_min').val('{{ $suggestedFreqMin ?? 1 }}');
    });
    
    // Edit rule
    $('.btn-edit-rule').click(function() {
        const id = $(this).data('id');
        const freqMin = $(this).data('frequency-min');
        const freqMax = $(this).data('frequency-max');
        const poin = $(this).data('poin');
        const sanksi = $(this).data('sanksi');
        const triggerSurat = $(this).data('trigger-surat');
        const pembinaRoles = $(this).data('pembina-roles');
        
        $('#formEditRule').attr('action', `/frequency-rules/rule/${id}`);
        $('#edit_frequency_min').val(freqMin);
        $('#edit_frequency_max').val(freqMax || '');
        $('#edit_poin').val(poin);
        $('#edit_sanksi').val(sanksi);
        $('#edit_trigger_surat').prop('checked', triggerSurat == 1);
        
        // Uncheck all pembina checkboxes first
        $('.pembina-checkbox-edit').prop('checked', false);
        
        // Check the selected pembina roles
        pembinaRoles.forEach(role => {
            $(`.pembina-checkbox-edit[value="${role}"]`).prop('checked', true);
        });
        
        $('#modalEditRule').modal('show');
    });
});
</script>
@endpush
@endsection
