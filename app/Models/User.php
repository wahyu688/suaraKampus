<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nim',
        'role',
        'password',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Petugas, konselor, dan admin sama-sama bekerja di halaman /petugas
    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'counselor', 'admin']);
    }

    // Halaman utama setelah login: mahasiswa ke Laporan Saya, petugas ke Laporan Masuk
    public function homeUrl(): string
    {
        return $this->isStaff() ? route('staff.reports.index') : route('reports.index');
    }

    // "Maya Saputri" menjadi "MS" untuk avatar
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(explode(' ', $this->name))
                ->take(2)
                ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                ->join(''),
        );
    }

    protected function roleLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->role) {
                'student' => 'Mahasiswa',
                'employee' => 'Dosen/Staf',
                'staff' => 'Petugas',
                'counselor' => 'Konselor',
                'admin' => 'Admin',
            },
        );
    }
}
