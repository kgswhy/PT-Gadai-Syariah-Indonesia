<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WilayahService;

class WilayahController extends Controller
{
    private $wilayahService;
    private $provinsiPath = 'provinsi.json';
    private $kabupatenPath = 'kabupaten/';
    private $kecamatanPath = 'kecamatan/';
    private $kelurahanPath = 'kelurahan/';

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function provinsiIndex()
    {
        $data = $this->wilayahService->loadJson($this->provinsiPath);

        if ($data) {
            return response()->json(['status' => true, 'data' => $data]);
        }

        return response()->json(['status' => false, 'message' => 'Provinsi data not found.'], 404);
    }

    public function kabupatenIndex(Request $request)
    {
        $request->validate(['id' => 'required|string']);
        $path = $this->kabupatenPath . $request->id . '.json';

        $data = $this->wilayahService->loadJson($path);

        if ($data) {
            return response()->json(['status' => true, 'data' => $data]);
        }

        return response()->json(['status' => false, 'message' => 'Kabupaten data not found for the given ID.'], 404);
    }

    public function kecamatanIndex(Request $request)
    {
        $request->validate(['id' => 'required|string']);
        $path = $this->kecamatanPath . $request->id . '.json';

        $data = $this->wilayahService->loadJson($path);

        if ($data) {
            return response()->json(['status' => true, 'data' => $data]);
        }

        return response()->json(['status' => false, 'message' => 'Kecamatan data not found for the given ID.'], 404);
    }

    public function kelurahanIndex(Request $request)
    {
        $request->validate(['id' => 'required|string']);
        $path = $this->kelurahanPath . $request->id . '.json';

        $data = $this->wilayahService->loadJson($path);

        if ($data) {
            return response()->json(['status' => true, 'data' => $data]);
        }

        return response()->json(['status' => false, 'message' => 'Kelurahan data not found for the given ID.'], 404);
    }

    public function allKabupaten()
    {
        $data = $this->wilayahService->loadAllJsonFromDirectory($this->kabupatenPath);

        if (!empty($data)) {
            return response()->json(['status' => true, 'data' => $data]);
        }

        return response()->json(['status' => false, 'message' => 'No kabupaten data found.'], 404);
    }
}
