@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    {{-- Hero --}}
    <section class="sk-hero">
        <div class="container sk-container">
            <div class="row align-items-center gx-4 gy-5">
                <div class="col-lg-7">
                    <span class="sk-hero-tag">#SuaraBinusian</span>
                    <h1 class="sk-hero-title">Kampus nyaman dimulai dari suara kamu</h1>
                    <p class="sk-hero-text">
                        Laporkan sampah, fasilitas rusak, atau masalah yang kamu alami di kampus.
                        Lampirkan foto, pantau statusnya, dan pilih anonim kalau perlu.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        @if (auth()->user()?->isStaff())
                            <a href="{{ route('staff.reports.index') }}" class="btn btn-sk btn-sk-light">Lihat Laporan Masuk</a>
                        @else
                            <a href="{{ route('reports.create') }}" class="btn btn-sk btn-sk-light">Buat Laporan</a>
                            <a href="{{ route('reports.index') }}" class="btn btn-sk btn-sk-ghost">Lacak Laporan</a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-5">
                    @if ($latestReport)
                        <a href="{{ route('reports.index', ['laporan' => $latestReport->code]) }}" class="sk-hero-card d-block mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="sk-meta">#{{ $latestReport->code }} · {{ $latestReport->created_at->diffForHumans() }}</small>
                                <x-status-badge :status="$latestReport->status" />
                            </div>
                            <h2 class="sk-hero-card-title">{{ $latestReport->title }}</h2>
                            <p class="sk-hero-card-sub">
                                {{ $latestReport->category->name }} · Kampus {{ $latestReport->campus->name }}, {{ $latestReport->location_detail }}
                            </p>

                            <div class="sk-track">
                                <div class="sk-track-fill" style="width: {{ $latestReport->progress_step * 25 }}%"></div>
                            </div>
                            <div class="sk-steps">
                                @foreach (['Terkirim', 'Diverifikasi', 'Diproses', 'Selesai'] as $index => $step)
                                    <span class="{{ $index < $latestReport->progress_step ? 'is-done' : '' }}">{{ $step }}</span>
                                @endforeach
                            </div>
                        </a>
                    @else
                        <div class="sk-hero-card mb-3">
                            <h2 class="sk-hero-card-title">Pantau laporanmu di sini</h2>
                            <p class="sk-hero-card-sub mb-0">
                                Setelah kamu mengirim laporan, status terbarunya akan muncul di kartu ini.
                            </p>
                        </div>
                    @endif

                    <div class="sk-hero-card d-flex align-items-center gap-3">
                        <span class="sk-icon-circle is-orange flex-shrink-0"><i class="bi bi-shield-check"></i></span>
                        <div>
                            <div class="fw-bold">Laporan sensitif bisa anonim</div>
                            <small class="text-secondary">Untuk kasus perundungan atau konflik, identitasmu disembunyikan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistik --}}
    <section class="bg-white py-5">
        <div class="container sk-container">
            <div class="row g-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="sk-stat">
                        <strong>{{ number_format($totalReports, 0, ',', '.') }}</strong>
                        <span>laporan sudah masuk</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sk-stat">
                        <strong>{{ $resolvedPercent }}%</strong>
                        <span>laporan sudah selesai ditangani</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sk-stat">
                        <strong>{{ $averageDays ? number_format($averageDays, 1, ',', '.') . ' hari' : '-' }}</strong>
                        <span>rata-rata waktu penanganan</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sk-stat">
                        <strong class="text-accent">24/7</strong>
                        <span>bisa melapor kapan saja</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    <section class="py-5 my-lg-4">
        <div class="container sk-container">
            <p class="sk-eyebrow">Kategori laporan</p>
            <h2 class="sk-section-title mb-4">Mau <strong>melapor</strong> apa hari ini?</h2>

            <div class="row g-4">
                @foreach ($categories as $category)
                    <div class="col-md-6 col-lg-4">
                        <x-category-card :category="$category" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Cara kerja --}}
    <section class="sk-how py-5">
        <div class="container sk-container py-lg-4 text-center">
            <p class="sk-eyebrow text-white-50">Cara kerjanya</p>
            <h2 class="sk-section-title text-white mb-5"><strong>Tiga langkah</strong>, laporanmu sampai</h2>

            <div class="row g-4 text-start">
                <div class="col-md-4">
                    <div class="sk-how-card">
                        <span class="sk-how-number">1</span>
                        <h3>Tulis laporan</h3>
                        <p>Pilih kategori, ceritakan masalahnya, lalu lampirkan foto sebagai bukti.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sk-how-card">
                        <span class="sk-how-number">2</span>
                        <h3>Ditinjau unit terkait</h3>
                        <p>Laporan diverifikasi lalu diteruskan ke unit yang berwenang, seperti pengelola gedung atau konselor.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sk-how-card">
                        <span class="sk-how-number">3</span>
                        <h3>Pantau sampai selesai</h3>
                        <p>Cek status dan balasan petugas kapan saja, lalu beri penilaian setelah laporan selesai.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
