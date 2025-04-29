<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\BankApiController;

// Public routes
Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login'])->name('login');

// Protected routes (require JWT authentication)
Route::middleware('auth.jwt')->group(function () {
    // Logout route
    Route::post('logout', [ApiController::class, 'logout']);

    // Profile routes
    Route::put('update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('get-profile', [ProfileController::class, 'getProfile']);
});

// Bank API routes
Route::prefix('bank')->group(function () {
    Route::post('inquiry', [BankApiController::class, 'inquiry']);
});

// Wilayah (Region) routes
Route::prefix('wilayah')->group(function () {
    Route::post('provinsi', [WilayahController::class, 'provinsiIndex']);
    Route::post('kabupaten', [WilayahController::class, 'kabupatenIndex']);
    Route::post('kecamatan', [WilayahController::class, 'kecamatanIndex']);
    Route::post('kelurahan', [WilayahController::class, 'kelurahanIndex']);
    Route::post('kabupaten/all', [WilayahController::class, 'allKabupaten']);
});
