<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();
        $buildingManagement = User::where('email', 'petugas@binus.edu')->first();
        $security = User::where('email', 'keamanan@binus.edu')->first();
        $counselor = User::where('email', 'konselor@binus.edu')->first();

        // Sama dengan 5 laporan di prototipe
        $reports = [
            [
                'code' => 'BN-2481',
                'category' => 'fasilitas-rusak',
                'campus' => 'Anggrek',
                'title' => 'AC tidak dingin di ruang 702',
                'location_detail' => 'Lantai 7, R. 702',
                'incident_date' => '2026-10-23',
                'description' => 'Sejak minggu lalu AC di ruang 702 hanya mengeluarkan angin dan tidak dingin. Kelas siang jadi panas dan susah fokus, terutama saat ruangan penuh.',
                'is_anonymous' => false,
                'status' => 'in_progress',
                'created_at' => '2026-10-24 09:12',
                'updates' => [
                    ['pending', 'Laporan terkirim', 'Laporan diterima dan menunggu verifikasi admin.', '2026-10-24 09:12', null],
                    ['in_progress', 'Diverifikasi & diteruskan', 'Laporan valid dan diteruskan ke Building Management Kampus Anggrek.', '2026-10-24 13:40', $buildingManagement],
                    ['in_progress', 'Sedang dikerjakan', 'Teknisi sudah mengecek unit AC. Freon dijadwalkan diisi ulang paling lambat 28 Okt 2026.', '2026-10-25 10:05', $buildingManagement],
                ],
                'message' => [$buildingManagement, 'Terima kasih atas laporannya. Unit AC sudah kami cek dan perbaikan dijadwalkan minggu ini. Sementara itu, kelas bisa dipindah ke ruang 705 kalau dibutuhkan.', '2026-10-25 10:07'],
            ],
            [
                'code' => 'BN-2479',
                'category' => 'kebersihan-sampah',
                'campus' => 'Anggrek',
                'title' => 'Tempat sampah penuh dan berbau di lantai 4',
                'location_detail' => 'Lantai 4, depan R. 403',
                'incident_date' => '2026-10-26',
                'description' => 'Tempat sampah di depan ruang 403 sudah penuh sejak Senin dan mulai berbau. Sampahnya berserakan ke lantai, padahal jalur ini ramai dilewati mahasiswa.',
                'is_anonymous' => false,
                'status' => 'pending',
                'created_at' => '2026-10-26 07:48',
                'updates' => [
                    ['pending', 'Laporan terkirim', 'Laporan diterima dan sedang menunggu verifikasi admin.', '2026-10-26 07:48', null],
                ],
                'message' => null,
            ],
            [
                'code' => 'BN-2455',
                'category' => 'keamanan-kampus',
                'campus' => 'Syahdan',
                'title' => 'Lampu koridor mati di lantai 2',
                'location_detail' => 'Lantai 2, koridor timur',
                'incident_date' => '2026-10-20',
                'description' => 'Tiga lampu di koridor timur lantai 2 mati sejak minggu lalu. Kalau pulang kelas malam, koridornya gelap dan terasa tidak aman.',
                'is_anonymous' => false,
                'status' => 'in_progress',
                'created_at' => '2026-10-20 18:30',
                'updates' => [
                    ['pending', 'Laporan terkirim', 'Laporan diterima dan menunggu verifikasi admin.', '2026-10-20 18:30', null],
                    ['in_progress', 'Diverifikasi & diteruskan', 'Laporan valid dan diteruskan ke tim keamanan Kampus Syahdan.', '2026-10-21 08:15', $security],
                    ['in_progress', 'Sedang dikerjakan', 'Lampu pengganti sedang dipesan. Patroli malam di koridor ini ditambah sementara.', '2026-10-22 09:40', $security],
                ],
                'message' => [$security, 'Terima kasih sudah melapor. Sambil menunggu lampu baru, petugas akan lebih sering berpatroli di lantai 2 setiap malam.', '2026-10-22 09:45'],
            ],
            [
                'code' => 'BN-2410',
                'category' => 'konflik-mahasiswa',
                'campus' => 'Anggrek',
                'title' => 'Konflik pembagian tugas kelompok',
                'location_detail' => 'Kelompok tugas mata kuliah',
                'incident_date' => '2026-10-12',
                'description' => 'Ada anggota kelompok yang tidak pernah mengerjakan bagiannya, dan setiap ditegur malah marah di grup. Saya bingung harus bagaimana tanpa memperkeruh suasana.',
                'is_anonymous' => true,
                'status' => 'resolved',
                'rating' => 5,
                'resolved_at' => '2026-10-16 10:20',
                'created_at' => '2026-10-12 21:05',
                'updates' => [
                    ['pending', 'Laporan terkirim', 'Laporan diterima. Identitasmu disembunyikan dari petugas.', '2026-10-12 21:05', null],
                    ['in_progress', 'Diteruskan ke konselor', 'Laporan sensitif hanya dibaca oleh tim konselor kampus.', '2026-10-13 09:00', $counselor],
                    ['in_progress', 'Ditangani konselor', 'Sesi konseling dan mediasi kelompok sudah dilakukan.', '2026-10-14 13:00', $counselor],
                    ['resolved', 'Selesai', 'Laporan ditutup oleh konselor.', '2026-10-16 10:20', $counselor],
                ],
                'message' => [$counselor, 'Senang mendengar mediasinya berjalan baik. Kalau masalahnya muncul lagi, kamu bisa membalas laporan ini kapan saja.', '2026-10-16 10:15'],
            ],
            [
                'code' => 'BN-2388',
                'category' => 'fasilitas-rusak',
                'campus' => 'Kijang',
                'title' => 'Wastafel toilet lantai 3 bocor',
                'location_detail' => 'Toilet lantai 3',
                'incident_date' => '2026-10-08',
                'description' => 'Pipa di bawah wastafel paling ujung bocor. Lantainya jadi licin dan sempat ada yang terpeleset.',
                'is_anonymous' => false,
                'status' => 'resolved',
                'resolved_at' => '2026-10-09 16:30',
                'created_at' => '2026-10-08 11:20',
                'updates' => [
                    ['pending', 'Laporan terkirim', 'Laporan diterima dan menunggu verifikasi admin.', '2026-10-08 11:20', null],
                    ['in_progress', 'Diverifikasi & diteruskan', 'Laporan valid dan diteruskan ke Building Management Kampus Kijang.', '2026-10-08 14:05', $buildingManagement],
                    ['in_progress', 'Ditangani teknisi', 'Pipa wastafel sudah diganti dan lantai sudah dikeringkan.', '2026-10-09 10:00', $buildingManagement],
                    ['resolved', 'Selesai', 'Perbaikan selesai, laporan ditutup.', '2026-10-09 16:30', $buildingManagement],
                ],
                'message' => [$buildingManagement, 'Perbaikan sudah selesai. Terima kasih sudah membantu menjaga fasilitas kampus tetap aman.', '2026-10-09 16:35'],
            ],
        ];

        foreach ($reports as $data) {
            // forceCreate boleh mengisi kolom di luar $fillable (code, status, created_at, ...)
            $report = $student->reports()->forceCreate([
                'code' => $data['code'],
                'category_id' => Category::where('slug', $data['category'])->first()->id,
                'campus_id' => Campus::where('name', $data['campus'])->first()->id,
                'title' => $data['title'],
                'location_detail' => $data['location_detail'],
                'incident_date' => $data['incident_date'],
                'description' => $data['description'],
                'is_anonymous' => $data['is_anonymous'],
                'status' => $data['status'],
                'rating' => $data['rating'] ?? null,
                'resolved_at' => $data['resolved_at'] ?? null,
                'created_at' => $data['created_at'],
                'updated_at' => $data['created_at'],
            ]);

            foreach ($data['updates'] as [$status, $title, $note, $time, $officer]) {
                $report->updates()->forceCreate([
                    'user_id' => $officer?->id,
                    'status' => $status,
                    'title' => $title,
                    'note' => $note,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }

            if ($data['message']) {
                [$officer, $body, $time] = $data['message'];

                $report->messages()->forceCreate([
                    'user_id' => $officer->id,
                    'body' => $body,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }
}
