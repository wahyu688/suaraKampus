@extends('layouts.app')

@section('title', 'Daftar')
@section('mobile_title', 'Daftar')
@section('mobile_back', route('login'))

@section('content')
    <div class="container sk-container py-5">
        <div class="sk-card sk-auth-card mx-auto p-4 p-md-5">
            <h1 class="mb-1">Daftar akun</h1>
            <p class="text-secondary mb-4">Untuk mahasiswa, dosen, dan staf BINUS yang ingin melapor.</p>

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label sk-label">Nama lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control sk-input @error('name') is-invalid @enderror"
                           autocomplete="name" autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label sk-label">Email BINUS</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control sk-input @error('email') is-invalid @enderror"
                           autocomplete="email" placeholder="nama@binus.ac.id">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            {{-- '@' ditulis di dalam {{ }}, karena @{{ di Blade berarti "tampilkan {{ apa adanya" --}}
                            Mahasiswa: {{ '@' . config('suarakampus.student_email_domain') }} · Dosen/staf: {{ '@' . config('suarakampus.employee_email_domain') }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nim" class="form-label sk-label">NIM</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}" inputmode="numeric"
                           class="form-control sk-input @error('nim') is-invalid @enderror">
                    @error('nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Wajib untuk mahasiswa. Dosen/staf boleh mengosongkan.</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label sk-label">Password</label>
                    <input type="password" id="password" name="password"
                           class="form-control sk-input @error('password') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label sk-label">Ulangi password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control sk-input" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-sk btn-sk-primary w-100">Daftar</button>
            </form>

            <p class="text-center text-secondary mt-4 mb-0">
                Sudah punya akun? <a href="{{ route('login') }}" class="sk-link">Masuk</a>
            </p>
        </div>
    </div>
@endsection
