<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Services\BankApiService;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    protected $bankApiService;

    public function __construct(BankApiService $bankApiService)
    {
        $this->bankApiService = $bankApiService;
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        $profile->fill([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempatLahir,
            'tanggal_lahir' => $request->tanggalLahir,
            'jenis_kelamin' => $request->jenisKelamin,
            'gol_darah' => $request->golDarah,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kel' => $request->kel,
            'desa' => $request->kel, // Sementara desa = kel
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'agama' => $request->agama,
            'status_pekerjaan' => $request->statusPekerjaan,
            'status_perkawinan' => $request->statusPerkawinan,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'berlaku_hingga' => $request->berlakuHingga,
            'kode_bank' => $request->kodeBank,
            'no_rekening' => $request->noRekening,
        ]);

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
            'data' => new ProfileResource($profile),
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
                    'profile' => new ProfileResource($profile),
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
