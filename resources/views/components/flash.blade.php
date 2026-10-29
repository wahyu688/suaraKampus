@if (session('success'))
    <div class="container sk-container pt-3">
        <div class="alert alert-success alert-dismissible fade show sk-flash mb-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container sk-container pt-3">
        <div class="alert alert-danger alert-dismissible fade show sk-flash mb-0" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    </div>
@endif
