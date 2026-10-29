<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportMessageController extends Controller
{
    public function store(Request $request, Report $report)
    {
        Gate::authorize('handle', $report);

        $validated = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            [
                'body.required' => 'Balasan tidak boleh kosong.',
                'body.max' => 'Balasan maksimal 1000 karakter.',
            ],
        );

        $report->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Balasan terkirim ke pelapor.');
    }
}
