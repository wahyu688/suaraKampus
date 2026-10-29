@extends('layouts.app')

@section('title', 'Laporan Saya')
@section('mobile_title', 'Laporan Saya')

@section('content')
    @include('partials.page-header', [
        'title' => 'Laporan Saya',
        'subtitle' => 'Pantau perkembangan semua laporan yang sudah kamu kirim.',
        'actionUrl' => route('reports.create'),
        'actionLabel' => 'Buat Laporan',
    ])

    <div class="container sk-container py-4 pb-5">
        <div class="row g-4">
            {{-- Kolom kiri: tab + daftar laporan --}}
            <div class="col-lg-5">
                <nav class="sk-tabs mb-3" aria-label="Filter status">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('reports.index', $key ? ['status' => $key] : []) }}"
                           class="sk-tab-pill {{ $status == $key ? 'active' : '' }}"
                           @if ($status == $key) aria-current="page" @endif>
                            {{ $label }} ({{ $counts[$key] }})
                        </a>
                    @endforeach
                </nav>

                @forelse ($reports as $report)
                    <x-report-card :report="$report" :active="$selected && $selected->id === $report->id" />
                @empty
                    <div class="sk-card p-4 text-center">
                        <p class="text-secondary mb-3">Belum ada laporan di tab ini.</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-sk btn-sk-primary btn-sk-sm">Buat Laporan</a>
                    </div>
                @endforelse

                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            </div>

            {{-- Kolom kanan: detail laporan terpilih (hanya desktop) --}}
            <div class="col-lg-7 d-none d-lg-block">
                <div class="sk-card p-4">
                    @if ($selected)
                        @include('reports.partials.detail', ['report' => $selected])
                    @else
                        <p class="text-secondary text-center my-5">Pilih laporan di sebelah kiri untuk melihat detailnya.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
