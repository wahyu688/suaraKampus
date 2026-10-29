<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kebersihan & Sampah',
                'slug' => 'kebersihan-sampah',
                'description' => 'Sampah menumpuk, toilet kotor, atau area kampus yang tidak terawat.',
                'is_sensitive' => false,
                'icon' => 'bi-trash3',
            ],
            [
                'name' => 'Fasilitas Rusak',
                'slug' => 'fasilitas-rusak',
                'description' => 'AC mati, proyektor bermasalah, kursi patah, atau lift yang macet.',
                'is_sensitive' => false,
                'icon' => 'bi-lightbulb',
            ],
            [
                'name' => 'Keamanan Kampus',
                'slug' => 'keamanan-kampus',
                'description' => 'Area gelap, barang hilang, atau orang mencurigakan di sekitar kampus.',
                'is_sensitive' => false,
                'icon' => 'bi-lock',
            ],
            [
                'name' => 'Perundungan (Bullying)',
                'slug' => 'perundungan',
                'description' => 'Dirundung secara langsung maupun online. Ditangani langsung oleh tim konselor.',
                'is_sensitive' => true,
                'icon' => 'bi-exclamation-triangle',
            ],
            [
                'name' => 'Konflik Antar Mahasiswa',
                'slug' => 'konflik-mahasiswa',
                'description' => 'Masalah di kelompok tugas, organisasi, atau hubungan dengan sesama mahasiswa.',
                'is_sensitive' => true,
                'icon' => 'bi-people',
            ],
            [
                'name' => 'Curhat Akademik',
                'slug' => 'curhat-akademik',
                'description' => 'Tekanan kuliah, kesulitan belajar, atau hal lain yang ingin kamu ceritakan.',
                'is_sensitive' => true,
                'icon' => 'bi-chat-left-text',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
