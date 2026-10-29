<?php

namespace App\Http\Controllers;

class HelpController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'Apakah identitasku aman kalau melapor?',
                'answer' => 'Kalau kamu menyalakan mode anonim, nama dan NIM tidak ditampilkan ke petugas. Laporan sensitif seperti perundungan dan konflik hanya bisa dibaca tim konselor kampus.',
            ],
            [
                'question' => 'Berapa lama laporanku ditangani?',
                'answer' => 'Laporan diverifikasi admin paling lambat 1×24 jam pada hari kerja. Setelah itu, lama penanganan tergantung jenis masalah dan unit yang menanganinya. Semua perkembangan bisa kamu pantau di Laporan Saya.',
            ],
            [
                'question' => 'Siapa yang menangani laporanku?',
                'answer' => 'Laporan diteruskan ke unit yang sesuai: pengelola gedung untuk fasilitas dan kebersihan, keamanan kampus untuk kasus keamanan, dan konselor untuk perundungan, konflik, atau curhat akademik.',
            ],
            [
                'question' => 'Bisakah laporan dibatalkan?',
                'answer' => 'Bisa, selama statusnya masih Menunggu. Buka Laporan Saya, pilih laporannya, lalu tekan Batalkan Laporan.',
            ],
            [
                'question' => 'Bagaimana kalau keadaannya darurat?',
                'answer' => 'Jangan menunggu balasan lewat SuaraKampus. Segera hubungi keamanan kampus terdekat atau layanan darurat 112.',
            ],
        ];

        return view('help.index', ['faqs' => $faqs]);
    }
}
