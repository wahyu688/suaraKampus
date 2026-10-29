<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: laporan butuh kampus, kategori, dan user yang sudah ada
        $this->call([
            CampusSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ReportSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
