<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKtmRequest;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Services\KtmOcr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

// Mahasiswa memverifikasi akunnya dengan foto KTM.
// OCR mencoba membaca NIM; kalau gagal 3 kali (atau mahasiswa memilih), KTM dicek manual oleh admin.
class KtmVerificationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user->needsKtmVerification()) {
            return redirect($user->homeUrl());
        }

        return view('verification.show', ['user' => $user]);
    }

    // KtmOcr otomatis disiapkan Laravel lewat dependency injection
    public function store(StoreKtmRequest $request, KtmOcr $ocr)
    {
        $user = $request->user();

        // Hanya boleh upload saat belum verifikasi atau setelah ditolak admin
        abort_unless(in_array($user->verification_status, ['unverified', 'rejected']) && $user->role === 'student', 403);

        // Foto lama diganti foto baru. Disk "local" = storage/app/private, tidak bisa dibuka lewat URL.
        if ($user->ktm_path) {
            Storage::disk('local')->delete($user->ktm_path);
        }

        $user->update([
            'ktm_path' => $request->file('ktm')->store('ktm', 'local'),
            'ktm_attempts' => $user->ktm_attempts + 1,
        ]);

        $result = $ocr->check(Storage::disk('local')->path($user->ktm_path), $user->nim);

        if ($result === KtmOcr::MATCHED) {
            $this->markVerified($user);

            return redirect()->route('reports.index')
                ->with('success', 'KTM terverifikasi! Sekarang kamu bisa membuat laporan.');
        }

        if ($result === KtmOcr::UNAVAILABLE) {
            $this->sendToAdmin($user);

            return redirect()->route('verification.show')
                ->with('success', 'Pengecekan otomatis sedang tidak tersedia. KTM-mu sudah dikirim ke admin untuk dicek manual.');
        }

        if ($user->remainingKtmAttempts() === 0) {
            $this->sendToAdmin($user);

            return redirect()->route('verification.show')
                ->with('success', 'NIM belum terbaca setelah ' . $user->ktm_attempts . ' percobaan. KTM-mu dikirim ke admin untuk dicek manual.');
        }

        return redirect()->route('verification.show')
            ->with('error', 'NIM ' . $user->nim . ' tidak terbaca di foto KTM. Pastikan foto tegak, terang, dan tidak silau, lalu coba lagi.');
    }

    // Tombol "Kirim ke admin": tidak perlu menunggu 3 kali gagal
    public function requestReview(Request $request)
    {
        $user = $request->user();

        abort_unless(in_array($user->verification_status, ['unverified', 'rejected']) && $user->role === 'student', 403);

        if (! $user->ktm_path || ! Storage::disk('local')->exists($user->ktm_path)) {
            return back()->with('error', 'Upload foto KTM dulu sebelum mengirim ke admin.');
        }

        $this->sendToAdmin($user);

        return redirect()->route('verification.show')
            ->with('success', 'KTM-mu sudah dikirim ke admin. Kamu akan mendapat notifikasi setelah dicek.');
    }

    private function markVerified(User $user): void
    {
        // Foto KTM berisi data pribadi, jadi dihapus begitu tidak diperlukan lagi
        Storage::disk('local')->delete($user->ktm_path);

        $user->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'ktm_path' => null,
            'rejection_reason' => null,
        ]);
    }

    private function sendToAdmin(User $user): void
    {
        $user->update(['verification_status' => 'pending', 'rejection_reason' => null]);

        Notification::send(User::where('role', 'admin')->get(), new ReportNotification(
            'Verifikasi KTM baru',
            'Ada mahasiswa yang menunggu KTM-nya dicek.',
            route('admin.verifications.index', [], false),
        ));
    }
}
