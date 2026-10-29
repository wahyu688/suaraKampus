{{-- Dipakai bersama oleh create.blade.php dan edit.blade.php.
     Saat tambah, $category adalah objek kosong (new Category), jadi nilainya null. --}}
<div class="mb-3">
    <label for="name" class="form-label sk-label">Nama kategori</label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="60"
           placeholder="Contoh: Wi-Fi & Jaringan"
           class="form-control sk-input @error('name') is-invalid @enderror">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label sk-label">Deskripsi singkat</label>
    <textarea id="description" name="description" rows="3" maxlength="200"
              placeholder="Muncul di kartu kategori di Beranda."
              class="form-control sk-textarea @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<fieldset class="mb-3">
    <legend class="form-label sk-label">Ikon</legend>
    <div class="d-flex flex-wrap gap-2">
        @foreach (\App\Models\Category::ICONS as $icon => $label)
            <input type="radio" class="btn-check" name="icon" id="icon-{{ $icon }}" value="{{ $icon }}"
                   @checked(old('icon', $category->icon) === $icon)>
            <label class="sk-chip" for="icon-{{ $icon }}" title="{{ $label }}">
                <i class="bi {{ $icon }} d-inline"></i> {{ $label }}
            </label>
        @endforeach
    </div>
    @error('icon')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</fieldset>

<div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" id="is_sensitive" name="is_sensitive" value="1"
           @checked(old('is_sensitive', $category->is_sensitive))>
    <label class="form-check-label" for="is_sensitive">
        <strong>Kategori sensitif</strong>
        <small class="d-block text-secondary">Laporan hanya bisa dibaca konselor dan admin, dan pelapor disarankan anonim.</small>
    </label>
</div>
