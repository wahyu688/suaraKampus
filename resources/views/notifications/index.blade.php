@extends('layouts.app')

@section('title', 'Notifikasi')
@section('mobile_title', 'Notifikasi')

@section('content')
    @include('partials.page-header', [
        'title' => 'Notifikasi',
        'subtitle' => 'Kabar terbaru tentang laporan yang kamu kirim atau tangani.',
    ])

    <div class="container sk-container py-4 pb-5">
        <div class="sk-card overflow-hidden sk-form-narrow mx-auto">
            <div class="sk-notif-head">
                <strong>{{ $notifications->total() }} notifikasi</strong>
                @if (auth()->user()->unreadNotifications()->exists())
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-link sk-link p-0 small">Tandai semua sudah dibaca</button>
                    </form>
                @endif
            </div>

            @forelse ($notifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <p class="text-secondary text-center py-5 mb-0">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <div class="sk-form-narrow mx-auto mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
