@extends('layouts.app')

@section('title', $report->title)
@section('mobile_title', 'Detail Laporan')
@section('mobile_back', route('reports.index'))

@section('content')
    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('reports.index') }}" class="sk-back-link mb-3 d-none d-md-inline-flex">
            <i class="bi bi-chevron-left"></i> Laporan Saya
        </a>

        <div class="sk-card sk-mobile-flat p-4 sk-detail-page">
            @include('reports.partials.detail', ['report' => $report])
        </div>
    </div>

    {{-- Popup sekali tampil, tepat setelah laporan berhasil dikirim (flash dari ReportController@store) --}}
    @if (session('report_submitted'))
        <div class="modal fade" id="submitted-modal" tabindex="-1" aria-labelledby="submitted-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered sk-modal-sm">
                <div class="modal-content sk-modal text-center">
                    <div class="sk-success-ring mx-auto mb-3"><span><i class="bi bi-check-lg"></i></span></div>
                    <h2 class="h4 fw-bold" id="submitted-modal-title">Laporan terkirim!</h2>
                    <p class="text-secondary">
                        Terima kasih sudah bersuara. Kami akan mengabarimu setiap ada perkembangan pada laporan ini.
                    </p>
                    <div class="mb-4">
                        <span class="sk-report-number">Nomor laporan <strong>#{{ session('report_submitted') }}</strong></span>
                    </div>
                    <button type="button" class="btn btn-sk btn-sk-primary w-100 mb-2" data-bs-dismiss="modal">Lihat status laporan</button>
                    <a href="{{ route('home') }}" class="btn btn-sk btn-sk-outline w-100">Kembali ke beranda</a>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                new bootstrap.Modal(document.getElementById('submitted-modal')).show();
            </script>
        @endpush
    @endif
@endsection
