<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopupTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_submitting_a_report_shows_the_submitted_popup_once(): void
    {
        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();

        $this->actingAs($student)->post(route('reports.store'), [
            'category_id' => 2,
            'campus_id' => 1,
            'title' => 'Proyektor lab 601 berkedip',
            'location_detail' => 'Lab 601',
            'incident_date' => now()->toDateString(),
            'description' => 'Proyektor di lab 601 berkedip terus sejak kemarin siang.',
            'agreement' => '1',
        ])->assertRedirect(route('reports.show', 'BN-2482'))
          ->assertSessionHas('report_submitted', 'BN-2482');

        // Request berikutnya: popup tampil dengan nomor laporan
        $this->actingAs($student)->get(route('reports.show', 'BN-2482'))
            ->assertSee('Laporan terkirim!')
            ->assertSee('#BN-2482');

        // Dibuka lagi: flash sudah habis, popup tidak muncul lagi
        $this->actingAs($student)->get(route('reports.show', 'BN-2482'))
            ->assertDontSee('submitted-modal');
    }

    public function test_flash_messages_render_as_toasts(): void
    {
        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();

        $this->actingAs($student)
            ->withSession(['success' => 'Pesanmu sudah terkirim ke petugas.'])
            ->get(route('reports.index'))
            ->assertSee('sk-toast', false)
            ->assertSee('role="status"', false)
            ->assertSee('Pesanmu sudah terkirim ke petugas.');

        $this->actingAs($student)
            ->withSession(['error' => 'Kategori tidak bisa dihapus.'])
            ->get(route('reports.index'))
            ->assertSee('role="alert"', false)
            ->assertSee('data-bs-autohide="false"', false);
    }
}
