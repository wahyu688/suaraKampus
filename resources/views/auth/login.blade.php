@extends('layouts.app')

@section('title', 'Masuk')
@section('mobile_title', 'Masuk')
@section('mobile_back', route('home'))

@section('content')
    <div class="container sk-container py-5">
        <div class="sk-card sk-auth-card mx-auto p-4 p-md-5">
            <h1 class="mb-1">Masuk</h1>
            <p class="text-secondary mb-4">Masuk untuk membuat dan memantau laporanmu.</p>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label sk-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control sk-input @error('email') is-invalid @enderror"
                           autocomplete="email" placeholder="nama@binus.ac.id" autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label sk-label">Password</label>
                    <input type="password" id="password" name="password"
                           class="form-control sk-input @error('password') is-invalid @enderror"
                           autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-sk btn-sk-primary w-100">Masuk</button>
            </form>

            <p class="text-center text-secondary mt-4 mb-0">
                Belum punya akun? <a href="{{ route('register') }}" class="sk-link">Daftar di sini</a>
            </p>
        </div>
    </div>
@endsection
