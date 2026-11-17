<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Tulis alasan penolakan supaya mahasiswa tahu harus memperbaiki apa.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 255 karakter.',
        ];
    }
}
