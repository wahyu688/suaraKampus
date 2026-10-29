// Interaksi kecil di form Buat Laporan (tanpa library)
document.addEventListener('DOMContentLoaded', function () {
    // 1. Penghitung karakter deskripsi
    const description = document.getElementById('description');
    const counter = document.getElementById('description-counter');

    function updateCounter() {
        counter.textContent = description.value.length + ' / 1000';
    }

    description.addEventListener('input', updateCounter);
    updateCounter();

    // 2. Kategori sensitif -> sarankan anonim dengan mencentang toggle-nya
    const anonymous = document.getElementById('is_anonymous');

    document.querySelectorAll('input[name="category_id"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.dataset.sensitive === '1') {
                anonymous.checked = true;
            }
        });
    });

    // 3. Pratinjau foto, maksimal 3
    const MAX_PHOTOS = 3;
    const input = document.getElementById('photos');
    const list = document.getElementById('photo-list');
    const picker = document.getElementById('photo-picker');
    const remaining = document.getElementById('photo-remaining');
    let selectedFiles = [];

    input.addEventListener('change', function () {
        // Gabungkan dengan foto yang sudah dipilih sebelumnya, potong di 3
        selectedFiles = selectedFiles.concat(Array.from(input.files)).slice(0, MAX_PHOTOS);
        renderPhotos();
    });

    function renderPhotos() {
        // Isi ulang <input type="file"> supaya yang terkirim sama dengan yang tampil
        const transfer = new DataTransfer();
        selectedFiles.forEach(function (file) {
            transfer.items.add(file);
        });
        input.files = transfer.files;

        list.querySelectorAll('.sk-photo').forEach(function (el) {
            el.remove();
        });

        selectedFiles.forEach(function (file, index) {
            const item = document.createElement('div');
            item.className = 'sk-photo';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Foto bukti ' + (index + 1);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'sk-photo-remove';
            remove.setAttribute('aria-label', 'Hapus foto ' + (index + 1));
            remove.innerHTML = '<i class="bi bi-x-lg"></i>';
            remove.addEventListener('click', function () {
                selectedFiles.splice(index, 1);
                renderPhotos();
            });

            item.append(img, remove);
            list.appendChild(item);
        });

        const left = MAX_PHOTOS - selectedFiles.length;
        remaining.textContent = 'Sisa ' + left + ' foto';
        picker.classList.toggle('d-none', left === 0);
    }
});
