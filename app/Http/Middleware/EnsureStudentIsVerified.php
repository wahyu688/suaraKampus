<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Mahasiswa yang KTM-nya belum terverifikasi belum boleh membuka halaman laporan.
// Dipakai di route sebagai middleware 'ktm.verified'. Dosen/staf dan petugas dilewatkan.
class EnsureStudentIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->needsKtmVerification()) {
            return redirect()->route('verification.show')
                ->with('error', 'Verifikasi KTM dulu sebelum membuat atau melihat laporan.');
        }

        return $next($request);
    }
}
