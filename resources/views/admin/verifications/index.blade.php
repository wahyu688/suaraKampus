@extends('layouts.app')

@section('title', 'Verifikasi KTM')
@section('mobile_title', 'Verifikasi KTM')

@section('content')
    @include('partials.page-header', [
        'title' => 'Verifikasi KTM',
        'subtitle' => 'Mahasiswa yang KTM-nya tidak terbaca otomatis. Cocokkan NIM dan nama di kartu dengan data akun.',
    ])

    <div class="container sk-container py-4 pb-5">
        @forelse ($students as $student)
            <div class="sk-card p-3 p-md-4 mb-3">
                <div class="row g-4 align-items-start">
                    <div class="col-md-5">
                        @if ($student->hasKtmPhoto())
                            <a href="{{ route('admin.verifications.photo', $student) }}" target="_blank" rel="noopener">
                                <img src="{{ route('admin.verifications.photo', $student) }}" alt="Foto KTM {{ $student->name }}" class="sk-ktm-review">
                            </a>
                            <small class="text-secondary d-block mt-1">Klik foto untuk memperbesar.</small>
                        @else
                            {{-- Disk Railway terhapus saat redeploy, jadi foto bisa hilang --}}
                            <div class="sk-ktm-missing">
                                <i class="bi bi-image"></i>
                                Foto tidak ditemukan. Tolak dan minta mahasiswa upload ulang.
                            </div>
                        @endif
                    </div>

                    <div class="col-md-7">
                        <dl class="sk-ktm-data mb-3">
                            <dt>Nama</dt>
                            <dd>{{ $student->name }}</dd>
                            <dt>NIM</dt>
                            <dd class="fw-bold">{{ $student->nim }}</dd>
                            <dt>Email</dt>
                            <dd>{{ $student->email }}</dd>
                            <dt>Percobaan OCR</dt>
                            <dd>{{ $student->ktm_attempts }}×</dd>
                        </dl>

                        <form method="POST" action="{{ route('admin.verifications.approve', $student) }}" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sk btn-sk-primary w-100" @disabled(! $student->hasKtmPhoto())>
                                <i class="bi bi-check-lg me-1"></i> Setujui
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.verifications.reject', $student) }}">
                            @csrf
                            @method('PATCH')
                            {{-- Supaya pesan error hanya muncul di form mahasiswa yang sedang ditolak --}}
                            <input type="hidden" name="rejecting_user" value="{{ $student->id }}">
                            @php($isThisForm = (int) old('rejecting_user') === $student->id)

                            <div class="input-group">
                                <input type="text" name="rejection_reason" maxlength="255"
                                       value="{{ $isThisForm ? old('rejection_reason') : '' }}"
                                       class="form-control @if ($isThisForm) @error('rejection_reason') is-invalid @enderror @endif"
                                       placeholder="Alasan, mis. NIM di KTM berbeda">
                                <button type="submit" class="btn btn-outline-danger">Tolak</button>
                            </div>
                            @if ($isThisForm)
                                @error('rejection_reason')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sk-card p-5 text-center text-secondary">
                <i class="bi bi-patch-check fs-1 d-block mb-2"></i>
                Tidak ada KTM yang menunggu dicek.
            </div>
        @endforelse

        <div class="mt-3">
            {{ $students->links() }}
        </div>
    </div>
@endsection
