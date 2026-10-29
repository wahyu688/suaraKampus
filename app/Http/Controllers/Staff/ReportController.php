<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Report;
use App\Notifications\ReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    private const TABS = [
        '' => 'Semua',
        'pending' => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    // Daftar laporan masuk sesuai peran petugas
    public function index(Request $request)
    {
        $user = $request->user();

        $status = $request->query('status');
        if (! array_key_exists($status, self::TABS)) {
            $status = null;
        }

        $counts = [];
        foreach (self::TABS as $key => $label) {
            $counts[$key] = Report::visibleTo($user)->status($key)->count();
        }

        // visibleTo() menyaring kategori sensitif langsung di query
        $reports = Report::visibleTo($user)
            ->with(['category', 'campus', 'user'])
            ->status($status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.reports.index', [
            'tabs' => self::TABS,
            'counts' => $counts,
            'status' => $status,
            'reports' => $reports,
        ]);
    }

    public function show(Report $report)
    {
        // Staff yang membuka laporan sensitif lewat URL tetap dapat 403
        Gate::authorize('handle', $report);

        $report->load(['category', 'campus', 'user', 'photos', 'updates.user', 'messages.user']);

        return view('staff.reports.show', ['report' => $report]);
    }

    public function updateStatus(UpdateReportStatusRequest $request, Report $report)
    {
        Gate::authorize('updateStatus', $report);

        $report->status = $request->status;
        if ($request->status === 'resolved') {
            $report->resolved_at = now();
        }
        $report->save();

        // Aturan bisnis: setiap perubahan status tercatat di timeline
        $report->updates()->create([
            'user_id' => $request->user()->id,
            'status' => $request->status,
            'title' => $request->title,
            'note' => $request->note,
        ]);

        // Kabari pelapor
        $report->user->notify(new ReportNotification(
            $report->status === 'resolved'
                ? 'Laporan #' . $report->code . ' selesai'
                : 'Laporan #' . $report->code . ' sedang diproses',
            $report->status === 'resolved'
                ? 'Beri penilaian untuk penanganan laporanmu.'
                : $request->title . '.',
            route('reports.show', $report, false),
        ));

        return back()->with('success', 'Status laporan #' . $report->code . ' diperbarui menjadi ' . $report->status_label . '.');
    }
}
