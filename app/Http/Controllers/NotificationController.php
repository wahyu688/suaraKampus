<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Semua notifikasi milik user yang login, terbaru di atas
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(10);

        return view('notifications.index', ['notifications' => $notifications]);
    }

    // Klik notifikasi: tandai sudah dibaca, lalu buka laporannya
    public function open(Request $request, string $id)
    {
        // Dicari lewat notifications() milik user, jadi notifikasi orang lain menghasilkan 404
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
