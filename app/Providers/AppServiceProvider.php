<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tombol halaman ($reports->links()) memakai gaya Bootstrap, bukan Tailwind bawaan
        Paginator::useBootstrapFive();

        // URL Route::resource memakai bahasa Indonesia: /kategori/tambah dan /kategori/{id}/ubah
        Route::resourceVerbs([
            'create' => 'tambah',
            'edit' => 'ubah',
        ]);
    }
}
