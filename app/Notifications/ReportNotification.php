<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

// Satu jenis notifikasi untuk semua kejadian laporan (status berubah, dibalas, laporan baru).
// Dikirim dengan $user->notify(new ReportNotification(...)) dan disimpan di tabel notifications.
class ReportNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public string $url,
    ) {
    }

    // Disimpan di database (bukan email), lalu ditampilkan di lonceng
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Isi kolom "data" di tabel notifications
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ];
    }
}
