<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · SuaraKampus</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/suarakampus.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <x-navbar />

    <main class="flex-grow-1">
        <x-flash />
        @yield('content')
    </main>

    @include('partials.footer')
    <x-mobile-tabbar />

    {{-- JS Bootstrap untuk dropdown akun dan tombol tutup alert --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Script khusus halaman tertentu masuk lewat @push('scripts') --}}
    @stack('scripts')
</body>
</html>
