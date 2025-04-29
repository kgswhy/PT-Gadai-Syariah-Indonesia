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

            // Validasi format base64 gambar
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $type = strtolower($type[1]); // jpg, png, gif, etc.

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return response()->json(['status' => 'error', 'message' => 'Unsupported image format.'], 400);
                }

                // Opsional: validasi apakah base64 bisa didecode (bukan disimpan, hanya cek validitas)
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                if (base64_decode($data, true) === false) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid base64 encoding.'], 400);
                }

                // Simpan base64-nya ke database
                $profile->gambarKtp = $base64Image;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid base64 image format.'], 400);
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
