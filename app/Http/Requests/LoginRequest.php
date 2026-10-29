<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower((string) $this->email)]);
    }

    public function rules(): array
    {
        // Semua domain BINUS yang boleh login (mahasiswa + petugas), misalnya "@binus.ac.id,@binus.edu"
        $domains = collect(config('suarakampus.staff_email_domains'))
            ->push(config('suarakampus.student_email_domain'))
            ->unique()
            ->map(fn ($domain) => '@' . $domain)
            ->join(',');

        return [
            'email' => ['required', 'email', 'ends_with:' . $domains],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email' => 'Format email tidak valid.',
            'email.ends_with' => 'Gunakan email BINUS untuk masuk.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
