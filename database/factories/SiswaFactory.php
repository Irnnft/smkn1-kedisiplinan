<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Generate NISN acak 10 digit
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nama_siswa' => $this->faker->name(), // Nama Indonesia acak
            'nomor_hp_ortu' => $this->faker->phoneNumber(),
            // kelas_id dan orang_tua_user_id akan kita isi manual saat dipanggil di Seeder
        ];
    }
}