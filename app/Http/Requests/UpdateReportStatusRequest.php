<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:in_progress,resolved'],
            'title' => ['required', 'string', 'max:80'],
            // Aturan bisnis: setiap perubahan status wajib disertai catatan
            'note' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'Status',
            'title' => 'Judul perkembangan',
            'note' => 'Catatan',
        ];
    }
}
