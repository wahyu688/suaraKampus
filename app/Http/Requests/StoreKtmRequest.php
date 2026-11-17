<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKtmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ktm' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ktm.required' => 'Pilih atau ambil foto KTM dulu.',
            'ktm.image' => 'File harus berupa gambar.',
            'ktm.mimes' => 'Foto KTM harus berformat JPG atau PNG.',
            'ktm.max' => 'Ukuran foto KTM maksimal 5 MB.',
        ];
    }
}
