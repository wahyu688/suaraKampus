# CLAUDE.md — SuaraKampus

Panduan untuk Claude (dan anggota kelompok) saat mengerjakan proyek ini. Baca seluruh file ini sebelum menulis kode.

## 1. Tentang proyek

**SuaraKampus** adalah website pengaduan untuk mahasiswa BINUS. Lewat website ini mahasiswa bisa:

- melaporkan masalah di lingkungan kampus: sampah, fasilitas rusak, kebersihan, keamanan;
- menceritakan masalah pribadi di kampus: konflik antar mahasiswa, perundungan (bullying), curhat akademik;
- melampirkan deskripsi dan foto, lalu memantau status laporan sampai selesai.

Proyek ini terkait **SDG 16 (Peace, Justice and Strong Institutions)** karena menyediakan saluran pengaduan yang mudah, aman, dan transparan. Target pengguna adalah mahasiswa BINUS yang aktif beraktivitas di area kampus.

Ini **proyek kelompok mata kuliah**. Tujuannya bukan hanya aplikasi jadi, tapi juga menunjukkan penguasaan materi Laravel yang diajarkan di kelas (lihat bagian 3).

## 2. Tech stack

- **Laravel 12 atau 13.** Ikuti versi yang dipakai di kelas. Laravel 13 butuh PHP 8.3 atau lebih baru.
- **PHP 8.3+**, **Composer**
- **MySQL / MariaDB**, misalnya lewat XAMPP atau Laragon
- **Blade** untuk semua tampilan, tanpa framework JavaScript
- **Bootstrap 5** lewat CDN, ditambah `public/css/suarakampus.css` untuk design token (bagian 8). Kalau kelompok memutuskan pakai Tailwind, ubah baris ini dulu.
- JavaScript hanya vanilla dan seperlunya, misalnya pratinjau foto sebelum upload.

Jangan menambah package Composer atau npm di luar Laravel bawaan tanpa persetujuan kelompok. Jangan pakai Livewire, Inertia, React, Vue, Filament, atau starter kit. Autentikasi ditulis manual dengan controller supaya materinya terlihat.

## 3. Materi kelas yang harus terlihat di kode

Setiap fitur sebaiknya memakai konsep berikut, karena ini yang dinilai:

- **MVC dan routing:** `routes/web.php`, route bernama (`->name()`), route group, prefix, middleware, route model binding
- **Controller:** controller biasa dan resource-style (`index`, `create`, `store`, `show`, `update`)
- **Blade:** layout (`@extends`/`@section` atau komponen `<x-layout>`), `@include`, komponen Blade, `@foreach`, `@if`, `@csrf`, `@method`, `@error`, `old()`
- **Migration, seeder, factory**
- **Eloquent:** model, relasi (`belongsTo`, `hasMany`), eager loading (`with()`), scope, accessor, casts
- **Validasi:** Form Request (`php artisan make:request`)
- **Autentikasi manual:** `Auth::attempt()`, `Auth::logout()`, hashing password, session regenerate
- **Middleware custom** untuk role
- **Otorisasi:** cek kepemilikan laporan (Policy atau `abort(403)`)
- **Upload file:** `Storage`, disk `public`, `php artisan storage:link`
- **Session flash message** dan **pagination**

**Cara Claude bekerja di proyek ini:**
- Utamakan kode yang sederhana dan mudah dijelaskan saat presentasi, bukan yang paling ringkas.
- Setiap kali memakai konsep baru, jelaskan singkat dalam bahasa Indonesia apa fungsinya.
- Pakai perintah `php artisan make:...` untuk membuat file, lalu sebutkan perintahnya.
- Jangan menulis query SQL mentah. Pakai Eloquent.
- Kerjakan satu fitur per langkah, dan tunjukkan cara mengujinya di browser.

## 4. Konvensi

