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
@endsection
