@extends('layouts.app')

@section('title', 'Ubah Password')
@section('mobile_title', 'Ubah Password')

@section('content')
    <div class="container sk-container py-5">
        <div class="sk-card sk-auth-card mx-auto p-4 p-md-5">
            <h1 class="mb-1">Ubah password</h1>

            @if ($user->must_change_password)
                <div class="alert alert-warning small mt-3" role="alert">
                    <i class="bi bi-shield-lock me-1"></i>
                    Akunmu masih memakai password sementara dari admin. Ganti dulu sebelum mulai menangani laporan.
                </div>
            @else
                <p class="text-secondary mb-4">Pakai minimal 8 karakter yang tidak dipakai di akun lain.</p>
            @endif

            <form method="POST" action="{{ route('password.update') }}" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label sk-label">Password saat ini</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                           class="form-control sk-input @error('current_password') is-invalid @enderror">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label sk-label">Password baru</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                           class="form-control sk-input @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label sk-label">Ulangi password baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                           class="form-control sk-input">
                </div>

                <button type="submit" class="btn btn-sk btn-sk-primary w-100">Simpan password</button>
            </form>
        </div>
    </div>
@endsection
