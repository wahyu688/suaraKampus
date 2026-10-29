@extends('layouts.app')

@section('title', $report->title)
@section('mobile_title', 'Detail Laporan')
@section('mobile_back', route('staff.reports.index'))

@section('content')
    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('staff.reports.index') }}" class="sk-back-link mb-3 d-none d-md-inline-flex">
            <i class="bi bi-chevron-left"></i> Laporan Masuk
        </a>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="sk-card sk-mobile-flat p-4">
                    @include('reports.partials.detail', ['report' => $report])
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                {{-- Identitas pelapor; anonim = nama dan NIM disembunyikan --}}
                <div class="sk-card p-4">
                    <h2 class="h6 fw-bold mb-3">Pelapor</h2>
                    @if ($report->is_anonymous)
                        <div class="d-flex align-items-center gap-2">
                            <span class="sk-icon-circle is-orange sk-icon-sm"><i class="bi bi-incognito"></i></span>
                            <div>
                                <strong class="d-block">Pelapor anonim</strong>
                                <small class="text-secondary">Identitas disembunyikan atas permintaan pelapor.</small>
                            </div>
                        </div>
                    @else
                        <strong class="d-block">{{ $report->reporter_name }}</strong>
                        {{-- Dosen/staf tidak punya NIM, jadi yang ditampilkan perannya --}}
                        <small class="text-secondary">
                            {{ $report->reporter_nim ? 'NIM ' . $report->reporter_nim : $report->user->role_label }}
                        </small>
                    @endif
                </div>

                @can('updateStatus', $report)
                    <form method="POST" action="{{ route('staff.reports.status', $report) }}" class="sk-card p-4" novalidate>
                        @csrf
                        @method('PATCH')
                        <h2 class="h6 fw-bold mb-3">Ubah status</h2>

                        <div class="mb-3">
                            <label for="status" class="form-label sk-label">Status baru</label>
                            <select id="status" name="status" class="form-select sk-input @error('status') is-invalid @enderror">
                                <option value="in_progress" @selected(old('status') === 'in_progress')>Diproses</option>
                                <option value="resolved" @selected(old('status') === 'resolved')>Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label sk-label">Judul perkembangan</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="80"
                                   placeholder="Contoh: Diverifikasi & diteruskan"
                                   class="form-control sk-input @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label sk-label">Catatan untuk pelapor</label>
                            <textarea id="note" name="note" rows="3" maxlength="1000"
                                      placeholder="Apa yang sudah atau akan dilakukan?"
                                      class="form-control sk-textarea @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-sk btn-sk-primary w-100">Simpan perubahan</button>
                    </form>
                @else
                    <div class="sk-card p-4 text-secondary small">
                        <i class="bi bi-lock me-1"></i> Laporan ini sudah ditutup, statusnya tidak bisa diubah lagi.
                    </div>
                @endcan

                @if ($report->status !== 'cancelled')
                    <form method="POST" action="{{ route('staff.reports.messages.store', $report) }}" class="sk-card p-4" novalidate>
                        @csrf
                        <h2 class="h6 fw-bold mb-3">Balas pelapor</h2>
                        <label for="body" class="visually-hidden">Balasan</label>
                        <textarea id="body" name="body" rows="3" maxlength="1000" placeholder="Tulis balasan untuk pelapor..."
                                  class="form-control sk-textarea @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-sk btn-sk-outline w-100 mt-3">Kirim balasan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
