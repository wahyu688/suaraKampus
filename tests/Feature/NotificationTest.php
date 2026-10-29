<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function user(string $email): User
    {
        return User::where('email', $email)->first();
    }

    private function newReport(array $override = []): array
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

    public function test_new_report_notifies_only_the_right_handlers(): void
    {
        $student = $this->user('mahasiswa@binus.ac.id');

        // Kategori umum (Fasilitas Rusak): staff dan admin dapat, konselor tidak
        $this->actingAs($student)->post(route('reports.store'), $this->newReport());
        $this->assertSame('Laporan baru #BN-2482', $this->user('petugas@binus.edu')->notifications()->first()->data['title']);
        $this->assertSame(1, $this->user('admin@binus.edu')->notifications()->count());
        $this->assertSame(0, $this->user('konselor@binus.edu')->notifications()->count());

        // Kategori sensitif (Perundungan, id 4): konselor dan admin dapat, staff tidak
        $staffBefore = $this->user('keamanan@binus.edu')->notifications()->count();
        $this->actingAs($student)->post(route('reports.store'), $this->newReport(['category_id' => 4]));
        $this->assertSame(1, $this->user('konselor@binus.edu')->notifications()->count());
        $this->assertSame($staffBefore, $this->user('keamanan@binus.edu')->notifications()->count());
    }

    public function test_reporter_is_notified_when_status_changes_and_officer_replies(): void
    {
        $student = $this->user('mahasiswa@binus.ac.id');
        $staff = $this->user('petugas@binus.edu');
        $before = $student->unreadNotifications()->count();

        $this->actingAs($staff)->patch(route('staff.reports.status', 'BN-2479'), [
            'status' => 'in_progress',
            'title' => 'Diverifikasi & diteruskan',
            'note' => 'Diteruskan ke cleaning service.',
        ]);
        $this->actingAs($staff)->post(route('staff.reports.messages.store', 'BN-2479'), ['body' => 'Sedang kami tangani.']);

        $this->assertSame($before + 2, $student->unreadNotifications()->count());
        $latest = $student->notifications()->take(2)->get()->pluck('data.title');
        $this->assertContains('Laporan #BN-2479 sedang diproses', $latest);
        $this->assertContains('Laporan #BN-2479 dibalas', $latest);
        $this->assertSame('/laporan/BN-2479', $student->notifications()->first()->data['url']);
    }

    public function test_opening_a_notification_marks_it_read_and_redirects(): void
    {
        $student = $this->user('mahasiswa@binus.ac.id');
        $notification = $student->unreadNotifications()->first();

        $this->actingAs($student)->get(route('notifications.open', $notification->id))
            ->assertRedirect($notification->data['url']);
        $this->assertNotNull($notification->fresh()->read_at);

        // Notifikasi milik orang lain tidak bisa dibuka
        $staffNotification = $this->user('petugas@binus.edu')->notifications()->first();
        $this->actingAs($student)->get(route('notifications.open', $staffNotification->id))->assertNotFound();
    }

    public function test_mark_all_as_read_and_bell_dot(): void
    {
        $student = $this->user('mahasiswa@binus.ac.id');

        $this->actingAs($student)->get(route('home'))->assertSee('Notifikasi, 2 belum dibaca');

        $this->actingAs($student)->post(route('notifications.read-all'))->assertRedirect();
        $this->assertSame(0, $student->unreadNotifications()->count());

        $this->actingAs($student)->get(route('home'))->assertSee('Notifikasi, 0 belum dibaca');
        $this->actingAs($student)->get(route('notifications.index'))->assertOk()->assertSee('Laporan #BN-2481 dibalas');
    }
}
