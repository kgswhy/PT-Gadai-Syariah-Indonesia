<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        // Get the authenticated user
        $user = $request->user(); // Retrieve the authenticated user

        // Find the profile associated with the user
        $profile = Profile::where('user_id', $user->id)->first();

        // If the profile does not exist, return a message
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        // Return the profile data
        return response()->json([
            'message' => 'Profile retrieved successfully',
            'profile' => $profile
        ], 200);
    }
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:profiles,nik,' . $request->id,
            'nama' => 'required|string|max:255',
            'tempatLahir' => 'required|string|max:255',
            'tanggalLahir' => 'required|date',
            'jenisKelamin' => 'required|in:L,P',
            'golDarah' => 'nullable|string|max:3',
            'alamat' => 'required|string|max:255',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'kel' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'agama' => 'required|string|max:255',
            'statusPekerjaan' => 'required|string|max:255',
            'statusPerkawinan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'kewarganegaraan' => 'required|string|max:255',
            'berlakuHingga' => 'required|date',
            'gambarKtp' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kodeBank' => 'nullable|string|max:255',
            'noRekening' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find existing profile or create a new one
        $profile = Profile::find($request->id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Handle the KTP image upload
        $imagePath = null;
        if ($request->hasFile('gambarKtp')) {
            $imagePath = $request->file('gambarKtp')->store('ktp_images', 'public');
        }

        // Update the profile
        $profile->update([
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
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'agama' => $request->agama,
            'statusPekerjaan' => $request->statusPekerjaan,
            'statusPerkawinan' => $request->statusPerkawinan,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'berlakuHingga' => $request->berlakuHingga,
            'gambarKtp' => $imagePath,
            'kodeBank' => $request->kodeBank,
            'noRekening' => $request->noRekening,
        ]);

        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile], 200);
    }
}
