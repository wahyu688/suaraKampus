<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\ReportNotification;
use Illuminate\Database\Seeder;

// Contoh isi lonceng untuk demo, mengikuti dropdown notifikasi di prototipe
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();
        $staff = User::where('email', 'petugas@binus.edu')->first();

        // [penerima, judul, isi, url, kapan, sudah dibaca?]
        $notifications = [
            [$student, 'Laporan #BN-2410 selesai', 'Konselor Kampus menutup laporanmu.', '/laporan/BN-2410', now()->subDays(3), true],
            [$student, 'Laporan #BN-2388 selesai', 'Beri penilaian untuk penanganan laporanmu.', '/laporan/BN-2388', now()->subHours(2), false],
            [$student, 'Laporan #BN-2481 dibalas', 'Building Management membalas laporanmu.', '/laporan/BN-2481', now()->subMinutes(10), false],
            [$staff, 'Laporan baru #BN-2479', 'Tempat sampah penuh dan berbau di lantai 4', '/petugas/laporan/BN-2479', now()->subHours(1), false],
        ];

        foreach ($notifications as [$user, $title, $body, $url, $time, $isRead]) {
            $user->notify(new ReportNotification($title, $body, $url));

            // notify() mencatat waktu "sekarang"; diundur supaya terlihat seperti kejadian lama
            $user->notifications()->latest()->first()->update([
                'created_at' => $time,
                'read_at' => $isRead ? $time : null,
            ]);
        }
    }
}
