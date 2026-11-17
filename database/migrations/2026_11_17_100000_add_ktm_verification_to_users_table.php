<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Kolom untuk verifikasi KTM mahasiswa.
    // Default "verified" supaya akun lama, dosen/staf, dan petugas tidak ikut terkunci.
    // Mahasiswa yang baru daftar diisi "unverified" oleh RegisterController.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
                ->default('verified')
                ->after('role');
            $table->string('ktm_path')->nullable()->after('verification_status');
            $table->unsignedTinyInteger('ktm_attempts')->default(0)->after('ktm_path');
            $table->string('rejection_reason')->nullable()->after('ktm_attempts');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'ktm_path', 'ktm_attempts', 'rejection_reason', 'verified_at']);
        });
    }
};
