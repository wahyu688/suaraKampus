@props(['category'])

@php
    // Petugas tidak membuat laporan, jadi bagi mereka kartu ini hanya informasi (bukan link)
    $canReport = ! auth()->user()?->isStaff();
@endphp

@if ($canReport)
    <a href="{{ route('reports.create', ['kategori' => $category->slug]) }}"
       class="sk-card sk-category-card {{ $category->is_sensitive ? 'is-sensitive' : '' }}">
@else
    <div class="sk-card sk-category-card is-static {{ $category->is_sensitive ? 'is-sensitive' : '' }}">
@endif

    <div class="d-flex justify-content-between align-items-start mb-3">
        <span class="sk-icon-circle"><i class="bi {{ $category->icon }}"></i></span>
        @if ($category->is_sensitive)
            <span class="sk-badge sk-badge-anon">Bisa anonim</span>
        @endif
    </div>

    <h3>{{ $category->name }}</h3>
    <p>{{ $category->description }}</p>

@if ($canReport)
        <span class="sk-category-link">
            {{ $category->is_sensitive ? 'Ceritakan' : 'Laporkan' }}
            <i class="bi bi-arrow-right"></i>
        </span>
    </a>
@else
    </div>
@endif
