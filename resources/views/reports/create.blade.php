@extends('layouts.app')

@section('title', 'Buat Laporan')
@section('mobile_title', 'Buat Laporan')
@section('mobile_back', route('reports.index'))

@php
    // Validasi gagal: pakai pilihan terakhir pengguna.
    // Baru buka form: centang otomatis kalau kategorinya sensitif.
    $anonymousChecked = session()->hasOldInput()
        ? old('is_anonymous')
        : ($selectedCategory?->is_sensitive ?? false);
@endphp

@section('content')
    @include('partials.page-header', [
        'title' => 'Buat Laporan',
        'heading' => 'Buat Laporan Baru',
        'subtitle' => 'Isi detail laporan dengan jelas supaya cepat ditindaklanjuti oleh unit terkait.',
    ])

    <div class="container sk-container py-4 pb-5">
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- enctype wajib ada supaya file foto ikut terkirim --}}
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data"
                      class="sk-card sk-mobile-flat p-4" id="report-form" novalidate>
                    @csrf

                    {{-- Kategori: radio yang diberi gaya chip --}}
                    <fieldset class="mb-4">
                        <legend class="form-label sk-label">Kategori laporan</legend>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <input type="radio" class="btn-check" name="category_id"
                                       id="category-{{ $category->id }}" value="{{ $category->id }}"
                                       data-sensitive="{{ $category->is_sensitive ? '1' : '0' }}"
                                       @checked(old('category_id', $selectedCategory?->id) == $category->id)>
                                <label class="sk-chip" for="category-{{ $category->id }}">
                                    <i class="bi bi-check-lg"></i>{{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('category_id')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <div class="mb-4">
                        <label for="title" class="form-label sk-label">Judul laporan</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="80"
                               placeholder="Contoh: Tempat sampah penuh dan berbau di lantai 4"
                               class="form-control sk-input @error('title') is-invalid @enderror">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Singkat dan jelas, maksimal 80 karakter.</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="campus_id" class="form-label sk-label">Kampus</label>
                            <select id="campus_id" name="campus_id" class="form-select sk-input @error('campus_id') is-invalid @enderror">
                                <option value="">Pilih kampus</option>
                                @foreach ($campuses as $campus)
                                    <option value="{{ $campus->id }}" @selected(old('campus_id') == $campus->id)>
                                        Kampus {{ $campus->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('campus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="location_detail" class="form-label sk-label">Gedung / ruang</label>
                            <input type="text" id="location_detail" name="location_detail" value="{{ old('location_detail') }}"
                                   maxlength="120" placeholder="Lantai 4, depan R. 403"
                                   class="form-control sk-input @error('location_detail') is-invalid @enderror">
                            @error('location_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="incident_date" class="form-label sk-label">Tanggal kejadian</label>
                            <input type="date" id="incident_date" name="incident_date"
                                   value="{{ old('incident_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}"
                                   class="form-control sk-input @error('incident_date') is-invalid @enderror">
                            @error('incident_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label sk-label">Deskripsi masalah</label>
                        <div class="sk-textarea-wrap">
                            <textarea id="description" name="description" rows="5" maxlength="1000"
                                      placeholder="Ceritakan apa yang terjadi, sejak kapan, dan dampaknya."
                                      class="form-control sk-textarea @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            <span class="sk-counter" id="description-counter">0 / 1000</span>
                        </div>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <span class="form-label sk-label d-block mb-0">Foto bukti</span>
                        <div class="form-text mt-0 mb-2">Maksimal 3 foto (JPG/PNG, maks. 5 MB per foto).</div>

                        <div class="d-flex flex-wrap gap-3" id="photo-list">
                            <label for="photos" class="sk-upload" id="photo-picker">
                                <i class="bi bi-upload"></i>
                                <strong>Tambah foto</strong>
                                <small id="photo-remaining">Sisa 3 foto</small>
                            </label>
                        </div>
                        <input type="file" id="photos" name="photos[]" accept=".jpg,.jpeg,.png" multiple class="visually-hidden">

                        @error('photos')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                        @error('photos.*')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sk-anon-box mb-4">
                        <div class="form-check form-switch m-0 d-flex gap-3 align-items-start ps-0">
                            <input class="form-check-input sk-switch m-0 flex-shrink-0" type="checkbox" role="switch"
                                   id="is_anonymous" name="is_anonymous" value="1" @checked($anonymousChecked)>
                            <label class="form-check-label" for="is_anonymous">
                                <strong class="d-block">Kirim sebagai anonim</strong>
                                <small>Nama dan NIM kamu tidak ditampilkan ke petugas. Disarankan untuk laporan perundungan atau konflik.</small>
                            </label>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input @error('agreement') is-invalid @enderror" type="checkbox"
                               id="agreement" name="agreement" value="1" @checked(old('agreement'))>
                        <label class="form-check-label" for="agreement">
                            Saya menyatakan laporan ini benar dan dapat dipertanggungjawabkan.
                        </label>
                        @error('agreement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 border-top pt-4">
                        <a href="{{ route('reports.index') }}" class="btn btn-sk btn-sk-outline">Batal</a>
                        <button type="submit" class="btn btn-sk btn-sk-primary">
                            Kirim Laporan <i class="bi bi-send ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="sk-card p-4">
                    <h2 class="h6 fw-bold d-flex align-items-center gap-2 mb-3">
                        <span class="sk-icon-circle sk-icon-sm"><i class="bi bi-lightbulb"></i></span>
                        Tips laporan yang jelas
                    </h2>
                    <ul class="sk-tips mb-0">
                        <li>Sebutkan lokasi detail: gedung, lantai, ruang.</li>
                        <li>Lampirkan foto yang memperlihatkan masalahnya.</li>
                        <li>Ceritakan sejak kapan masalah terjadi.</li>
                        <li>Di laporan umum, hindari menyebut nama orang.</li>
                    </ul>
                </div>

                <div class="sk-privacy-card p-4">
                    <h2 class="h6 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check fs-5"></i> Privasimu terjaga
                    </h2>
                    <p class="mb-0">Laporan sensitif hanya bisa dibaca tim konselor kampus dan tidak dibagikan ke pihak lain.</p>
                </div>

                <div class="sk-urgent-card p-4">
                    <h2 class="h6 fw-bold">Butuh bantuan segera?</h2>
                    <p>Kalau kamu sedang dalam bahaya, segera hubungi keamanan kampus atau layanan darurat 112.</p>
                    <a href="tel:112" class="btn btn-sk btn-sk-accent">Hubungi Bantuan</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/report-form.js') }}"></script>
@endpush
