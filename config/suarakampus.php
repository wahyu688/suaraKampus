<?php

// Pengaturan khusus SuaraKampus. Dibaca dengan config('suarakampus.nama_kunci').
return [

    // Mahasiswa yang daftar sendiri wajib memakai email kampus ini
    'student_email_domain' => 'binus.ac.id',

    // Dosen/staf BINUS juga boleh daftar sendiri untuk melapor (tanpa NIM)
    'employee_email_domain' => 'binus.edu',

    // Akun petugas dibuat admin dan boleh memakai salah satu domain ini
    'staff_email_domains' => ['binus.ac.id', 'binus.edu'],

];
