<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class WilayahService
{
    /**
     * Load dan decode file JSON dari Storage
     *
     * @param string $path
     * @return array|null
     */
    public function loadJson(string $path): ?array
    {
        if (!Storage::exists($path)) {
            return null;
        }

        $json = Storage::get($path);
        return json_decode($json, true);
    }

    /**
     * Load semua file JSON dari folder tertentu dan gabungkan isinya
     *
     * @param string $directory
     * @return array
     */
    public function loadAllJsonFromDirectory(string $directory): array
    {
        $files = Storage::files($directory);
        $allData = [];

        foreach ($files as $file) {
            $json = Storage::get($file);
            $data = json_decode($json, true);

            if (is_array($data)) {
                $allData = array_merge($allData, $data);
            }
        }

        return $allData;
    }
}
