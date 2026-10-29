<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Maya Saputri', 'email' => 'mahasiswa@binus.ac.id', 'nim' => '2702000001', 'role' => 'student'],
            ['name' => 'Dosen Demo', 'email' => 'dosen@binus.edu', 'nim' => null, 'role' => 'employee'],
            ['name' => 'Building Management', 'email' => 'petugas@binus.edu', 'nim' => null, 'role' => 'staff'],
            ['name' => 'Keamanan Kampus', 'email' => 'keamanan@binus.edu', 'nim' => null, 'role' => 'staff'],
            ['name' => 'Konselor Kampus', 'email' => 'konselor@binus.edu', 'nim' => null, 'role' => 'counselor'],
            ['name' => 'Admin SuaraKampus', 'email' => 'admin@binus.edu', 'nim' => null, 'role' => 'admin'],
        ];

        foreach ($users as $user) {
            User::create([
                ...$user,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
