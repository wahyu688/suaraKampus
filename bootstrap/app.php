<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway membuka HTTPS di proxy lalu meneruskan ke Laravel lewat HTTP.
        // Percayai header X-Forwarded-* supaya asset() dan route() memakai https.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'ktm.verified' => \App\Http\Middleware\EnsureStudentIsVerified::class,
        ]);

        // Dijalankan di semua halaman web (bukan hanya route tertentu)
        $middleware->web(append: [
            \App\Http\Middleware\EnsurePasswordIsChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
