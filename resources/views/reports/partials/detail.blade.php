{{-- Detail satu laporan. Dipakai di panel kanan Laporan Saya dan di halaman detail. --}}
<div class="d-flex justify-content-between align-items-center gap-2 mb-2">
    <small class="sk-meta">#{{ $report->code }} · Dikirim {{ $report->created_at->translatedFormat('j M Y, H.i') }}</small>
    <x-status-badge :status="$report->status" />
</div>

<h2 class="sk-detail-title">{{ $report->title }}</h2>

<div class="d-flex flex-wrap gap-2 mb-3">
    <span class="sk-tag">{{ $report->category->name }}</span>
    <span class="sk-tag">Kampus {{ $report->campus->name }} · {{ $report->location_detail }}</span>
    @if ($report->is_anonymous)
        <span class="sk-tag"><i class="bi bi-incognito me-1"></i>Dikirim anonim</span>
    @endif
</div>

<p class="sk-detail-text">{{ $report->description }}</p>

@if ($report->photos->isNotEmpty())
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach ($report->photos as $photo)
            <a href="{{ asset('storage/' . $photo->path) }}" target="_blank" rel="noopener">
                <img src="{{ asset('storage/' . $photo->path) }}" alt="Foto bukti {{ $loop->iteration }}" class="sk-detail-photo">
            </a>
        @endforeach
    </div>
@endif

<hr class="my-4">

<h3 class="h6 fw-bold mb-3">Perkembangan laporan</h3>
<x-report-timeline :updates="$report->updates" :status="$report->status"
                   :for-reporter="$report->user_id === auth()->id()" />

@if ($report->rating)
    <div class="sk-rating-done mt-3">
        @for ($i = 1; $i <= 5; $i++)
            <i class="bi {{ $i <= $report->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
        @endfor
        <span class="ms-2">Kamu memberi penilaian {{ $report->rating }} dari 5.</span>
    </div>
@endif

{{-- Percakapan dengan petugas --}}
@foreach ($report->messages as $message)
    @php
        $isMine = $message->user_id === auth()->id();
        $isReporter = $message->user_id === $report->user_id;
        // Pesan dari pelapor anonim tidak boleh menampilkan nama ke petugas
        $hideName = $isReporter && ! $isMine && $report->is_anonymous;
    @endphp
    <div class="sk-message {{ $isMine ? 'is-mine' : '' }}">
        <span class="sk-message-avatar">
            @if ($hideName)
                <i class="bi bi-incognito"></i>
            @else
                {{ $message->user->initials }}
            @endif
        </span>
        <div>
            <div class="sk-message-name">
                @if ($isMine)
                    Kamu
                @elseif ($isReporter)
                    {{ $report->reporter_name }} · Pelapor
                @else
                    {{ $message->user->name }} · {{ $message->user->role_label }}
                @endif
            </div>
            <p class="mb-1">{{ $message->body }}</p>
            <small>{{ $message->created_at->translatedFormat('j M Y, H.i') }}</small>
        </div>
    </div>
@endforeach

{{-- Tombol aksi; @can mengecek aturan yang sama dengan ReportPolicy --}}
<div class="d-flex flex-wrap gap-2 mt-4">
    @can('message', $report)
        <button type="button" class="btn btn-sk btn-sk-primary btn-sk-sm" data-bs-toggle="modal" data-bs-target="#message-modal">
            {{ $report->messages->isNotEmpty() ? 'Balas petugas' : 'Tambah keterangan' }}
        </button>
    @endcan

    @can('rate', $report)
        <button type="button" class="btn btn-sk btn-sk-primary btn-sk-sm" data-bs-toggle="modal" data-bs-target="#rating-modal">
            Beri penilaian
        </button>
    @endcan

    @can('cancel', $report)
        <button type="button" class="btn btn-sk btn-sk-danger-outline btn-sk-sm" data-bs-toggle="modal" data-bs-target="#cancel-modal">
            Batalkan laporan
        </button>
    @endcan
</div>

{{-- ===== Popup kirim pesan ===== --}}
@can('message', $report)
    <div class="modal fade" id="message-modal" tabindex="-1" aria-labelledby="message-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('reports.messages.store', $report) }}" class="modal-content sk-modal">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h5 fw-bold mb-0" id="message-modal-title">Kirim pesan ke petugas</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <p class="text-secondary small">Tambahkan keterangan atau balas petugas. Pesanmu akan tercatat di riwayat laporan.</p>

                <label for="body" class="visually-hidden">Pesan</label>
                <textarea id="body" name="body" rows="4" maxlength="1000" placeholder="Tulis pesanmu di sini..."
                          class="form-control sk-textarea @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-sk btn-sk-outline btn-sk-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sk btn-sk-primary btn-sk-sm">Kirim pesan</button>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- ===== Popup penilaian ===== --}}
@can('rate', $report)
    <div class="modal fade" id="rating-modal" tabindex="-1" aria-labelledby="rating-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('reports.rating.store', $report) }}" class="modal-content sk-modal text-center">
                @csrf
                <h2 class="h5 fw-bold" id="rating-modal-title">Bagaimana penanganannya?</h2>
                <p class="text-secondary small">Penilaianmu membantu kampus memperbaiki layanan untuk mahasiswa lain.</p>

                {{-- Urutan 5 ke 1 + CSS row-reverse supaya bintang di kiri ikut menyala --}}
                <fieldset class="sk-stars mx-auto mb-3">
                    <legend class="visually-hidden">Jumlah bintang</legend>
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}" @checked(old('rating') == $i)>
                        <label for="star-{{ $i }}" title="{{ $i }} bintang"><i class="bi bi-star-fill"></i></label>
                    @endfor
                </fieldset>
                @error('rating')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <label for="rating_comment" class="visually-hidden">Komentar</label>
                <textarea id="rating_comment" name="rating_comment" rows="3" maxlength="500"
                          placeholder="Tambahkan komentar (opsional)" class="form-control sk-textarea">{{ old('rating_comment') }}</textarea>

                <button type="submit" class="btn btn-sk btn-sk-primary w-100 mt-3">Kirim penilaian</button>
                <button type="button" class="btn btn-link sk-link mt-1" data-bs-dismiss="modal">Nanti saja</button>
            </form>
        </div>
    </div>
@endcan

{{-- ===== Popup batalkan ===== --}}
@can('cancel', $report)
    <div class="modal fade" id="cancel-modal" tabindex="-1" aria-labelledby="cancel-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('reports.cancel', $report) }}" class="modal-content sk-modal text-center">
                @csrf
                @method('PATCH')
                <span class="sk-icon-circle is-danger mx-auto mb-3"><i class="bi bi-exclamation-triangle"></i></span>
                <h2 class="h5 fw-bold" id="cancel-modal-title">Batalkan laporan ini?</h2>
                <p class="text-secondary small">
                    Laporan #{{ $report->code }} akan ditutup dan tidak diteruskan ke unit terkait. Tindakan ini tidak bisa diurungkan.
                </p>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-sk btn-sk-outline btn-sk-sm" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-sk btn-sk-danger btn-sk-sm">Ya, batalkan</button>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- Kalau validasi pesan/penilaian gagal, buka lagi popup-nya supaya error-nya terlihat --}}
@if ($errors->has('body') || $errors->has('rating') || $errors->has('rating_comment'))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const id = @json($errors->has('body') ? '#message-modal' : '#rating-modal');
                const el = document.querySelector(id);
                if (el) {
                    new bootstrap.Modal(el).show();
                }
            });
        </script>
    @endpush
@endif
