<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    // Memungkinkan Report::factory() untuk membuat data palsu (dipakai seeder demo dan tes)
    use HasFactory;

    // Nilai status di database => label yang dilihat pengguna
    public const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = [
        'category_id',
        'campus_id',
        'title',
        'location_detail',
        'incident_date',
        'description',
        'is_anonymous',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'is_anonymous' => 'boolean',
            'rating' => 'integer',
            'resolved_at' => 'datetime',
        ];
    }

    // Kode BN-XXXX dibuat otomatis tepat sebelum laporan baru disimpan
    protected static function booted(): void
    {
        static::creating(function (Report $report) {
            if (empty($report->code)) {
                $lastNumber = Report::pluck('code')
                    ->map(fn ($code) => (int) str_replace('BN-', '', $code))
                    ->max();

                $report->code = 'BN-' . (($lastNumber ?? 2399) + 1);
            }
        });
    }

    // Scope: Report::status('pending'). Kalau status kosong, query tidak difilter.
    public function scopeStatus($query, ?string $status)
    {
        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    // Scope: laporan yang boleh dilihat petugas tertentu.
    // Admin semua, konselor hanya kategori sensitif, staff hanya non-sensitif.
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'counselor') {
            $query->whereHas('category', fn ($category) => $category->where('is_sensitive', true));
        } elseif ($user->role === 'staff') {
            $query->whereHas('category', fn ($category) => $category->where('is_sensitive', false));
        }

        return $query;
    }

    // Nama pelapor untuk petugas. Laporan anonim tidak boleh menampilkan nama/NIM.
    protected function reporterName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_anonymous ? 'Pelapor anonim' : $this->user->name,
        );
    }

    protected function reporterNim(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_anonymous ? null : $this->user->nim,
        );
    }

    // Dipanggil di view sebagai $report->status_label
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUS_LABELS[$this->status],
        );
    }

    // Tahap di progress bar: 1 Terkirim, 2 Diverifikasi, 3 Diproses, 4 Selesai
    protected function progressStep(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                'pending' => 1,
                'in_progress' => 3,
                'resolved' => 4,
                'cancelled' => 0,
            },
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function photos()
    {
        return $this->hasMany(ReportPhoto::class);
    }

    // Diurutkan supaya timeline dan percakapan tampil dari yang paling lama
    public function updates()
    {
        return $this->hasMany(ReportUpdate::class)->oldest();
    }

    public function messages()
    {
        return $this->hasMany(ReportMessage::class)->oldest();
    }
}
