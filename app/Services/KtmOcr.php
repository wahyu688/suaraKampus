<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Throwable;

// Membaca foto KTM dengan Tesseract OCR lalu mengecek apakah NIM yang diketik ada di kartu.
// Tesseract adalah program terpisah (bukan package Composer), dipanggil lewat facade Process bawaan Laravel.
class KtmOcr
{
    public const MATCHED = 'matched';         // NIM dan kata BINUS terbaca
    public const NOT_MATCHED = 'not_matched'; // Terbaca, tapi NIM tidak ditemukan
    public const UNAVAILABLE = 'unavailable'; // Tesseract tidak terpasang / error di server

    public function check(string $imagePath, string $nim): string
    {
        if (! $this->isAvailable()) {
            return self::UNAVAILABLE;
        }

        // Foto KTM sering miring (difoto vertikal), jadi dicoba dalam 4 arah putaran
        foreach ([0, 90, 270, 180] as $angle) {
            $file = $this->prepareImage($imagePath, $angle);

            if ($file === null) {
                // GD tidak tersedia atau gambar rusak: baca file aslinya saja, sekali
                return $this->textContainsNim($this->readText($imagePath), $nim)
                    ? self::MATCHED
                    : self::NOT_MATCHED;
            }

            $text = $this->readText($file);
            @unlink($file);

            if ($this->textContainsNim($text, $nim)) {
                return self::MATCHED;
            }
        }

        return self::NOT_MATCHED;
    }

    public function isAvailable(): bool
    {
        try {
            return Process::timeout(10)->run([config('suarakampus.tesseract_path'), '--version'])->successful();
        } catch (Throwable) {
            return false;
        }
    }

    // Mengecek teks hasil OCR. Dibuat public supaya bisa diuji tanpa Tesseract.
    public function textContainsNim(string $text, string $nim): bool
    {
        // Hapus spasi, garis miring, dll. "/2802 497 800" menjadi "2802497800"
        $compact = preg_replace('/[^A-Z0-9]/', '', strtoupper($text));

        // OCR sering salah membaca angka sebagai huruf yang mirip
        $digitLike = strtr($compact, ['O' => '0', 'D' => '0', 'Q' => '0', 'I' => '1', 'L' => '1', 'S' => '5', 'B' => '8', 'Z' => '2', 'G' => '6']);

        $hasBinus = str_contains($compact, 'BINUS') || str_contains($digitLike, '81NU5');

        return $hasBinus && str_contains($digitLike, $nim);
    }

    private function readText(string $path): string
    {
        try {
            // --psm 11 = cari teks yang tersebar (cocok untuk kartu, bukan paragraf)
            $result = Process::timeout(30)->run([
                config('suarakampus.tesseract_path'), $path, 'stdout', '-l', 'eng', '--psm', '11',
            ]);

            return $result->successful() ? $result->output() : '';
        } catch (Throwable $e) {
            Log::warning('OCR KTM gagal: ' . $e->getMessage());

            return '';
        }
    }

    // Membuat salinan sementara: diputar, diperkecil (maks. 2000px), dan dijadikan abu-abu supaya OCR lebih akurat
    private function prepareImage(string $path, int $angle): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $image = @imagecreatefromstring((string) file_get_contents($path));
        if ($image === false) {
            return null;
        }

        if (imagesx($image) > 2000) {
            $image = imagescale($image, 2000);
        }
        if ($angle !== 0) {
            $image = imagerotate($image, $angle, 0);
        }
        imagefilter($image, IMG_FILTER_GRAYSCALE);

        // tempnam() membuat file kosong; dihapus lalu dipakai namanya dengan akhiran .png
        $base = tempnam(sys_get_temp_dir(), 'ktm');
        @unlink($base);
        $file = $base . '.png';
        imagepng($image, $file);

        return $file;
    }
}
