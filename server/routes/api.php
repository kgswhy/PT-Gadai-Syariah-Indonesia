<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\ProfileController;

Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);

Route::group([
    "middleware" => ["auth:sanctum"]
], function () {
    //profile page
    Route::get('profile', [ApiController::class, 'profile']);
    //logout
    Route::get('logout', [ApiController::class, 'logout']);
    Route::middleware('auth:api')->put('/update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('profile', [ProfileController::class, 'getProfile']);  // New route to get profile


});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
