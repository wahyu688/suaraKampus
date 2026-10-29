@props(['updates', 'status', 'forReporter' => true])

@php
    $isClosed = in_array($status, ['resolved', 'cancelled']);
@endphp

<ol class="sk-timeline">
    @foreach ($updates as $update)
        {{-- Riwayat terakhir pada laporan yang masih berjalan = tahap yang sedang dikerjakan --}}
        <li class="{{ $loop->last && ! $isClosed ? 'is-current' : 'is-done' }} {{ $update->status === 'cancelled' ? 'is-cancelled' : '' }}">
            <span class="sk-tl-dot">
                <i class="bi {{ $update->status === 'cancelled' ? 'bi-x-lg' : 'bi-check-lg' }}"></i>
            </span>
            <strong>{{ $update->title }}</strong>
            <small>{{ $update->created_at->translatedFormat('j M Y, H.i') }}</small>
            @if ($update->note)
                <p>{{ $update->note }}</p>
            @endif
        </li>
    @endforeach

    @unless ($isClosed)
        <li class="is-waiting">
            <span class="sk-tl-dot"></span>
            <strong>Selesai</strong>
            <p>{{ $forReporter
                ? 'Kamu akan diminta memberi penilaian setelah laporan selesai.'
                : 'Pelapor akan diminta memberi penilaian setelah laporan selesai.' }}</p>
        </li>
    @endunless
</ol>
