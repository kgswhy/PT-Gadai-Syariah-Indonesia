<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Bolehkan semua user yang terautentikasi
    }

    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'nik' => 'required|unique:profiles,nik,' . $userId . ',user_id',
            'nama' => 'required|string|max:255',
            'tempatLahir' => 'required|string|max:255',
            'tanggalLahir' => 'required|date',
            'jenisKelamin' => 'required|in:L,P',
            'golDarah' => 'nullable|string|max:3',
            'alamat' => 'required|string|max:255',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'kel' => 'required|string|max:255',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'agama' => 'required|string|max:255',
            'statusPekerjaan' => 'nullable|string|max:255',
            'statusPerkawinan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'kewarganegaraan' => 'required|string|max:255',
            'berlakuHingga' => 'nullable|date',
            'gambarKtp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kodeBank' => 'nullable|string|max:10',
            'noRekening' => 'nullable|string|max:50',
        ];
    }
}