- **Bahasa:** nama class, method, variabel, tabel, dan kolom pakai **bahasa Inggris**. Semua teks yang dilihat pengguna (label, tombol, pesan error, flash message) pakai **bahasa Indonesia**.
- **Tabel:** jamak, snake_case (`reports`, `report_photos`). **Model:** tunggal, PascalCase (`Report`).
- **Route name:** `reports.index`, `reports.create`, `staff.reports.show`, dan seterusnya.
- **View:** `resources/views/{fitur}/{aksi}.blade.php`, misalnya `reports/create.blade.php`.
- **Komentar kode:** bahasa Indonesia, singkat, hanya untuk logika yang tidak jelas.
- **Git:** satu branch per fitur (`fitur/buat-laporan`), commit kecil dan jelas, merge lewat pull request. Jangan commit `.env`, `vendor/`, `node_modules/`, atau `storage/app/public/` berisi upload.

## 5. Peran pengguna (role)

| Role | Bisa apa |
|---|---|
| `student` | Membuat laporan, melihat laporan miliknya sendiri, membatalkan laporan yang masih menunggu, mengirim pesan, memberi penilaian |
| `employee` | Dosen/staf BINUS yang ikut melapor. Haknya sama dengan `student`, tapi tanpa NIM. **Tidak** bisa melihat laporan orang lain |
| `staff` | Petugas, misalnya pengelola gedung atau keamanan. Melihat dan menangani laporan kategori **non-sensitif**, mengubah status, membalas |
| `counselor` | Konselor kampus. Melihat dan menangani laporan kategori **sensitif** |
| `admin` | Melihat semua laporan, mengelola kategori dan pengguna |

### Akun dan email BINUS

- Website ini khusus warga BINUS. Domain email diatur di `config/suarakampus.php`.
- **Mahasiswa** daftar sendiri lewat `/register` dengan email `@binus.ac.id` dan wajib mengisi NIM (role `student`).
- **Dosen/staf BINUS** juga bisa daftar sendiri dengan email `@binus.edu`, tanpa NIM (role `employee`). Mereka hanya bisa melapor, bukan menangani laporan.
- **Petugas, konselor, dan admin tidak bisa daftar sendiri.** Akunnya dibuat admin di halaman Kelola Petugas, dengan email `@binus.ac.id` atau `@binus.edu`.
- Akun buatan admin mendapat password sementara acak (`must_change_password = true`). Middleware `EnsurePasswordIsChanged` memaksa pemiliknya mengganti password sebelum bisa membuka halaman lain.
- Login hanya menerima email dengan domain di atas. Email diubah ke huruf kecil sebelum divalidasi.

## 6. Database

### Tabel

**users** (tambahan dari bawaan Laravel)
- `nim` string, nullable, unik. Diisi untuk mahasiswa.
- `role` enum: `student`, `employee`, `staff`, `counselor`, `admin`. Default `student`.
- `must_change_password` boolean, default `false`. `true` untuk akun petugas yang baru dibuat admin.

**campuses**
- `id`, `name`. Isi: Anggrek, Syahdan, Kijang, Alam Sutera, Bekasi, Senayan.

**categories**
- `id`, `name`, `slug`, `description`
- `is_sensitive` boolean. `true` untuk Perundungan, Konflik Antar Mahasiswa, dan Curhat Akademik.
- `icon` string, nama ikon.

**reports**
- `id`
- `code` string unik, format `BN-XXXX`, misalnya `BN-2481`
- `user_id` → users
- `category_id` → categories
- `campus_id` → campuses
- `title` string(80)
- `location_detail` string, misalnya "Lantai 4, depan R. 403"
- `incident_date` date
- `description` text(1000)
- `is_anonymous` boolean
- `status` enum: `pending`, `in_progress`, `resolved`, `cancelled`. Default `pending`.
- `rating` tinyint nullable (1–5), `rating_comment` text nullable
- `resolved_at` timestamp nullable
- timestamps

**report_photos**
- `id`, `report_id`, `path`, timestamps

