{{-- Flash message tampil sebagai toast (kotak kecil di bawah layar) yang hilang sendiri setelah beberapa detik --}}
@if (session('success') || session('error'))
    <div class="sk-toast-area">
        @if (session('success'))
            <div class="toast sk-toast" role="status" aria-live="polite" aria-atomic="true" data-bs-delay="4500">
                <span class="sk-toast-icon is-success"><i class="bi bi-check-lg"></i></span>
                <span class="sk-toast-text">{{ session('success') }}</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            {{-- Pesan gagal tidak hilang sendiri, supaya sempat dibaca --}}
            <div class="toast sk-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                <span class="sk-toast-icon is-error"><i class="bi bi-exclamation-lg"></i></span>
                <span class="sk-toast-text">{{ session('error') }}</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Toast Bootstrap tidak muncul otomatis; harus dipanggil .show()
            document.querySelectorAll('.sk-toast').forEach(function (el) {
                bootstrap.Toast.getOrCreateInstance(el).show();
            });
        </script>
    @endpush
@endif
