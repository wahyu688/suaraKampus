<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\KtmOcr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Menguji alur verifikasi KTM. Tesseract tidak dipanggil: KtmOcr diganti tiruan (mock).
class KtmVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function newStudent(array $attributes = []): User
    {
        return User::factory()->create([
            'role' => 'student',
            'nim' => '2702999999',
            'verification_status' => 'unverified',
            ...$attributes,
        ]);
    }

    private function fakeOcrResult(string $result): void
    {
        $this->mock(KtmOcr::class)->shouldReceive('check')->andReturn($result);
    }

    private function uploadKtm(User $user)
    {
        return $this->actingAs($user)->post(route('verification.store'), [
            'ktm' => UploadedFile::fake()->image('ktm.jpg', 800, 500),
        ]);
    }

    public function test_new_student_must_verify_ktm_before_reporting(): void
    {
        $this->post(route('register'), [
            'name' => 'Andi Pratama',
            'nim' => '2702111111',
            'email' => 'andi@binus.ac.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('verification.show'));

        $this->assertDatabaseHas('users', ['email' => 'andi@binus.ac.id', 'verification_status' => 'unverified']);

        $this->get(route('reports.create'))->assertRedirect(route('verification.show'));
        $this->get(route('reports.index'))->assertRedirect(route('verification.show'));
        $this->get(route('verification.show'))->assertOk()->assertSee('Verifikasi KTM');
    }

    public function test_matching_ktm_verifies_student_and_deletes_photo(): void
    {
        $this->fakeOcrResult(KtmOcr::MATCHED);
        $student = $this->newStudent();

        $this->uploadKtm($student)->assertRedirect(route('reports.index'));

        $student->refresh();
        $this->assertSame('verified', $student->verification_status);
        $this->assertNotNull($student->verified_at);
        $this->assertNull($student->ktm_path);
        $this->assertEmpty(Storage::disk('local')->allFiles('ktm'));

        $this->actingAs($student)->get(route('reports.create'))->assertOk();
    }

    public function test_failed_ocr_allows_retry_then_sends_to_admin_after_three_attempts(): void
    {
        $this->fakeOcrResult(KtmOcr::NOT_MATCHED);
        $student = $this->newStudent();
        $admin = User::where('role', 'admin')->first();

        $this->uploadKtm($student)->assertSessionHas('error');
        $this->uploadKtm($student)->assertSessionHas('error');
        $this->assertSame('unverified', $student->fresh()->verification_status);
        // Foto lama diganti, jadi hanya satu file yang tersimpan
        $this->assertCount(1, Storage::disk('local')->allFiles('ktm'));

        $this->uploadKtm($student)->assertSessionHas('success');

        $this->assertSame('pending', $student->fresh()->verification_status);
        $this->assertSame(1, $admin->notifications()->count());

        // Saat menunggu admin, tidak bisa upload lagi
        $this->actingAs($student)->get(route('verification.show'))->assertSee('sedang dicek admin');
        $this->uploadKtm($student)->assertForbidden();
    }

    public function test_student_can_send_ktm_to_admin_without_waiting(): void
    {
        $this->fakeOcrResult(KtmOcr::NOT_MATCHED);
        $student = $this->newStudent();

        // Belum upload: ditolak
        $this->actingAs($student)->post(route('verification.request-review'))->assertSessionHas('error');

        $this->uploadKtm($student);
        $this->actingAs($student)->post(route('verification.request-review'))->assertRedirect(route('verification.show'));

        $this->assertSame('pending', $student->fresh()->verification_status);
    }

    public function test_missing_tesseract_goes_straight_to_admin(): void
    {
        $this->fakeOcrResult(KtmOcr::UNAVAILABLE);
        $student = $this->newStudent();

        $this->uploadKtm($student)->assertSessionHas('success');

        $this->assertSame('pending', $student->fresh()->verification_status);
    }

    public function test_admin_can_approve_or_reject_and_only_admin_sees_photo(): void
    {
        $admin = User::where('role', 'admin')->first();
        Storage::disk('local')->put('ktm/a.png', 'foto');
        Storage::disk('local')->put('ktm/b.png', 'foto');
        $approved = $this->newStudent(['verification_status' => 'pending', 'ktm_path' => 'ktm/a.png', 'ktm_attempts' => 3]);
        $rejected = $this->newStudent(['nim' => '2702888888', 'verification_status' => 'pending', 'ktm_path' => 'ktm/b.png', 'ktm_attempts' => 3]);

        // Foto KTM private: mahasiswa lain dan petugas tidak bisa membukanya
        $this->actingAs($rejected)->get(route('admin.verifications.photo', $approved))->assertForbidden();
        $this->actingAs(User::where('role', 'staff')->first())->get(route('admin.verifications.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.verifications.photo', $approved))->assertOk();
        $this->actingAs($admin)->get(route('admin.verifications.index'))->assertOk()->assertSee($approved->nim);

        $this->actingAs($admin)->patch(route('admin.verifications.approve', $approved))->assertRedirect();
        $this->assertSame('verified', $approved->fresh()->verification_status);
        $this->assertFalse(Storage::disk('local')->exists('ktm/a.png'));
        $this->assertSame(1, $approved->notifications()->count());

        // Alasan penolakan wajib diisi
        $this->actingAs($admin)->patch(route('admin.verifications.reject', $rejected), ['rejection_reason' => ''])
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAs($admin)->patch(route('admin.verifications.reject', $rejected), [
            'rejection_reason' => 'NIM di KTM berbeda dengan NIM akun.',
        ])->assertRedirect();

        $rejected->refresh();
        $this->assertSame('rejected', $rejected->verification_status);
        $this->assertSame(0, $rejected->ktm_attempts);
        $this->assertNull($rejected->ktm_path);

        // Setelah ditolak, mahasiswa bisa upload ulang
        $this->fakeOcrResult(KtmOcr::MATCHED);
        $this->uploadKtm($rejected)->assertRedirect(route('reports.index'));
        $this->assertSame('verified', $rejected->fresh()->verification_status);
    }

    public function test_ocr_text_matching_tolerates_common_misreads(): void
    {
        $ocr = new KtmOcr();

        // Teks mirip hasil OCR dari KTM asli: spasi, garis miring, dan O/I yang salah baca
        $this->assertTrue($ocr->textContainsNim("WAHYU A. BARMAWI\nBN125468324 /28O2 4978OO\nBINUS UNIVERSITY", '2802497800'));
        $this->assertTrue($ocr->textContainsNim("B1NUS UNIVERS1TY\n27O2OOOOO2", '2702000002'));

        // NIM berbeda atau tidak ada kata BINUS: gagal
        $this->assertFalse($ocr->textContainsNim("BINUS UNIVERSITY\n2802497800", '2702000002'));
        $this->assertFalse($ocr->textContainsNim('KARTU PERPUSTAKAAN 2802497800', '2802497800'));
    }
}
