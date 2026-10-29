<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

// Laravel otomatis memakai policy ini untuk model Report (dari kesamaan nama)
class ReportPolicy
{
    // ===== Mahasiswa (pemilik laporan) =====

    // Mahasiswa hanya boleh melihat laporannya sendiri
    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }

    // Kirim pesan: pemilik, dan laporannya belum dibatalkan
    public function message(User $user, Report $report): bool
    {
        return $this->view($user, $report) && $report->status !== 'cancelled';
    }

    // Batalkan: pemilik, dan statusnya masih Menunggu
    public function cancel(User $user, Report $report): bool
    {
        return $this->view($user, $report) && $report->status === 'pending';
    }

    // Penilaian: pemilik, status Selesai, dan belum pernah menilai
    public function rate(User $user, Report $report): bool
    {
        return $this->view($user, $report)
            && $report->status === 'resolved'
            && $report->rating === null;
    }

    // ===== Petugas, konselor, admin =====

    // Melihat dan membalas: aturannya sama dengan scope Report::visibleTo()
    public function handle(User $user, Report $report): bool
    {
        return match ($user->role) {
            'admin' => true,
            'counselor' => $report->category->is_sensitive,
            'staff' => ! $report->category->is_sensitive,
            default => false,
        };
    }

    // Ubah status: boleh menangani, dan laporannya belum ditutup
    public function updateStatus(User $user, Report $report): bool
    {
        return $this->handle($user, $report)
            && ! in_array($report->status, ['resolved', 'cancelled']);
    }
}
