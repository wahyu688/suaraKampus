<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportRatingController extends Controller
{
    public function store(Request $request, Report $report)
    {
        // Hanya pemilik, status Selesai, dan belum pernah menilai
        Gate::authorize('rate', $report);

        $validated = $request->validate(
            [
                'rating' => ['required', 'integer', 'between:1,5'],
                'rating_comment' => ['nullable', 'string', 'max:500'],
            ],
            [
                'rating.required' => 'Pilih jumlah bintang dulu.',
                'rating.between' => 'Penilaian harus 1 sampai 5 bintang.',
                'rating_comment.max' => 'Komentar maksimal 500 karakter.',
            ],
        );

        $report->rating = $validated['rating'];
        $report->rating_comment = $validated['rating_comment'] ?? null;
        $report->save();

        return back()->with('success', 'Terima kasih! Penilaianmu sudah tersimpan.');
    }
}
