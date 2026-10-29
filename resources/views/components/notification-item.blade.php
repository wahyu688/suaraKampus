@props(['notification'])

{{-- Satu baris notifikasi. Dipakai di dropdown lonceng dan di halaman Notifikasi. --}}
<a href="{{ route('notifications.open', $notification->id) }}"
   class="sk-notif-item {{ $notification->read_at ? '' : 'is-unread' }}">
    <span class="sk-notif-dot" aria-hidden="true"></span>
    <span>
        <strong class="d-block">{{ $notification->data['title'] }}</strong>
        <span class="d-block">{{ $notification->data['body'] }}</span>
        <small>
            {{ $notification->created_at->diffForHumans() }}
            @unless ($notification->read_at)
                <span class="visually-hidden">(belum dibaca)</span>
            @endunless
        </small>
    </span>
</a>
