@props(['report', 'active' => false])

<div class="sk-card sk-report-card position-relative {{ $active ? 'is-active' : '' }}">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
        <small class="sk-meta">#{{ $report->code }} · {{ $report->created_at->translatedFormat('j M Y') }}</small>
        <x-status-badge :status="$report->status" />
    </div>
    <h3>{{ $report->title }}</h3>
    <small class="text-secondary">
        {{ $report->category->name }} ·
        {{ $report->is_anonymous ? 'Dikirim anonim' : 'Kampus ' . $report->campus->name }}
    </small>

    {{-- Desktop: tampilkan di panel kanan. Mobile: buka halaman detail. --}}
    <a href="{{ request()->fullUrlWithQuery(['laporan' => $report->code]) }}"
       class="stretched-link d-none d-lg-block" aria-label="Lihat {{ $report->title }}"></a>
    <a href="{{ route('reports.show', $report) }}"
       class="stretched-link d-lg-none" aria-label="Buka {{ $report->title }}"></a>
</div>
