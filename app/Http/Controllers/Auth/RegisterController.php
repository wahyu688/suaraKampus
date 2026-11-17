<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // Pendaftaran publik untuk pelapor: mahasiswa (@binus.ac.id) atau dosen/staf (@binus.edu).
        // Akun petugas yang menangani laporan tetap dibuat admin.
        $isStudent = $request->isStudentEmail();

        $user = User::create([
            'name' => $request->name,
            'nim' => $isStudent ? $request->nim : null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $isStudent ? 'student' : 'employee',
            // Mahasiswa wajib verifikasi KTM dulu; dosen/staf langsung aktif
            'verification_status' => $isStudent ? 'unverified' : 'verified',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($isStudent) {
            return redirect()->route('verification.show')
                ->with('success', 'Akun berhasil dibuat. Satu langkah lagi: verifikasi KTM-mu.');
        }

        return redirect()->route('reports.index')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->name . '!');
    }
}
