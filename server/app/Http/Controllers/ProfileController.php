<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Validator;
use app\Rules\Base64Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\BankApiService;

class ProfileController extends Controller
{
    protected $bankApiService;

    public function __construct(BankApiService $bankApiService)
    {
        $this->bankApiService = $bankApiService;
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:profiles,nik,' . $user->id . ',user_id',
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
            'gambarKtp' => 'nullable|string',
            'kodeBank' => 'nullable|string|max:10',
            'noRekening' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        $profile->fill([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tempatLahir' => $request->tempatLahir,
            'tanggalLahir' => $request->tanggalLahir,
            'jenisKelamin' => $request->jenisKelamin,
            'golDarah' => $request->golDarah,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kel' => $request->kel,
            'desa' => $request->kel,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'agama' => $request->agama,
            'statusPekerjaan' => $request->statusPekerjaan,
            'statusPerkawinan' => $request->statusPerkawinan,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'berlakuHingga' => $request->berlakuHingga,
            'kodeBank' => $request->kodeBank,
            'noRekening' => $request->noRekening,
        ]);


        if ($request->filled('gambarKtp')) {
            $base64Image = $request->gambarKtp;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif, etc.

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return response()->json(['status' => 'error', 'message' => 'Format gambar tidak didukung.'], 400);
                }

                $data = base64_decode($data);
                if ($data === false) {
                    return response()->json(['status' => 'error', 'message' => 'Base64 decoding gagal.'], 400);
                }

                $fileName = 'ktp_' . $user->id . '_' . time() . '.' . $type;
                $filePath = 'public/ktp/' . $fileName;
                Storage::put($filePath, $data);
                $profile->gambarKtp = Storage::url($filePath);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Format gambar base64 tidak valid.'], 400);
            }
        }


        // Inquiry ke bank jika kodeBank dan noRekening terisi
        if ($request->filled('kodeBank') && $request->filled('noRekening')) {
            $inquiry = $this->bankApiService->inquiryAccount($request->kodeBank, $request->noRekening);

            if ($inquiry && isset($inquiry['account_name'])) {
                $profile->account_name = $inquiry['account_name'];
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengambil nama rekening dari bank.',
                ], 400);
            }
        }

        $profile->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

    public function getProfile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $profile = Profile::where('user_id', $user->id)->first();

            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile not found.',
                ], 404);
            }

            // Periksa apakah gambarKtp memiliki path
            if ($profile->gambarKtp) {
                // Dapatkan path relatif dari URL
                $relativePath = str_replace('/storage/', '', $profile->gambarKtp);
                $fullPath = storage_path('app/public/' . $relativePath);

                // Periksa apakah file ada
                if (file_exists($fullPath)) {
                    // Dapatkan tipe MIME file
                    $mimeType = mime_content_type($fullPath);
                    // Baca konten file
                    $imageData = file_get_contents($fullPath);
                    // Encode ke base64 dan setel ke atribut gambarKtp
                    $profile->gambarKtp = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                } else {
                    $profile->gambarKtp = null;
                }
            } else {
                $profile->gambarKtp = null;
            }

            return response()->json([
                'status' => true,
                'message' => 'Profile info retrieved successfully.',
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Token invalid or expired.',
                'error' => $th->getMessage(),
            ], 401);
        }
    }


}