**report_updates** (riwayat timeline)
- `id`, `report_id`, `user_id` nullable (petugas yang mengubah)
- `status` enum yang sama dengan `reports.status`
- `title` string, misalnya "Diverifikasi & diteruskan"
- `note` text nullable
- timestamps

**report_messages** (balasan petugas dan pesan mahasiswa)
- `id`, `report_id`, `user_id`, `body` text, timestamps

### Relasi Eloquent

- `User` hasMany `Report`
- `Report` belongsTo `User`, `Category`, `Campus`
- `Report` hasMany `ReportPhoto`, `ReportUpdate`, `ReportMessage`
- `Category` dan `Campus` hasMany `Report`

### Label status untuk tampilan

Simpan nilai dalam bahasa Inggris, lalu tampilkan dengan accessor `status_label` di model `Report`:

| Nilai | Label | Warna badge |
|---|---|---|
| `pending` | Menunggu | oranye `#C96F00` di atas `#FFF1DF` |
| `in_progress` | Diproses | biru `#007FCC` di atas `#E0F3FE` |
| `resolved` | Selesai | hijau `#1E9E5A` di atas `#E3F6EC` |
| `cancelled` | Dibatalkan | abu `#5E5E5E` di atas `#F1F3F6` |

## 7. Aturan bisnis

1. **Kode laporan** dibuat otomatis saat laporan disimpan, misalnya lewat event `creating` di model: `BN-` diikuti nomor berurutan, mulai dari 2400.
2. Setiap laporan baru otomatis membuat `report_updates` pertama: status `pending`, judul "Laporan terkirim".
3. **Foto:** maksimal 3 file per laporan, format `jpg`, `jpeg`, atau `png`, maksimal 5 MB per file. Simpan di disk `public`, folder `reports/`.
4. **Anonim:** kalau `is_anonymous` bernilai `true`, nama dan NIM pelapor **tidak boleh tampil** di halaman staff maupun counselor. `user_id` tetap disimpan supaya laporan muncul di "Laporan Saya".
5. **Kategori sensitif** hanya bisa dilihat oleh `counselor` dan `admin`. `staff` tidak boleh melihatnya, dan ini dicek di query dan otorisasi, bukan hanya disembunyikan di tampilan.
6. Mahasiswa **hanya bisa melihat laporannya sendiri**. Akses ke laporan orang lain mengembalikan 403.
7. **Pembatalan** hanya boleh saat status `pending`.
8. **Penilaian** hanya boleh saat status `resolved`, dan hanya sekali.
9. Setiap perubahan status oleh petugas **wajib** membuat entri `report_updates` baru dengan catatan.
10. Pilihan kategori di form wajib tepat satu.
11. Saat pengguna memilih kategori sensitif, sarankan mode anonim. Cukup dengan mencentang kotaknya secara default lewat JavaScript; pengguna tetap boleh mematikannya.

## 8. Design system

Desain lengkap ada di file prototipe kelompok: `SuaraKampus-Desktop-Mobile.figcraft` (versi `.fig` untuk Figma, dan PNG tiap layar). Gaya desainnya mengikuti BINUS Maya.

### Warna (taruh sebagai CSS variable di `public/css/suarakampus.css`)

```css
:root {
  --sk-primary: #007FCC;
  --sk-primary-light: #0097DA;
  --sk-primary-hover: #006BAD;
  --sk-accent: #F3931B;
  --sk-navy: #07314A;
  --sk-navy-card: #0B4466;
  --sk-tint: #E0F3FE;
  --sk-bg: #F5F7FA;
  --sk-border: #E3E7EB;
  --sk-input-border: #D5D9DE;
  --sk-text: #212121;
  --sk-muted: #5E5E5E;
  --sk-success: #1E9E5A;
  --sk-danger: #D93025;
  --sk-hero-gradient: linear-gradient(115deg, #007FCC 0%, #56C6F2 100%);
}
```

### Tipografi dan bentuk

