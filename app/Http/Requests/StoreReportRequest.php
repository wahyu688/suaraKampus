<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'campus_id' => ['required', 'exists:campuses,id'],
            'title' => ['required', 'string', 'max:80'],
            'location_detail' => ['required', 'string', 'max:120'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:20', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'agreement' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'category_id.required' => 'Pilih salah satu kategori laporan.',
            'campus_id.required' => 'Pilih kampus tempat kejadian.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
            'description.min' => 'Deskripsi minimal :min karakter supaya petugas paham masalahnya.',
            'incident_date.date' => 'Tanggal kejadian tidak valid.',
            'incident_date.before_or_equal' => 'Tanggal kejadian tidak boleh melebihi hari ini.',
            'photos.max' => 'Foto bukti maksimal :max file.',
            'photos.*.image' => 'File yang diunggah harus berupa gambar.',
            'photos.*.mimes' => 'Foto harus berformat JPG atau PNG.',
            'photos.*.max' => 'Ukuran tiap foto maksimal 5 MB.',
            'agreement.accepted' => 'Centang pernyataan ini sebelum mengirim laporan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'Kategori',
            'campus_id' => 'Kampus',
            'title' => 'Judul laporan',
            'location_detail' => 'Gedung / ruang',
            'incident_date' => 'Tanggal kejadian',
            'description' => 'Deskripsi',
        ];
    }
}
