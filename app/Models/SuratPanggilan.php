<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPanggilan extends Model
{
    use HasFactory;

    /**
     * Nama tabelnya adalah 'surat_panggilan'.
     */
    protected $table = 'surat_panggilan';

    /**
     * Kita memiliki timestamps 'created_at' dan 'updated_at' di tabel ini.
     * (Default, tidak perlu ditulis)
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tindak_lanjut_id',
        'nomor_surat',
        'tipe_surat',
        'tanggal_surat',
        'file_path_pdf',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    // =====================================================================
    // ----------------- DEFINISI RELASI ELOQUENT ------------------
    // =====================================================================

    /**
     * Relasi Wajib: SATU SuratPanggilan DIMILIKI OLEH SATU TindakLanjut.
     * (Foreign Key: tindak_lanjut_id)
     */
    public function tindakLanjut(): BelongsTo
    {
        return $this->belongsTo(TindakLanjut::class, 'tindak_lanjut_id');
    }
}