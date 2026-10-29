{{-- Hanya tampil di layar < 768px (d-md-none) --}}
<nav class="sk-tabbar d-md-none">
    <a href="{{ route('home') }}" class="sk-tab {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house"></i>
        <span>Beranda</span>
    </a>

    @if (auth()->user()?->isStaff())
        <a href="{{ route('staff.reports.index') }}" class="sk-tab {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i>
            <span>Laporan Masuk</span>
        </a>
    @else
        <a href="{{ route('reports.index') }}" class="sk-tab {{ request()->routeIs('reports.index', 'reports.show') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('reports.create') }}" class="sk-tab sk-tab-lapor {{ request()->routeIs('reports.create') ? 'active' : '' }}">
            <span class="sk-fab"><i class="bi bi-plus-lg"></i></span>
            <span>Lapor</span>
        </a>
    @endif

    {{-- Tamu diarahkan ke halaman login (route notifikasi butuh login) --}}
    <a href="{{ route('notifications.index') }}" class="sk-tab {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <span class="position-relative">
            <i class="bi bi-bell"></i>
            @if (auth()->user()?->unreadNotifications()->exists())
                <span class="sk-dot"></span>
            @endif
        </span>
        <span>Notifikasi</span>
    </a>
    <a href="{{ route('help.index') }}" class="sk-tab {{ request()->routeIs('help.index') ? 'active' : '' }}">
        <i class="bi bi-question-circle"></i>
        <span>Bantuan</span>
    </a>
</nav>
