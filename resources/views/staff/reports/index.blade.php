@extends('layouts.app')

@section('title', 'Laporan Masuk')
@section('mobile_title', 'Laporan Masuk')

@section('content')
    @include('partials.page-header', [
        'title' => 'Laporan Masuk',
        'subtitle' => match (auth()->user()->role) {
            'counselor' => 'Laporan kategori sensitif yang perlu ditangani tim konselor.',
            'staff' => 'Laporan fasilitas, kebersihan, dan keamanan yang perlu ditangani.',
            default => 'Semua laporan dari mahasiswa.',
        },
    ])

    <div class="container sk-container py-4 pb-5">
        <nav class="sk-tabs mb-3" aria-label="Filter status">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('staff.reports.index', $key ? ['status' => $key] : []) }}"
                   class="sk-tab-pill {{ $status == $key ? 'active' : '' }}"
                   @if ($status == $key) aria-current="page" @endif>
                    {{ $label }} ({{ $counts[$key] }})
                </a>
            @endforeach
        </nav>

        <div class="sk-card overflow-hidden">
            <div class="table-responsive">
                <table class="table sk-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Laporan</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Pelapor</th>
                            <th class="d-none d-md-table-cell">Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr class="position-relative">
                                <td>
                                    <small class="sk-meta d-block">#{{ $report->code }}</small>
                                    <a href="{{ route('staff.reports.show', $report) }}" class="sk-table-link stretched-link">
                                        {{ $report->title }}
                                    </a>
                                    <small class="text-secondary d-block">Kampus {{ $report->campus->name }} · {{ $report->location_detail }}</small>
                                    {{-- Di HP kolom Pelapor disembunyikan, jadi namanya ditaruh di sini --}}
                                    <small class="text-secondary d-block d-md-none">Pelapor: {{ $report->reporter_name }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $report->category->name }}
                                    @if ($report->category->is_sensitive)
                                        <span class="sk-badge sk-badge-anon ms-1">Sensitif</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $report->reporter_name }}
                                    @if ($report->reporter_nim)
                                        <small class="text-secondary d-block">{{ $report->reporter_nim }}</small>
                                    @endif
                                </td>
                                <td class="text-nowrap d-none d-md-table-cell">{{ $report->created_at->translatedFormat('j M Y') }}</td>
                                <td><x-status-badge :status="$report->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">Tidak ada laporan di tab ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
