<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

// Data tambahan untuk memperlihatkan pagination saat demo.
// Tidak dipanggil DatabaseSeeder; jalankan manual:
//   php artisan db:seed --class=DemoPaginationSeeder
class DemoPaginationSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();

        // ->for($student): semua laporan palsu ini milik mahasiswa demo
        Report::factory()->count(20)->for($student)->create();
        Report::factory()->count(5)->resolved()->for($student)->create();
    }
}
