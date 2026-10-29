<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
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
        // Contoh hasil: "@binus.ac.id,@binus.edu"
        $domains = collect(config('suarakampus.staff_email_domains'))
            ->map(fn ($domain) => '@' . $domain)
            ->join(',');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'ends_with:' . $domains, 'unique:users,email'],
            'role' => ['required', 'in:staff,counselor,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => 'Format email tidak valid.',
            'email.ends_with' => 'Pakai email BINUS (' . implode(' atau ', array_map(fn ($d) => '@' . $d, config('suarakampus.staff_email_domains'))) . ').',
            'email.unique' => 'Email ini sudah punya akun.',
            'role.in' => 'Peran yang dipilih tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'role' => 'Peran',
        ];
    }
}
