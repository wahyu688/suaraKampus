@extends('layouts.app')

@section('title', 'Kelola Petugas')
@section('mobile_title', 'Kelola Petugas')

@section('content')
    @include('partials.page-header', [
        'title' => 'Kelola Petugas',
        'subtitle' => 'Akun petugas, konselor, dan admin. Petugas tidak bisa mendaftar sendiri, akunnya dibuat di sini.',
        'actionUrl' => route('admin.staff.create'),
        'actionLabel' => 'Tambah Petugas',
    ])

    <div class="container sk-container py-4 pb-5">
        {{-- Password sementara hanya tampil sekali, tepat setelah akun dibuat --}}
        @if (session('temporary_password'))
            <div class="sk-temp-password mb-4">
                <h2 class="h6 fw-bold mb-2"><i class="bi bi-key me-1"></i> Password sementara untuk {{ session('new_staff_email') }}</h2>
                <code class="sk-temp-code">{{ session('temporary_password') }}</code>
                <p class="small mb-0 mt-2">
                    Berikan langsung ke petugasnya. Password ini hanya tampil sekali, dan petugas wajib menggantinya saat login pertama.
                </p>
            </div>
        @endif

        <a href="{{ route('admin.staff.create') }}" class="btn btn-sk btn-sk-primary w-100 mb-3 d-md-none">
            <i class="bi bi-plus-lg me-1"></i> Tambah Petugas
        </a>

        <div class="sk-card overflow-hidden">
            <div class="table-responsive">
                <table class="table sk-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Peran</th>
                            <th class="d-none d-md-table-cell">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staff as $member)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $member->name }}</strong>
                                    <small class="text-secondary d-md-none">{{ $member->email }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $member->email }}</td>
                                <td><span class="sk-tag">{{ $member->role_label }}</span></td>
                                <td class="d-none d-md-table-cell">
                                    @if ($member->must_change_password)
                                        <span class="sk-badge sk-badge-pending">Belum ganti password</span>
                                    @else
                                        <span class="sk-badge sk-badge-resolved">Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
