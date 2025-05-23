<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'tempatLahir',
        'tanggalLahir',
        'jenisKelamin',
        'golDarah',
        'alamat',
        'rt',
        'rw',
        'kel',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'agama',
        'statusPekerjaan',
        'statusPerkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'berlakuHingga',
        'gambarKtp',
        'kodeBank',
        'noRekening',
        'account_name'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}