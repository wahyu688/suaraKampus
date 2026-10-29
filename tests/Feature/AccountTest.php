<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Menguji aturan akun: email BINUS dan akun petugas yang dibuat admin
class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function registerData(string $email): array
    {
        return [
            'name' => 'Andi Pratama',
            'nim' => '2702111111',
            'email' => $email,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ];
    }

    public function test_students_must_register_with_binus_student_email(): void
    {
        $this->post(route('register'), $this->registerData('andi@gmail.com'))->assertSessionHasErrors('email');

        // Email mahasiswa wajib NIM
        $this->post(route('register'), array_merge($this->registerData('andi@binus.ac.id'), ['nim' => '']))
            ->assertSessionHasErrors('nim');

        // Huruf besar tetap diterima karena email diubah ke huruf kecil sebelum divalidasi
        $this->post(route('register'), $this->registerData('Andi.Pratama@BINUS.ac.id'))->assertRedirect(route('reports.index'));

        $this->assertDatabaseHas('users', ['email' => 'andi.pratama@binus.ac.id', 'role' => 'student', 'nim' => '2702111111']);
    }

    public function test_lecturers_register_with_binus_edu_without_nim_and_can_only_report(): void
    {
        $this->post(route('register'), array_merge($this->registerData('rina.dosen@binus.edu'), ['nim' => '']))
            ->assertRedirect(route('reports.index'));

        $lecturer = User::where('email', 'rina.dosen@binus.edu')->first();
        $this->assertSame('employee', $lecturer->role);
        $this->assertNull($lecturer->nim);

        // Bisa membuat laporan dan melihat laporannya sendiri
        $this->actingAs($lecturer)->get(route('reports.create'))->assertOk();
        $this->actingAs($lecturer)->post(route('reports.store'), [
            'category_id' => 2,
            'campus_id' => 1,
            'title' => 'Proyektor ruang dosen mati',
            'location_detail' => 'Ruang dosen lantai 5',
            'incident_date' => now()->toDateString(),
            'description' => 'Proyektor di ruang dosen lantai 5 tidak menyala sejak pagi.',
            'agreement' => '1',
        ])->assertRedirect();
        $this->assertSame(1, $lecturer->reports()->count());

        // Tapi tidak bisa membuka dashboard petugas atau laporan orang lain
        $this->actingAs($lecturer)->get(route('staff.reports.index'))->assertForbidden();
        $this->actingAs($lecturer)->get(route('reports.show', 'BN-2481'))->assertForbidden();
    }

    public function test_login_requires_binus_email(): void
    {
        $this->post(route('login'), ['email' => 'mahasiswa@gmail.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('login'), ['email' => 'mahasiswa@binus.ac.id', 'password' => 'password'])
            ->assertRedirect(route('reports.index'));
        $this->assertAuthenticated();
    }

    public function test_only_admin_can_manage_staff(): void
    {
        $this->actingAs(User::where('email', 'petugas@binus.edu')->first())
            ->get(route('admin.staff.index'))->assertForbidden();

        $this->actingAs(User::where('email', 'mahasiswa@binus.ac.id')->first())
            ->get(route('admin.staff.index'))->assertForbidden();

        $this->actingAs(User::where('email', 'admin@binus.edu')->first())
            ->get(route('admin.staff.index'))->assertOk()->assertSee('Building Management');
    }

    public function test_admin_creates_staff_with_binus_email_and_temporary_password(): void
    {
        $admin = User::where('email', 'admin@binus.edu')->first();

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), ['name' => 'Petugas Luar', 'email' => 'petugas@gmail.com', 'role' => 'staff'])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), ['name' => 'BM Kampus Kijang', 'email' => 'bm.kijang@binus.edu', 'role' => 'staff'])
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('temporary_password');

        $staff = User::where('email', 'bm.kijang@binus.edu')->first();
        $this->assertSame('staff', $staff->role);
        $this->assertTrue($staff->must_change_password);
    }

    public function test_staff_pages_have_no_create_report_links(): void
    {
        $admin = User::where('email', 'admin@binus.edu')->first();
        $createUrl = route('reports.create');

        $this->actingAs($admin)->get(route('home'))->assertOk()->assertDontSee($createUrl, false);
        $this->actingAs($admin)->get(route('help.index'))->assertOk()->assertDontSee($createUrl, false);

        // Mahasiswa tetap melihat link kategori ke form
        $this->actingAs(User::where('email', 'mahasiswa@binus.ac.id')->first())
            ->get(route('home'))->assertSee($createUrl . '?kategori=perundungan', false);
    }

    public function test_forbidden_page_explains_and_links_back(): void
    {
        $this->actingAs(User::where('email', 'admin@binus.edu')->first())
            ->get(route('reports.create'))
            ->assertForbidden()
            ->assertSee('Halaman ini khusus pelapor')
            ->assertSee(route('staff.reports.index'), false);
    }

    public function test_new_staff_must_change_temporary_password_first(): void
    {
        $admin = User::where('email', 'admin@binus.edu')->first();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'BM Kampus Kijang', 'email' => 'bm.kijang@binus.edu', 'role' => 'staff',
        ]);
        $temporaryPassword = $response->getSession()->get('temporary_password');
        $staff = User::where('email', 'bm.kijang@binus.edu')->first();

        // Sebelum ganti password, halaman apa pun dialihkan ke halaman ubah password
        $this->actingAs($staff)->get(route('staff.reports.index'))->assertRedirect(route('password.edit'));
        $this->actingAs($staff)->get(route('home'))->assertRedirect(route('password.edit'));

        $this->actingAs($staff)->put(route('password.update'), [
            'current_password' => 'salah-banget',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($staff)->put(route('password.update'), [
            'current_password' => $temporaryPassword,
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ])->assertRedirect(route('staff.reports.index'));

        $this->assertFalse($staff->fresh()->must_change_password);
        $this->actingAs($staff->fresh())->get(route('staff.reports.index'))->assertOk();
    }
}
