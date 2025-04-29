<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                $errorMessages = implode(', ', $validator->errors()->all());

                return response()->json([
                    'status' => false,
                    'message' => 'Validation error: ' . $errorMessages,
                ], 422);
            }

            // Membuat user baru
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Membuat profil untuk user
            Profile::create([
                'user_id' => $user->id,
                'nik' => $request->nik ?? null,
                'nama' => $request->username,
            ]);

            // Membuat token JWT
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'token' => $token,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred during registration: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');
            Log::info('Login attempt with credentials: ', $credentials);

            // Validasi kredensial
            $validator = Validator::make($credentials, [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                $errorMessages = implode(', ', $validator->errors()->all());

                return response()->json([
                    'status' => false,
                    'message' => 'Validation error: ' . $errorMessages,
                ], 422);
            }

            // Set expiration token manual
            $expiration = Carbon::now()->addMinutes(60)->timestamp;

            $token = JWTAuth::attempt($credentials, ['exp' => $expiration]);

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid username or password',
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Login error: ', ['error' => $th->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Error occurred during login: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout: ' . $th->getMessage(),
            ], 500);
        }
    }
}
