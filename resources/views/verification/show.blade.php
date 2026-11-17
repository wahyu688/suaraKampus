@extends('layouts.app')

@section('title', 'Verifikasi KTM')
@section('mobile_title', 'Verifikasi KTM')

@section('content')
    <div class="container sk-container py-5">
        <div class="sk-card sk-auth-card mx-auto p-4 p-md-5">
            <h1 class="mb-1">Verifikasi KTM</h1>

            @if ($user->verification_status === 'pending')
                {{-- Sudah dikirim ke admin: tidak perlu upload lagi, tinggal menunggu --}}
                <div class="text-center py-4">
                    <i class="bi bi-hourglass-split sk-ktm-wait-icon"></i>
                    <h2 class="h5 fw-bold mt-3">KTM-mu sedang dicek admin</h2>
                    <p class="text-secondary mb-0">
                        Kamu akan mendapat notifikasi setelah admin menyetujui atau menolak KTM-mu.
                        Sementara itu kamu tetap bisa membuka Beranda dan Bantuan.
                    </p>
                </div>
            @else
                <p class="text-secondary mb-4">
                    Untuk memastikan pelapor adalah mahasiswa BINUS, foto Kartu Tanda Mahasiswa (KTM) kamu.
                    Sistem akan membaca NIM <strong>{{ $user->nim }}</strong> dari foto secara otomatis.
                </p>

                @if ($user->verification_status === 'rejected')
                    <div class="alert alert-danger small" role="alert">
                        <strong class="d-block"><i class="bi bi-x-circle me-1"></i> Verifikasi sebelumnya ditolak admin</strong>
                        {{ $user->rejection_reason }}
                    </div>
                @endif

                <ul class="sk-ktm-tips small text-secondary mb-4">
                    <li><i class="bi bi-check2"></i> Sisi depan KTM, NIM terlihat jelas</li>
                    <li><i class="bi bi-check2"></i> Kartu memenuhi foto, posisi tegak</li>
                    <li><i class="bi bi-check2"></i> Cahaya terang, tanpa pantulan atau silau</li>
                </ul>

                <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- capture="environment" langsung membuka kamera belakang di HP --}}
                    <label for="ktm" class="sk-ktm-picker @error('ktm') is-invalid @enderror" id="ktm-picker">
                        <img id="ktm-preview" alt="Pratinjau foto KTM" class="d-none">
                        <span id="ktm-placeholder">
                            <i class="bi bi-camera"></i>
                            <strong>Ambil atau pilih foto KTM</strong>
                            <small>JPG/PNG, maks. 5 MB</small>
                        </span>
                    </label>
                    <input type="file" id="ktm" name="ktm" accept="image/jpeg,image/png" capture="environment" class="visually-hidden">

                    @error('ktm')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    <p class="small text-secondary mt-2 mb-4">
                        Sisa percobaan otomatis: <strong>{{ $user->remainingKtmAttempts() }}</strong>.
                        Foto hanya dilihat admin dan dihapus setelah akunmu terverifikasi.
                    </p>

                    <button type="submit" class="btn btn-sk btn-sk-primary w-100">
                        {{ $user->ktm_attempts > 0 ? 'Foto ulang & cek' : 'Kirim & cek KTM' }}
                    </button>
                </form>

                {{-- Jalan pintas kalau OCR terus gagal: hanya muncul setelah pernah upload --}}
                @if ($user->ktm_path)
                    <form method="POST" action="{{ route('verification.request-review') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-sk btn-sk-outline w-100">Kirim ke admin untuk dicek manual</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Pratinjau foto sebelum dikirim
        document.getElementById('ktm')?.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const preview = document.getElementById('ktm-preview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            document.getElementById('ktm-placeholder').classList.add('d-none');
        });
    </script>
@endpush
