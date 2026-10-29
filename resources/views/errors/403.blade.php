{{-- Laravel otomatis memakai file ini setiap kali ada abort(403) atau Gate::authorize() yang gagal --}}
@extends('layouts.app')

@section('title', 'Tidak punya akses')
@section('mobile_title', 'Tidak punya akses')

@section('content')
    <div class="container sk-container py-5">
        <div class="sk-card sk-auth-card mx-auto p-4 p-md-5 text-center">
            <span class="sk-icon-circle is-danger mx-auto mb-3"><i class="bi bi-lock"></i></span>
            <h1 class="h4 fw-bold">Kamu tidak punya akses ke halaman ini</h1>

            <p class="text-secondary mb-4">
                @if (auth()->user()?->isStaff())
                    Halaman ini khusus pelapor. Sebagai {{ strtolower(auth()->user()->role_label) }}, kamu menangani laporan lewat Laporan Masuk.
                @elseif (auth()->check())
                    Halaman ini bukan untuk akunmu, atau laporannya milik orang lain.
                @else
                    Masuk dulu dengan akun yang punya akses.
                @endif
            </p>

            <a href="{{ auth()->check() ? auth()->user()->homeUrl() : route('login') }}" class="btn btn-sk btn-sk-primary">
                {{ auth()->user()?->isStaff() ? 'Ke Laporan Masuk' : (auth()->check() ? 'Ke Laporan Saya' : 'Masuk') }}
            </a>
        </div>
    </div>
@endsection
