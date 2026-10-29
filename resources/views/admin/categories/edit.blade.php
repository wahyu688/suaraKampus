@extends('layouts.app')

@section('title', 'Ubah Kategori')
@section('mobile_title', 'Ubah Kategori')
@section('mobile_back', route('admin.categories.index'))

@section('content')
    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('admin.categories.index') }}" class="sk-back-link mb-3 d-none d-md-inline-flex">
            <i class="bi bi-chevron-left"></i> Kelola Kategori
        </a>

        {{-- Form HTML hanya kenal GET/POST; @method('PUT') membuat Laravel memperlakukannya sebagai PUT (update) --}}
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="sk-card sk-mobile-flat p-4 p-md-5 sk-form-narrow" novalidate>
            @csrf
            @method('PUT')
            <h1 class="h4 fw-bold mb-4">Ubah kategori</h1>

            @include('admin.categories.form')

            <button type="submit" class="btn btn-sk btn-sk-primary w-100">Simpan perubahan</button>
        </form>
    </div>
@endsection
