<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Dipakai untuk tambah (store) dan ubah (update) kategori
class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Saat mengubah, nama kategori itu sendiri tidak dianggap "sudah dipakai"
            'name' => ['required', 'string', 'max:60', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            'description' => ['required', 'string', 'max:200'],
            'icon' => ['required', Rule::in(array_keys(Category::ICONS))],
            'is_sensitive' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'name.unique' => 'Kategori dengan nama ini sudah ada.',
            'icon.in' => 'Pilih salah satu ikon yang tersedia.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama kategori',
            'description' => 'Deskripsi',
            'icon' => 'Ikon',
        ];
    }
}