- **Font:** Open Sans dari Google Fonts, ketebalan 400, 600, 700, 800.
- **Judul hero:** 50px ExtraBold di desktop, 30px di mobile. **Judul section:** 36px, dengan satu kata dibuat tebal, misalnya "Mau **melapor** apa hari ini?". Label kecil di atas judul memakai huruf kapital 13px abu-abu.
- **Tombol:** bentuk pil (`border-radius: 999px`), teks KAPITAL Bold 14px. Tombol utama biru, tombol sekunder outline biru.
- **Kartu:** `border-radius: 12px`, garis tepi `--sk-border`, latar putih.
- **Input:** tinggi 48px, `border-radius: 8px`.

### Layout responsif

- **Desktop (≥ 768px):** navbar atas berisi logo SuaraKampus, menu Beranda / Buat Laporan / Laporan Saya / Bantuan, lonceng notifikasi, dan avatar. Lebar konten maksimal 1280px.
- **Mobile (< 768px):** top bar (logo + lonceng, atau tombol kembali + judul) dan **tab bar bawah** berisi Beranda, Laporan, tombol bulat **Lapor** di tengah, Notifikasi, dan Bantuan.
- Buat satu view per halaman yang responsif. Jangan membuat view terpisah untuk mobile.

### Komponen Blade yang disarankan

`<x-navbar>`, `<x-mobile-tabbar>`, `<x-status-badge :status="...">`, `<x-report-card :report="...">`, `<x-report-timeline :updates="...">`, `<x-category-card :category="...">`, `<x-flash>`

## 9. Halaman dan route

| Halaman | Method & URL | Controller | Akses |
|---|---|---|---|
| Beranda | `GET /` | `HomeController@index` | publik |
| Bantuan (FAQ) | `GET /bantuan` | `HelpController@index` | publik |
| Login / Daftar | `GET/POST /login`, `GET/POST /register` | `Auth\LoginController`, `Auth\RegisterController` | tamu |
| Logout | `POST /logout` | `Auth\LoginController@logout` | login |
| Laporan Saya | `GET /laporan` | `ReportController@index` | student, employee |
| Buat Laporan | `GET /laporan/buat` | `ReportController@create` | student, employee |
| Simpan laporan | `POST /laporan` | `ReportController@store` | student, employee |
| Detail laporan | `GET /laporan/{report:code}` | `ReportController@show` | pemilik |
| Batalkan | `PATCH /laporan/{report:code}/batal` | `ReportController@cancel` | pemilik |
| Kirim pesan | `POST /laporan/{report:code}/pesan` | `ReportMessageController@store` | pemilik |
| Beri penilaian | `POST /laporan/{report:code}/penilaian` | `ReportRatingController@store` | pemilik |
| Daftar laporan petugas | `GET /petugas/laporan` | `Staff\ReportController@index` | staff, counselor, admin |
| Detail laporan petugas | `GET /petugas/laporan/{report:code}` | `Staff\ReportController@show` | staff, counselor, admin |
| Ubah status | `PATCH /petugas/laporan/{report:code}/status` | `Staff\ReportController@updateStatus` | staff, counselor, admin |
| Balas laporan | `POST /petugas/laporan/{report:code}/pesan` | `Staff\ReportMessageController@store` | staff, counselor, admin |
| Ubah password | `GET/PUT /password` | `Auth\PasswordController@edit`, `@update` | login |
| Kelola petugas | `GET /admin/petugas`, `GET /admin/petugas/tambah`, `POST /admin/petugas` | `Admin\StaffController@index`, `@create`, `@store` | admin |

Detail perilaku yang perlu ditiru dari prototipe:

