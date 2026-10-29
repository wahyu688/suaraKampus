<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportMessageController extends Controller
{
    public function store(Request $request, Report $report)
    {
        Gate::authorize('message', $report);

        // Form-nya cuma satu kolom, jadi cukup validasi langsung di controller
        $validated = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            [
                'body.required' => 'Pesan tidak boleh kosong.',
                'body.max' => 'Pesan maksimal 1000 karakter.',
            ],
        );

        $report->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Pesanmu sudah terkirim ke petugas.');
    }
}
