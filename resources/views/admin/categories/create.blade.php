@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('mobile_title', 'Tambah Kategori')
@section('mobile_back', route('admin.categories.index'))

@section('content')
    <div class="container sk-container py-4 pb-5">
        <a href="{{ route('admin.categories.index') }}" class="sk-back-link mb-3 d-none d-md-inline-flex">
            <i class="bi bi-chevron-left"></i> Kelola Kategori
        </a>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="sk-card sk-mobile-flat p-4 p-md-5 sk-form-narrow" novalidate>
            @csrf
            <h1 class="h4 fw-bold mb-4">Tambah kategori</h1>

            @include('admin.categories.form')

            <button type="submit" class="btn btn-sk btn-sk-primary w-100">Simpan kategori</button>
        </form>
    </div>
@endsection
