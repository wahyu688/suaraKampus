<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = ['Anggrek', 'Syahdan', 'Kijang', 'Alam Sutera', 'Bekasi', 'Senayan'];

        foreach ($campuses as $name) {
            Campus::create(['name' => $name]);
        }
    }
}
