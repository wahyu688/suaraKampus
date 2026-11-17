<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'verification_status',
        'ktm_path',
        'ktm_attempts',
        'rejection_reason',
        'verified_at',
    ];

    // Nilai default di model, sama dengan default kolom di database
    protected $attributes = [
        'verification_status' => 'verified',
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
            'verified_at' => 'datetime',
        ];
    }

    // Hanya mahasiswa yang wajib verifikasi KTM
    public function needsKtmVerification(): bool
    {
        return $this->role === 'student' && $this->verification_status !== 'verified';
    }

    // Sisa percobaan OCR sebelum harus dicek admin
    public function remainingKtmAttempts(): int
    {
        return max(0, config('suarakampus.ktm_max_attempts') - $this->ktm_attempts);
    }

    // Foto bisa hilang kalau disk server dihapus (misalnya redeploy Railway)
    public function hasKtmPhoto(): bool
    {
        return $this->ktm_path && Storage::disk('local')->exists($this->ktm_path);
    }

    // Scope: mahasiswa yang menunggu dicek admin. Dipakai: User::awaitingReview()->get()
    public function scopeAwaitingReview($query)
    {
        return $query->where('role', 'student')->where('verification_status', 'pending');
    }

    protected function verificationLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->verification_status) {
                'unverified' => 'Belum verifikasi',
                'pending' => 'Menunggu admin',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
            },
        );
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
