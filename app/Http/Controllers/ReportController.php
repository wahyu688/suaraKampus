<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    // Tab filter di Laporan Saya: nilai ?status= => label
    private const TABS = [
        '' => 'Semua',
        'pending' => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
    ];

    // Laporan Saya
    public function index(Request $request)
    {
        $user = $request->user();

        // Abaikan nilai ?status= yang tidak dikenal
        $status = $request->query('status');
        if (! array_key_exists($status, self::TABS)) {
            $status = null;
        }

        // Jumlah laporan di setiap tab
        $counts = [];
        foreach (self::TABS as $key => $label) {
            $counts[$key] = $user->reports()->status($key)->count();
        }

        $reports = $user->reports()
            ->with(['category', 'campus'])
            ->status($status)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        // Panel kanan (desktop): laporan dari ?laporan=, atau yang paling atas di daftar.
        // Dicari lewat $user->reports() supaya laporan orang lain tidak bisa dibuka.
        $selected = $request->filled('laporan')
            ? $user->reports()->where('code', $request->query('laporan'))->first()
            : $reports->first();

        $selected?->load(['category', 'campus', 'photos', 'updates', 'messages.user']);

        return view('reports.index', [
            'tabs' => self::TABS,
            'counts' => $counts,
            'status' => $status,
            'reports' => $reports,
            'selected' => $selected,
        ]);
    }

    // Form Buat Laporan
    public function create(Request $request)
    {
        // Dari Beranda: /laporan/buat?kategori=perundungan langsung memilih kategori itu
        $selectedCategory = Category::where('slug', $request->query('kategori'))->first();

        return view('reports.create', [
            'categories' => Category::all(),
            'campuses' => Campus::orderBy('name')->get(),
            'selectedCategory' => $selectedCategory,
        ]);
    }

    // Simpan laporan baru
    public function store(StoreReportRequest $request)
    {
        // Lewat relasi, jadi user_id otomatis milik user yang login.
        // Kode BN-XXXX dibuat oleh event "creating" di model Report.
        $report = $request->user()->reports()->create([
            'category_id' => $request->category_id,
            'campus_id' => $request->campus_id,
            'title' => $request->title,
            'location_detail' => $request->location_detail,
            'incident_date' => $request->incident_date,
            'description' => $request->description,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        // Riwayat pertama di timeline
        $report->updates()->create([
            'status' => 'pending',
            'title' => 'Laporan terkirim',
            'note' => 'Laporan diterima dan menunggu verifikasi admin.',
        ]);

        // Simpan foto ke storage/app/public/reports, path-nya dicatat di report_photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $report->photos()->create([
                    'path' => $photo->store('reports', 'public'),
                ]);
            }
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan terkirim! Nomor laporanmu #' . $report->code . '.');
    }

    // Detail laporan (dipakai di mobile dan sebagai link langsung)
    public function show(Report $report)
    {
        // 403 kalau bukan pemilik (aturan di ReportPolicy@view)
        Gate::authorize('view', $report);

        $report->load(['category', 'campus', 'photos', 'updates', 'messages.user']);

        return view('reports.show', ['report' => $report]);
    }

    // Batalkan laporan yang masih Menunggu
    public function cancel(Report $report)
    {
        Gate::authorize('cancel', $report);

        // status tidak ada di $fillable, jadi diisi langsung lalu save()
        $report->status = 'cancelled';
        $report->save();

        $report->updates()->create([
            'status' => 'cancelled',
            'title' => 'Laporan dibatalkan',
            'note' => 'Dibatalkan oleh pelapor.',
        ]);

        return back()->with('success', 'Laporan #' . $report->code . ' sudah dibatalkan.');
    }
}
