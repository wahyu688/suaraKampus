<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Database\Seeders\DemoPaginationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_factory_creates_reports_with_code_and_first_timeline_entry(): void
    {
        $report = Report::factory()->for(User::where('email', 'mahasiswa@binus.ac.id')->first())->create();

        $this->assertMatchesRegularExpression('/^BN-\d{4}$/', $report->code);
        $this->assertCount(1, $report->updates);
        $this->assertFalse($report->category->is_sensitive);
    }

    public function test_demo_seeder_makes_report_lists_paginate(): void
    {
        $this->seed(DemoPaginationSeeder::class);

        $student = User::where('email', 'mahasiswa@binus.ac.id')->first();
        $this->assertSame(30, $student->reports()->count());
        $this->assertSame(30, Report::distinct()->count('code'));

        // 5 per halaman di Laporan Saya, jadi ada halaman 2 dan halaman 6
        $this->actingAs($student)->get(route('reports.index'))
            ->assertSee('page=2', false)
            ->assertSee('page=6', false);

        // Halaman 2 bisa dibuka dan tetap membawa filter status
        $this->actingAs($student)->get(route('reports.index', ['status' => 'pending', 'page' => 2]))
            ->assertOk();

        // 10 per halaman di Laporan Masuk petugas
        $this->actingAs(User::where('email', 'petugas@binus.edu')->first())
            ->get(route('staff.reports.index'))
            ->assertSee('page=2', false);
    }
}
