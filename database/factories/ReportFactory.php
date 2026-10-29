<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Pabrik data laporan palsu. Contoh: Report::factory()->count(25)->create().
 *
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    // Judul dibuat dari potongan kalimat supaya terlihat seperti laporan asli (bahasa Indonesia)
    private const PROBLEMS = [
        'Kursi kelas patah',
        'Proyektor tidak menyala',
        'Wi-Fi sangat lambat',
        'Lift macet',
        'Keran wastafel bocor',
        'Lampu tangga mati',
        'Sampah menumpuk',
        'AC bocor dan menetes',
        'Pintu toilet tidak bisa dikunci',
        'Stopkontak tidak berfungsi',
    ];

    private const PLACES = ['ruang kelas', 'perpustakaan', 'kantin', 'toilet', 'lab komputer', 'koridor', 'lobi'];

    public function definition(): array
    {
        $floor = fake()->numberBetween(1, 8);
        $createdAt = fake()->dateTimeBetween('-60 days', 'now');

        return [
            // Pemilik laporan: user baru dari UserFactory, kecuali ditentukan lewat ->for($user)
            'user_id' => User::factory(),
            // Hanya kategori non-sensitif, supaya data palsu tidak berisi cerita perundungan
            'category_id' => fn () => Category::where('is_sensitive', false)->inRandomOrder()->value('id'),
            'campus_id' => fn () => Campus::inRandomOrder()->value('id'),
            'title' => fake()->randomElement(self::PROBLEMS) . ' di ' . fake()->randomElement(self::PLACES) . ' lantai ' . $floor,
            'location_detail' => 'Lantai ' . $floor . ', dekat R. ' . $floor . fake()->numberBetween(10, 30),
            'incident_date' => $createdAt->format('Y-m-d'),
            'description' => 'Masalah ini sudah terjadi beberapa hari dan cukup mengganggu kegiatan belajar. Mohon segera dicek oleh petugas.',
            'is_anonymous' => false,
            'status' => fake()->randomElement(['pending', 'in_progress']),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            // "code" tidak diisi di sini: dibuat otomatis oleh event creating di model Report
        ];
    }

    // State: laporan yang sudah selesai. Contoh: Report::factory()->resolved()->create()
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    // Setiap laporan buatan factory juga dapat riwayat pertama, sama seperti laporan asli
    public function configure(): static
    {
        return $this->afterCreating(function (Report $report) {
            $report->updates()->create([
                'status' => 'pending',
                'title' => 'Laporan terkirim',
                'note' => 'Laporan diterima dan menunggu verifikasi admin.',
            ]);
        });
    }
}
