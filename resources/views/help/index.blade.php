@extends('layouts.app')

@section('title', 'Bantuan')
@section('mobile_title', 'Bantuan')
@section('mobile_back', route('home'))

@section('content')
    @include('partials.page-header', [
        'title' => 'Bantuan',
        'heading' => 'Pusat Bantuan',
        'subtitle' => 'Jawaban untuk pertanyaan yang sering ditanyakan, plus kontak kalau kamu butuh bantuan langsung.',
    ])

    <div class="container sk-container py-4 pb-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="h5 fw-bold mb-3">Pertanyaan umum</h2>

                {{-- <details> bisa buka-tutup sendiri tanpa JavaScript --}}
                @foreach ($faqs as $faq)
                    <details class="sk-faq" @if ($loop->first) open @endif>
                        <summary>
                            {{ $faq['question'] }}
                            <i class="bi bi-chevron-down"></i>
                        </summary>
                        <p>{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="sk-card p-4">
                    <h2 class="h6 fw-bold mb-3">Masih butuh bantuan?</h2>

                    <div class="sk-contact">
                        <span class="sk-icon-circle is-orange"><i class="bi bi-people"></i></span>
                        <div>
                            <strong>Konselor kampus</strong>
                            <small>Untuk perundungan, konflik, atau curhat. Bisa dikirim anonim.</small>
                        </div>
                    </div>
                    <div class="sk-contact">
                        <span class="sk-icon-circle"><i class="bi bi-lightbulb"></i></span>
                        <div>
                            <strong>Pengelola gedung</strong>
                            <small>Untuk fasilitas rusak dan masalah kebersihan di gedung kampus.</small>
                        </div>
                    </div>
                    <div class="sk-contact">
                        <span class="sk-icon-circle"><i class="bi bi-lock"></i></span>
                        <div>
                            <strong>Keamanan kampus</strong>
                            <small>Untuk kejadian yang membahayakan di area kampus.</small>
                        </div>
                    </div>

                    @unless (auth()->user()?->isStaff())
                        <a href="{{ route('reports.create') }}" class="btn btn-sk btn-sk-primary w-100 mt-2">Buat Laporan</a>
                    @endunless
                </div>

                <div class="sk-urgent-card p-4">
                    <h2 class="h6 fw-bold">Keadaan darurat?</h2>
                    <p>Jangan menunggu balasan lewat aplikasi. Segera hubungi keamanan kampus atau layanan darurat 112.</p>
                    <a href="tel:112" class="btn btn-sk btn-sk-accent">Hubungi 112</a>
                </div>

                <div class="sk-sdg-card p-4">
                    <h2 class="h6 fw-bold text-white d-flex align-items-center gap-2">
                        <span class="sk-sdg">16</span> Kenapa SuaraKampus?
                    </h2>
                    <p class="mb-0">Mendukung SDG 16 dengan menyediakan saluran pengaduan yang mudah, aman, dan transparan untuk mahasiswa BINUS.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
