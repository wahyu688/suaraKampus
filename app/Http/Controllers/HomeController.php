<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $totalReports = Report::count();
        $resolvedReports = Report::where('status', 'resolved')->get();

        // Persentase laporan selesai; dicek dulu supaya tidak membagi dengan nol
        $resolvedPercent = $totalReports > 0
            ? round($resolvedReports->count() / $totalReports * 100)
            : 0;

        // Rata-rata lama penanganan (hari) dari laporan dibuat sampai selesai
        $averageDays = $resolvedReports->avg(
            fn ($report) => $report->created_at->diffInHours($report->resolved_at) / 24
        );

        // Kartu di hero menampilkan laporan terbaru milik pelapor yang sedang login
        $latestReport = null;
        if (Auth::check() && ! Auth::user()->isStaff()) {
            $latestReport = Auth::user()->reports()
                ->with(['category', 'campus'])
                ->latest()
                ->first();
        }

        return view('home.index', [
            'categories' => $categories,
            'totalReports' => $totalReports,
            'resolvedPercent' => $resolvedPercent,
            'averageDays' => $averageDays,
            'latestReport' => $latestReport,
        ]);
    }
}
