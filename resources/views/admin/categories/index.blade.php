@extends('layouts.app')

@section('title', 'Kelola Kategori')
@section('mobile_title', 'Kelola Kategori')

@section('content')
    @include('partials.page-header', [
        'title' => 'Kelola Kategori',
        'subtitle' => 'Kategori yang bisa dipilih pelapor saat membuat laporan.',
        'actionUrl' => route('admin.categories.create'),
        'actionLabel' => 'Tambah Kategori',
    ])

    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-sk btn-sk-primary w-100 mb-3 d-md-none">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </a>

        <div class="sk-card overflow-hidden">
            <div class="table-responsive">
                <table class="table sk-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="d-none d-md-table-cell">Jenis</th>
                            <th class="d-none d-md-table-cell">Dipakai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="sk-icon-circle sk-icon-sm flex-shrink-0"><i class="bi {{ $category->icon }}"></i></span>
                                        <div>
                                            <strong class="d-block">{{ $category->name }}</strong>
                                            <small class="text-secondary">{{ $category->description }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if ($category->is_sensitive)
                                        <span class="sk-badge sk-badge-anon">Sensitif</span>
                                    @else
                                        <span class="sk-tag">Umum</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell text-nowrap">{{ $category->reports_count }} laporan</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sk btn-sk-outline btn-sk-sm">Ubah</a>

                                    {{-- Hapus wajib lewat form DELETE + @csrf, dan ditanya dulu lewat confirm().
                                         @js() mengubah teks PHP jadi string JavaScript yang aman (tanda kutip di-escape). --}}
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                                          onsubmit="return confirm(@js('Hapus kategori "' . $category->name . '"?'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sk btn-sk-danger-outline btn-sk-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
