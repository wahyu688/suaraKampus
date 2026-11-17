<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectVerificationRequest;
use App\Models\User;
use App\Notifications\ReportNotification;
use Illuminate\Support\Facades\Storage;

// Admin mengecek manual KTM yang tidak lolos OCR
class VerificationController extends Controller
{
    public function index()
    {
        $students = User::awaitingReview()->orderBy('updated_at')->paginate(10);

        return view('admin.verifications.index', ['students' => $students]);
    }

    // Foto KTM disimpan di disk private, jadi dikirim lewat route ini (hanya admin yang bisa membuka)
    public function photo(User $user)
    {
        abort_unless($user->ktm_path && Storage::disk('local')->exists($user->ktm_path), 404);

        return Storage::disk('local')->response($user->ktm_path);
    }

    public function approve(User $user)
    {
        abort_unless($user->verification_status === 'pending', 404);

        if ($user->ktm_path) {
            Storage::disk('local')->delete($user->ktm_path);
        }

        $user->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'ktm_path' => null,
        ]);

        $user->notify(new ReportNotification(
            'KTM terverifikasi',
            'Akunmu sudah diverifikasi admin. Sekarang kamu bisa membuat laporan.',
            route('reports.create', [], false),
        ));

        return redirect()->route('admin.verifications.index')
            ->with('success', 'KTM ' . $user->name . ' disetujui.');
    }

    public function reject(RejectVerificationRequest $request, User $user)
    {
        abort_unless($user->verification_status === 'pending', 404);

        if ($user->ktm_path) {
            Storage::disk('local')->delete($user->ktm_path);
        }

        // Percobaan direset supaya mahasiswa bisa mencoba OCR lagi dengan foto baru
        $user->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'ktm_path' => null,
            'ktm_attempts' => 0,
        ]);

        $user->notify(new ReportNotification(
            'Verifikasi KTM ditolak',
            $request->rejection_reason,
            route('verification.show', [], false),
        ));

        return redirect()->route('admin.verifications.index')
            ->with('success', 'KTM ' . $user->name . ' ditolak.');
    }
}
