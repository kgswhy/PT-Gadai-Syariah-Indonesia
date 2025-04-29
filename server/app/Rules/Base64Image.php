<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Image implements Rule
{
    public function passes($attribute, $value)
    {
        // Periksa apakah string adalah Base64 yang valid
        if (!is_string($value) || !preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            return false;
        }

        $data = substr($value, strpos($value, ',') + 1);
        $data = base64_decode($data, true);

        if ($data === false) {
            return false;
        }

        // Periksa apakah data adalah gambar yang valid
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $data, FILEINFO_MIME_TYPE);

        return in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }

    public function message()
    {
        return 'The :attribute must be a valid base64 encoded image.';
    }
}
