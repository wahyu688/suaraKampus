<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Menguji aturan bisnis di CLAUDE.md bagian 7.
// Database tes memakai SQLite di memori (phpunit.xml), jadi data MySQL tidak tersentuh.
class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    // Jalankan DatabaseSeeder sebelum setiap tes (akun demo + 5 laporan contoh)
    protected $seed = true;

    private function user(string $name): User
    {
        $emails = [
            'mahasiswa' => 'mahasiswa@binus.ac.id',
            'petugas' => 'petugas@binus.edu',
            'konselor' => 'konselor@binus.edu',
            'admin' => 'admin@binus.edu',
        ];

        return User::where('email', $emails[$name])->first();
    }

    private function report(string $code): Report
    {
        return Report::where('code', $code)->first();
    }

    private function validReport(array $override = []): array
    {
        return array_merge([
            'category_id' => 2,
            'campus_id' => 1,
            'title' => 'Proyektor lab 601 berkedip',
            'location_detail' => 'Lab 601',
            'incident_date' => now()->toDateString(),
            'description' => 'Proyektor di lab 601 berkedip terus sejak kemarin siang.',
            'agreement' => '1',
        ], $override);
    }

    // PNG 1x1 piksel asli, supaya lolos aturan "image" tanpa butuh ekstensi GD
    private function fakePhoto(string $name): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    // Aturan 1: kode BN- dibuat otomatis dan berurutan
    public function test_rule_1_report_code_continues_from_the_last_number(): void
    {
        $this->actingAs($this->user('mahasiswa'))->post(route('reports.store'), $this->validReport());

        $this->assertDatabaseHas('reports', ['code' => 'BN-2482', 'title' => 'Proyektor lab 601 berkedip']);
    }

    // Aturan 1: kalau belum ada laporan sama sekali, nomornya mulai dari 2400
    public function test_rule_1_first_report_code_starts_at_2400(): void
    {
        Report::query()->delete();

        $report = $this->user('mahasiswa')->reports()->create([
            'category_id' => 1,
            'campus_id' => 1,
            'title' => 'Laporan pertama',
            'location_detail' => 'Lobi',
            'incident_date' => now()->toDateString(),
            'description' => 'Laporan pertama setelah database dikosongkan.',
        ]);

        $this->assertSame('BN-2400', $report->code);
    }

    // Aturan 2: laporan baru otomatis punya riwayat "Laporan terkirim"
    public function test_rule_2_new_report_gets_first_timeline_entry(): void
    {
        $this->actingAs($this->user('mahasiswa'))->post(route('reports.store'), $this->validReport());

        $updates = $this->report('BN-2482')->updates;

        $this->assertCount(1, $updates);
        $this->assertSame('pending', $updates->first()->status);
        $this->assertSame('Laporan terkirim', $updates->first()->title);
    }

    // Aturan 3: foto disimpan di disk public folder reports/
    public function test_rule_3_photos_are_stored_on_public_disk(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user('mahasiswa'))->post(route('reports.store'), $this->validReport([
            'photos' => [$this->fakePhoto('satu.png'), $this->fakePhoto('dua.png')],
        ]));

        $photos = $this->report('BN-2482')->photos;

        $this->assertCount(2, $photos);
        foreach ($photos as $photo) {
            $this->assertStringStartsWith('reports/', $photo->path);
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    // Aturan 3: maksimal 3 foto, dan harus gambar JPG/PNG
    public function test_rule_3_rejects_more_than_three_photos_or_non_images(): void
    {
        Storage::fake('public');
        $student = $this->user('mahasiswa');

        $this->actingAs($student)->post(route('reports.store'), $this->validReport([
            'photos' => [$this->fakePhoto('1.png'), $this->fakePhoto('2.png'), $this->fakePhoto('3.png'), $this->fakePhoto('4.png')],
        ]))->assertSessionHasErrors('photos');

        $this->actingAs($student)->post(route('reports.store'), $this->validReport([
            'photos' => [UploadedFile::fake()->createWithContent('dokumen.pdf', '%PDF-1.4 bukan gambar')],
        ]))->assertSessionHasErrors('photos.0');

        $this->assertSame(5, Report::count());
    }

    // Aturan 4: laporan anonim tidak menampilkan nama/NIM ke konselor, tapi tetap ada di Laporan Saya
    public function test_rule_4_anonymous_reporter_is_hidden_from_officers(): void
    {
        $this->actingAs($this->user('konselor'))
            ->get(route('staff.reports.show', 'BN-2410'))
            ->assertOk()
            ->assertSee('Pelapor anonim')
            ->assertDontSee('Maya Saputri')
            ->assertDontSee('2702000001');

        $this->actingAs($this->user('mahasiswa'))
            ->get(route('reports.index'))
            ->assertSee('Konflik pembagian tugas kelompok');
    }

    // Aturan 5: kategori sensitif disaring di daftar dan dicek di otorisasi
    public function test_rule_5_staff_cannot_see_sensitive_reports(): void
    {
        $staff = $this->user('petugas');

        $this->actingAs($staff)->get(route('staff.reports.index'))
            ->assertSee('BN-2481')
            ->assertDontSee('BN-2410');

        $this->actingAs($staff)->get(route('staff.reports.show', 'BN-2410'))->assertForbidden();

        $this->actingAs($this->user('konselor'))->get(route('staff.reports.index'))
            ->assertSee('BN-2410')
            ->assertDontSee('BN-2481');
    }

    // Aturan 6: mahasiswa lain mendapat 403
    public function test_rule_6_students_only_see_their_own_reports(): void
    {
        $otherStudent = User::factory()->create(['role' => 'student']);

        $this->actingAs($otherStudent)->get(route('reports.show', 'BN-2481'))->assertForbidden();
        $this->actingAs($this->user('mahasiswa'))->get(route('reports.show', 'BN-2481'))->assertOk();
    }

    // Aturan 7: pembatalan hanya saat status Menunggu
    public function test_rule_7_only_pending_reports_can_be_cancelled(): void
    {
        $student = $this->user('mahasiswa');

        $this->actingAs($student)->patch(route('reports.cancel', 'BN-2481'))->assertForbidden();
        $this->assertSame('in_progress', $this->report('BN-2481')->status);

        $this->actingAs($student)->patch(route('reports.cancel', 'BN-2479'))->assertRedirect();
        $this->assertSame('cancelled', $this->report('BN-2479')->status);
    }

    // Aturan 8: penilaian hanya saat Selesai, dan hanya sekali
    public function test_rule_8_rating_only_when_resolved_and_only_once(): void
    {
        $student = $this->user('mahasiswa');

        $this->actingAs($student)->post(route('reports.rating.store', 'BN-2481'), ['rating' => 5])->assertForbidden();

        $this->actingAs($student)->post(route('reports.rating.store', 'BN-2388'), ['rating' => 4])->assertRedirect();
        $this->assertSame(4, $this->report('BN-2388')->rating);

        $this->actingAs($student)->post(route('reports.rating.store', 'BN-2388'), ['rating' => 1])->assertForbidden();
        $this->assertSame(4, $this->report('BN-2388')->rating);
    }

    // Aturan 9: ubah status wajib dengan catatan, dan tercatat di timeline
    public function test_rule_9_status_change_requires_note_and_is_logged(): void
    {
        $staff = $this->user('petugas');
        $url = route('staff.reports.status', 'BN-2479');

        $this->actingAs($staff)
            ->patch($url, ['status' => 'in_progress', 'title' => 'Diverifikasi & diteruskan'])
            ->assertSessionHasErrors('note');
        $this->assertSame('pending', $this->report('BN-2479')->status);

        $this->actingAs($staff)->patch($url, [
            'status' => 'in_progress',
            'title' => 'Diverifikasi & diteruskan',
            'note' => 'Diteruskan ke cleaning service lantai 4.',
        ])->assertRedirect();

        $this->assertSame('in_progress', $this->report('BN-2479')->status);
        $this->assertDatabaseHas('report_updates', [
            'report_id' => $this->report('BN-2479')->id,
            'user_id' => $staff->id,
            'status' => 'in_progress',
            'note' => 'Diteruskan ke cleaning service lantai 4.',
        ]);
    }

    // Aturan 10: kategori wajib dipilih
    public function test_rule_10_category_is_required(): void
    {
        $this->actingAs($this->user('mahasiswa'))
            ->post(route('reports.store'), $this->validReport(['category_id' => null]))
            ->assertSessionHasErrors('category_id');

        $this->assertSame(5, Report::count());
    }

    // Aturan 11: kategori sensitif mencentang mode anonim secara default
    public function test_rule_11_sensitive_category_suggests_anonymous_mode(): void
    {
        $student = $this->user('mahasiswa');

        $sensitive = $this->actingAs($student)->get(route('reports.create', ['kategori' => 'perundungan']))->getContent();
        $this->assertMatchesRegularExpression('/id="is_anonymous"[^>]*checked/', $sensitive);

        $regular = $this->actingAs($student)->get(route('reports.create', ['kategori' => 'fasilitas-rusak']))->getContent();
        $this->assertDoesNotMatchRegularExpression('/id="is_anonymous"[^>]*checked/', $regular);
    }
}
