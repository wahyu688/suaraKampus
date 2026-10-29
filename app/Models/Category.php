<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Pilihan ikon (Bootstrap Icons) di form kategori: kelas ikon => label
    public const ICONS = [
        'bi-trash3' => 'Tempat sampah',
        'bi-lightbulb' => 'Lampu',
        'bi-lock' => 'Gembok',
        'bi-exclamation-triangle' => 'Peringatan',
        'bi-people' => 'Orang',
        'bi-chat-left-text' => 'Percakapan',
        'bi-wifi' => 'Wi-Fi',
        'bi-droplet' => 'Air',
        'bi-tools' => 'Perkakas',
        'bi-heart-pulse' => 'Kesehatan',
    ];

    protected $fillable = ['name', 'slug', 'description', 'is_sensitive', 'icon'];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
        ];
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
