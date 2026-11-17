<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

        // Mahasiswa yang KTM-nya menunggu dicek admin, supaya halaman Verifikasi KTM punya isi saat demo
        User::create([
            'name' => 'Raka Mahendra',
            'email' => 'belumverif@binus.ac.id',
            'nim' => '2702000002',
            'role' => 'student',
            'password' => Hash::make('password'),
            'verification_status' => 'pending',
            'ktm_attempts' => 3,
            'ktm_path' => $this->makeDemoKtm('Raka Mahendra', '2702000002'),
        ]);
    }

    // Membuat gambar KTM palsu sederhana dengan GD (bukan kartu asli)
    private function makeDemoKtm(string $name, string $nim): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $image = imagecreatetruecolor(320, 200);
        imagefill($image, 0, 0, imagecolorallocate($image, 0, 127, 204));
        imagefilledrectangle($image, 0, 130, 320, 200, imagecolorallocate($image, 255, 255, 255));

        $white = imagecolorallocate($image, 255, 255, 255);
        $navy = imagecolorallocate($image, 7, 49, 74);
        imagestring($image, 5, 16, 16, 'BINUS UNIVERSITY', $white);
        imagestring($image, 3, 16, 40, 'CONTOH KTM - DATA DEMO', $white);
        imagestring($image, 5, 16, 140, strtoupper($name), $navy);
        imagestring($image, 5, 16, 165, $nim, $navy);

        // Diperbesar 3x supaya tulisannya jelas
        $image = imagescale($image, 960, 600, IMG_NEAREST_NEIGHBOUR);

        ob_start();
        imagepng($image);
        $path = 'ktm/demo-' . $nim . '.png';
        Storage::disk('local')->put($path, ob_get_clean());

        return $path;
    }
}
