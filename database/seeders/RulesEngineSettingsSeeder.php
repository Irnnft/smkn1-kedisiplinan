<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RulesEngineSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $settings = [
            [
                'key' => 'surat_2_min_poin',
                'value' => '100',
                'label' => 'Surat 2 - Poin Minimum',
                'description' => 'Poin minimum untuk memicu Surat 2 (Pelanggaran Berat)',
                'category' => 'threshold_poin',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:1000',
                'display_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'surat_2_max_poin',
                'value' => '500',
                'label' => 'Surat 2 - Poin Maximum',
                'description' => 'Poin maksimum untuk Surat 2 (di atas ini akan menjadi Surat 3)',
                'category' => 'threshold_poin',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:1000',
                'display_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'surat_3_min_poin',
                'value' => '501',
                'label' => 'Surat 3 - Poin Minimum',
                'description' => 'Poin minimum untuk memicu Surat 3 (Sangat Berat)',
                'category' => 'threshold_poin',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:10000',
                'display_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'akumulasi_sedang_min',
                'value' => '55',
                'label' => 'Akumulasi Sedang - Minimum',
                'description' => 'Total poin akumulasi minimum untuk eskalasi ke Surat 2',
                'category' => 'threshold_akumulasi',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:1000',
                'display_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'akumulasi_sedang_max',
                'value' => '300',
                'label' => 'Akumulasi Sedang - Maximum',
                'description' => 'Total poin akumulasi maksimum untuk Surat 2 (di atas ini menjadi kritis)',
                'category' => 'threshold_akumulasi',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:1000',
                'display_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'akumulasi_kritis',
                'value' => '301',
                'label' => 'Akumulasi Kritis',
                'description' => 'Total poin akumulasi untuk memicu Surat 3 (Akumulasi Kritis)',
                'category' => 'threshold_akumulasi',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:10000',
                'display_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'frekuensi_atribut',
                'value' => '10',
                'label' => 'Frekuensi Pelanggaran Atribut',
                'description' => 'Jumlah pelanggaran atribut yang memicu Surat 1',
                'category' => 'threshold_frekuensi',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:100',
                'display_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'frekuensi_alfa',
                'value' => '4',
                'label' => 'Frekuensi Pelanggaran Alfa',
                'description' => 'Jumlah pelanggaran alfa yang memicu Surat 1',
                'category' => 'threshold_frekuensi',
                'data_type' => 'integer',
                'validation_rules' => 'required|integer|min:1|max:100',
                'display_order' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('rules_engine_settings')->insert($settings);
    }
}
