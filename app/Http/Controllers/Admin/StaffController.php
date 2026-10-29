<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Admin mengelola akun petugas. Petugas tidak bisa mendaftar sendiri.
class StaffController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role', ['staff', 'counselor', 'admin'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', ['staff' => $staff]);
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request)
    {
        // Password sementara acak; petugas wajib menggantinya saat login pertama
        $temporaryPassword = Str::password(10, symbols: false);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Akun ' . $user->name . ' dibuat.')
            ->with('temporary_password', $temporaryPassword)
            ->with('new_staff_email', $user->email);
    }
}
