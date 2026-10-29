{{-- Di HP judul halaman sudah tampil di top bar, jadi header ini disembunyikan --}}
<section class="sk-page-header d-none d-md-block">
    <div class="container sk-container d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
            <h1>{{ $heading ?? $title }}</h1>
            <p class="mb-0">{{ $subtitle }}</p>
        </div>

        {{-- Tombol opsional di kanan, misalnya "Buat Laporan" di Laporan Saya --}}
        @isset($actionUrl)
            <a href="{{ $actionUrl }}" class="btn btn-sk btn-sk-primary d-none d-md-inline-block">
                <i class="bi bi-plus-lg me-1"></i> {{ $actionLabel }}
            </a>
        @endisset
    </div>
</section>
