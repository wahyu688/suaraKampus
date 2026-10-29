@extends('layouts.app')

@section('title', 'Tambah Petugas')
@section('mobile_title', 'Tambah Petugas')
@section('mobile_back', route('admin.staff.index'))

@section('content')
    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('admin.staff.index') }}" class="sk-back-link mb-3 d-none d-md-inline-flex">
            <i class="bi bi-chevron-left"></i> Kelola Petugas
        </a>

        <form method="POST" action="{{ route('admin.staff.store') }}" class="sk-card sk-auth-card sk-mobile-flat p-4 p-md-5" novalidate>
            @csrf
            <h1 class="h4 fw-bold mb-1">Tambah petugas</h1>
            <p class="text-secondary mb-4">Password sementara dibuat otomatis setelah akun disimpan.</p>

            <div class="mb-3">
                <label for="name" class="form-label sk-label">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="100"
                       placeholder="Contoh: Building Management Kampus Kijang"
                       class="form-control sk-input @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label sk-label">Email BINUS</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@binus.edu"
                       class="form-control sk-input @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="form-text">
                        Harus berakhiran {{ implode(' atau ', array_map(fn ($domain) => '@' . $domain, config('suarakampus.staff_email_domains'))) }}.
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="role" class="form-label sk-label">Peran</label>
                <select id="role" name="role" class="form-select sk-input @error('role') is-invalid @enderror">
                    <option value="staff" @selected(old('role') === 'staff')>Petugas (fasilitas, kebersihan, keamanan)</option>
                    <option value="counselor" @selected(old('role') === 'counselor')>Konselor (laporan sensitif)</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin (semua laporan + kelola petugas)</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-sk btn-sk-primary w-100">Simpan akun</button>
        </form>
    </div>
@endsection
