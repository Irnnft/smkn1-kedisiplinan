<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TindakLanjut extends Model
{
    use HasFactory;

    /**
     * Nama tabelnya adalah 'tindak_lanjut'.
     */
    protected $table = 'tindak_lanjut';

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
        'siswa_id',
        'pemicu',
        'sanksi_deskripsi',
        'denda_deskripsi',
        'status',
        'tanggal_tindak_lanjut',
        'penyetuju_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_tindak_lanjut' => 'date',
        // 'status' akan otomatis dicast oleh Laravel 11+ jika 
        // Anda menggunakan Enum di migrasi, tapi ini cara eksplisitnya
        // jika masih string.
    ];

    // =====================================================================
    // ----------------- DEFINISI RELASI ELOQUENT ------------------
    // =====================================================================

    /**
     * Relasi Wajib: SATU Kasus TindakLanjut DIMILIKI OLEH SATU Siswa.
     * (Foreign Key: siswa_id)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi Opsional: SATU Kasus TindakLanjut DISETUJUI OLEH SATU User.
     * (Foreign Key: penyetuju_user_id)
     */
    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyetuju_user_id');
    }

    /**
     * Relasi Opsional: SATU Kasus TindakLanjut MEMILIKI SATU SuratPanggilan.
     * (Foreign Key di tabel 'surat_panggilan': tindak_lanjut_id)
     */
    public function suratPanggilan(): HasOne
    {
        return $this->hasOne(SuratPanggilan::class, 'tindak_lanjut_id');
    }
}