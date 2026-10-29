<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Dijalankan sebelum validasi: email dijadikan huruf kecil supaya "Nama@BINUS.ac.id" tetap lolos
    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower((string) $this->email)]);
    }

    // Email mahasiswa (@binus.ac.id) wajib NIM; dosen/staf (@binus.edu) tidak
    public function isStudentEmail(): bool
    {
        return str_ends_with((string) $this->email, '@' . config('suarakampus.student_email_domain'));
    }

    public function rules(): array
    {
        $studentDomain = '@' . config('suarakampus.student_email_domain');
        $employeeDomain = '@' . config('suarakampus.employee_email_domain');

        return [
            'name' => ['required', 'string', 'max:100'],
            'nim' => $this->isStudentEmail()
                ? ['required', 'digits:10', 'unique:users,nim']
                : ['nullable'],
            'email' => [
                'required',
                'email',
                'max:255',
                'ends_with:' . $studentDomain . ',' . $employeeDomain,
                'unique:users,email',
            ],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'nim.required' => 'NIM wajib diisi untuk email mahasiswa.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => 'Format email tidak valid.',
            'email.ends_with' => 'Pakai email BINUS: @' . config('suarakampus.student_email_domain')
                . ' (mahasiswa) atau @' . config('suarakampus.employee_email_domain') . ' (dosen/staf).',
            'nim.digits' => 'NIM harus terdiri dari 10 angka.',
            'unique' => ':attribute sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama lengkap',
            'nim' => 'NIM',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
