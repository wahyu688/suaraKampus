<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportMessageController;
use App\Http\Controllers\ReportRatingController;
use App\Http\Controllers\Staff\ReportController as StaffReportController;
use App\Http\Controllers\Staff\ReportMessageController as StaffReportMessageController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bantuan', [HelpController::class, 'index'])->name('help.index');

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Semua user yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifikasi/{id}', [NotificationController::class, 'open'])->name('notifications.open');
});

// Pelapor (mahasiswa + dosen/staf): semua URL diawali /laporan dan nama route diawali reports.
Route::middleware(['auth', 'role:student,employee'])->prefix('laporan')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/buat', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');

    // {report:code} = route model binding lewat kolom code, misalnya /laporan/BN-2481.
    // Ditulis setelah /buat supaya "buat" tidak dianggap sebagai kode laporan.
    Route::get('/{report:code}', [ReportController::class, 'show'])->name('show');
    Route::patch('/{report:code}/batal', [ReportController::class, 'cancel'])->name('cancel');
    Route::post('/{report:code}/pesan', [ReportMessageController::class, 'store'])->name('messages.store');
    Route::post('/{report:code}/penilaian', [ReportRatingController::class, 'store'])->name('rating.store');
});

// Petugas, konselor, admin: URL diawali /petugas dan nama route diawali staff.
Route::middleware(['auth', 'role:staff,counselor,admin'])->prefix('petugas')->name('staff.')->group(function () {
    Route::get('/laporan', [StaffReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/{report:code}', [StaffReportController::class, 'show'])->name('reports.show');
    Route::patch('/laporan/{report:code}/status', [StaffReportController::class, 'updateStatus'])->name('reports.status');
    Route::post('/laporan/{report:code}/pesan', [StaffReportMessageController::class, 'store'])->name('reports.messages.store');
});

// Admin: kelola akun petugas dan kategori
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/petugas', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/petugas/tambah', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/petugas', [StaffController::class, 'store'])->name('staff.store');

    // Satu baris = 6 route CRUD (index, create, store, edit, update, destroy).
    // URL memakai "kategori", nama route memakai "categories" (admin.categories.index, dst.)
    Route::resource('kategori', CategoryController::class)
        ->except('show')
        ->names('categories')
        ->parameters(['kategori' => 'category']);
});