- **Beranda:** klik kartu kategori membuka `/laporan/buat?kategori={slug}` dengan kategori itu sudah terpilih.
- **Laporan Saya:** tab Semua / Menunggu / Diproses / Selesai memakai query string `?status=`, dan setiap tab menampilkan jumlahnya. Di desktop, klik kartu menampilkan detail di panel kanan lewat `?laporan={code}`. Di mobile, klik kartu membuka halaman detail.
- **Form laporan:** chip kategori adalah `radio` yang diberi gaya, toggle anonim adalah `checkbox`. Kalau validasi gagal, isian lama tetap terisi lewat `old()`.
- **Setelah kirim:** redirect ke detail laporan dengan flash message "Laporan terkirim! Nomor laporanmu #BN-XXXX."
- **Bantuan:** FAQ memakai elemen `<details>`/`<summary>`, jadi tidak perlu JavaScript.

## 10. Validasi (Form Request)

`StoreReportRequest`:

```php
'category_id'     => ['required', 'exists:categories,id'],
'campus_id'       => ['required', 'exists:campuses,id'],
'title'           => ['required', 'string', 'max:80'],
'location_detail' => ['required', 'string', 'max:120'],
'incident_date'   => ['required', 'date', 'before_or_equal:today'],
'description'     => ['required', 'string', 'min:20', 'max:1000'],
'is_anonymous'    => ['nullable', 'boolean'],
'photos'          => ['nullable', 'array', 'max:3'],
'photos.*'        => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
'agreement'       => ['accepted'],
```

Pesan error ditulis dalam bahasa Indonesia lewat method `messages()` dan `attributes()`.

## 11. Data contoh (seeder)

Buat `DatabaseSeeder` yang memanggil seeder untuk kampus, kategori, pengguna, dan laporan:

- **Pengguna demo** (password semua `password`):
  - `mahasiswa@binus.ac.id`, role student, NIM `2702000001`
  - `dosen@binus.edu`, role employee (dosen yang ikut melapor)
  - `petugas@binus.edu`, role staff (Building Management)
  - `keamanan@binus.edu`, role staff (Keamanan Kampus, membalas BN-2455)
  - `konselor@binus.edu`, role counselor
  - `admin@binus.edu`, role admin
- **5 laporan contoh** milik akun mahasiswa demo, sama dengan prototipe, lengkap dengan riwayat `report_updates` dan pesan petugas:
  - `BN-2481` AC tidak dingin di ruang 702 — Fasilitas Rusak, Anggrek, **Diproses**
  - `BN-2479` Tempat sampah penuh dan berbau di lantai 4 — Kebersihan & Sampah, Anggrek, **Menunggu**
  - `BN-2455` Lampu koridor mati di lantai 2 — Keamanan Kampus, Syahdan, **Diproses**
  - `BN-2410` Konflik pembagian tugas kelompok — Konflik Antar Mahasiswa, **anonim**, **Selesai**, rating 5
  - `BN-2388` Wastafel toilet lantai 3 bocor — Fasilitas Rusak, Kijang, **Selesai**
- Pakai factory untuk data tambahan kalau perlu menguji pagination.

## 12. Menjalankan proyek

```bash
composer install
cp .env.example .env          # di Windows: copy .env.example .env
php artisan key:generate
# atur DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env, lalu buat database kosong
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Reset database dengan data contoh: `php artisan migrate:fresh --seed`

## 13. Urutan pengerjaan yang disarankan

1. Migration, model, relasi, dan seeder
2. Layout Blade, CSS design token, navbar, dan tab bar mobile
3. Autentikasi manual dan middleware role
4. Beranda dan halaman Bantuan
5. Form Buat Laporan: Form Request, upload foto, kode otomatis
6. Laporan Saya: filter status, detail, timeline, batalkan, pesan, penilaian
7. Halaman petugas: daftar, filter kategori sensitif, ubah status, balas
8. Rapikan tampilan mobile dan uji semua aturan bisnis di bagian 7

## 14. Belum termasuk (opsional, kalau waktu cukup)

Notifikasi (lonceng), simpan draf, pencarian laporan, dan dashboard statistik admin. Kerjakan setelah semua bagian 13 selesai.
