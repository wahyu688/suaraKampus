<?php

// Pengaturan khusus SuaraKampus. Dibaca dengan config('suarakampus.nama_kunci').
return [

    // Mahasiswa yang daftar sendiri wajib memakai email kampus ini
    'student_email_domain' => 'binus.ac.id',

    // Dosen/staf BINUS juga boleh daftar sendiri untuk melapor (tanpa NIM)
    'employee_email_domain' => 'binus.edu',

    // Akun petugas dibuat admin dan boleh memakai salah satu domain ini
    'staff_email_domains' => ['binus.ac.id', 'binus.edu'],

    // Verifikasi KTM: berapa kali OCR boleh dicoba sebelum otomatis dikirim ke admin
    'ktm_max_attempts' => 3,

    // Lokasi program Tesseract OCR. Kosongkan di .env kalau sudah ada di PATH.
    'tesseract_path' => env('TESSERACT_PATH', 'tesseract'),

];
