<nav class="navbar navbar-expand bg-white border-bottom sk-navbar">
    <div class="container sk-container">
        {{-- Di HP, halaman yang mengisi @section('mobile_title') menampilkan judul, bukan logo --}}
        <a class="navbar-brand align-items-center gap-2 @hasSection('mobile_title') d-none d-md-flex @else d-flex @endif" href="{{ route('home') }}">
            <span class="sk-logo-icon"><i class="bi bi-chat-square-text-fill"></i></span>
            <span class="sk-logo-text">Suara<span>Kampus</span></span>
        </a>

        @hasSection('mobile_title')
            <div class="d-flex d-md-none align-items-center gap-2 sk-mobile-title">
                @hasSection('mobile_back')
                    <a href="@yield('mobile_back')" class="sk-icon-btn d-inline-flex align-items-center justify-content-center" aria-label="Kembali">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif
                <span>@yield('mobile_title')</span>
            </div>
        @endif

        {{-- Menu disembunyikan di mobile, nanti diganti tab bar bawah --}}
        <div class="navbar-nav mx-auto d-none d-md-flex gap-4">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
            @if (auth()->user()?->isStaff())
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.reports.index') }}">Laporan Masuk</a>
                @if (auth()->user()->role === 'admin')
                    <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">Petugas</a>
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Kategori</a>
                @endif
            @else
                <a class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}" href="{{ route('reports.create') }}">Buat Laporan</a>
                <a class="nav-link {{ request()->routeIs('reports.index', 'reports.show') ? 'active' : '' }}" href="{{ route('reports.index') }}">Laporan Saya</a>
            @endif
            <a class="nav-link {{ request()->routeIs('help.index') ? 'active' : '' }}" href="{{ route('help.index') }}">Bantuan</a>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
            @auth
                @php
                    // Data lonceng: jumlah yang belum dibaca + 5 notifikasi terbaru milik user yang login
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                    $latestNotifications = auth()->user()->notifications()->take(5)->get();
                @endphp

                <div class="dropdown @hasSection('mobile_title') d-none d-md-block @endif">
                    <button type="button" class="sk-icon-btn" data-bs-toggle="dropdown" aria-expanded="false"
                            aria-label="Notifikasi, {{ $unreadCount }} belum dibaca">
                        <i class="bi bi-bell"></i>
                        @if ($unreadCount > 0)
                            <span class="sk-dot"></span>
                        @endif
                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-0 sk-notif-menu">
                        <div class="sk-notif-head">
                            <strong>Notifikasi</strong>
                            @if ($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link sk-link p-0 small">Tandai sudah dibaca</button>
                                </form>
                            @endif
                        </div>

                        @forelse ($latestNotifications as $notification)
                            <x-notification-item :notification="$notification" />
                        @empty
                            <p class="text-secondary small text-center py-4 mb-0">Belum ada notifikasi.</p>
                        @endforelse

                        <a href="{{ route('notifications.index') }}" class="sk-notif-foot">Lihat semua notifikasi</a>
                    </div>
                </div>
            @endauth

            @auth
                <div class="dropdown">
                    <button type="button" class="sk-avatar-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                        <span class="sk-avatar">{{ auth()->user()->initials }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm sk-account-menu">
                        <div class="px-3 py-2">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-secondary">
                                {{ auth()->user()->role_label }}
                                @if (auth()->user()->nim)
                                    · {{ auth()->user()->nim }}
                                @endif
                            </small>
                        </div>
                        <hr class="dropdown-divider">
                        @if (auth()->user()->role === 'admin')
                            <a class="dropdown-item" href="{{ route('admin.staff.index') }}">
                                <i class="bi bi-people me-2"></i>Kelola Petugas
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tags me-2"></i>Kelola Kategori
                            </a>
                        @endif
                        <a class="dropdown-item" href="{{ route('password.edit') }}">
                            <i class="bi bi-key me-2"></i>Ubah password
                        </a>
                        {{-- Logout wajib POST + @csrf supaya tidak bisa dipicu lewat link biasa --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            {{-- Tidak perlu tombol Masuk di halaman login/daftar itu sendiri --}}
            @guest
                @unless (request()->routeIs('login', 'register'))
                    <a href="{{ route('login') }}" class="btn btn-sk btn-sk-primary btn-sk-sm">Masuk</a>
                @endunless
            @endguest
        </div>
    </div>
</nav>
